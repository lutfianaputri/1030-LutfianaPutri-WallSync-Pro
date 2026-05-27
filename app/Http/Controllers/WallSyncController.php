<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WallSyncController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        return view('wallsync', compact('categories'));
    }

    public function checkAuth(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'logged_in' => Auth::check(),
            'name' => $user ? $user->name : null,
            'balance' => $user ? $user->balance : 0
        ]);
    }

    public function setup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_num'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $user = User::where('name', $validated['username'])->first();

        if ($user) {
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json(['success' => false, 'message' => 'Password salah.'], 401);
            }
        } else {
            $user = User::create([
                'name'     => $validated['username'],
                'password' => Hash::make($validated['password']),
                'balance'  => 0,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Selamat datang, ' . $user->name . '!',
            'name'    => $user->name,
            'balance' => $user->balance,
        ]);
    }

    public function spend(Request $request): JsonResponse
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'source' => ['required', 'string', 'in:mandiri,shopeepay,bank jago,gopay'],
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'integer', 'min:1000'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if ($user->balance < $validated['amount']) {
            return response()->json(['success' => false, 'message' => 'Saldo dompet Anda tidak mencukupi!'], 422);
        }

        DB::beginTransaction();
        try {
            $user->balance -= $validated['amount'];
            $user->save();

            Expense::create([
                'user_id'     => $user->id,
                'category_id' => $validated['category_id'],
                'source'      => $validated['source'],
                'description' => $validated['description'],
                'amount'      => $validated['amount'],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil dicatat!', 'new_balance' => $user->balance]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mencatat pengeluaran.'], 500);
        }
    }

    public function income(Request $request): JsonResponse
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        $validated = $request->validate([
            'source'      => ['required', 'string', 'in:mandiri,shopeepay,bank jago,gopay'],
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'integer', 'min:5000'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $user->balance += $validated['amount'];
            $user->save();

            Income::create([
                'user_id'     => $user->id,
                'source'      => $validated['source'],
                'description' => $validated['description'],
                'amount'      => $validated['amount'],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pemasukan berhasil ditambah!', 'new_balance' => $user->balance]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses pemasukan.'], 500);
        }
    }

    public function historyData(Request $request): JsonResponse
    {
        if (!Auth::check()) return response()->json([], 401);

        $user = Auth::user();

        $expenseQuery = Expense::with('category')->where('user_id', $user->id);
        if ($request->filled('month')) {
            $expenseQuery->whereMonth('created_at', '=', substr($request->month, 5, 2))
                         ->whereYear('created_at', '=', substr($request->month, 0, 4));
        }
        $expenses = $expenseQuery->get()->map(function($e) {
            return [
                'id'          => $e->id,
                'type'        => 'expense',
                'label'       => $e->category->name . ' (' . $e->source . ')',
                'color'       => $e->category->color,
                'description' => $e->description,
                'amount'      => $e->amount,
                'created_at'  => $e->created_at->format('Y-m-d H:i'),
                'timestamp'   => $e->created_at->timestamp,
            ];
        });

        $incomeQuery = Income::where('user_id', $user->id);
        if ($request->filled('month')) {
            $incomeQuery->whereMonth('created_at', '=', substr($request->month, 5, 2))
                        ->whereYear('created_at', '=', substr($request->month, 0, 4));
        }
        $incomes = $incomeQuery->get()->map(function($i) {
            return [
                'id'          => $i->id,
                'type'        => 'income',
                'label'       => 'Pemasukan (' . $i->source . ')',
                'color'       => '#10b981',
                'description' => $i->description,
                'amount'      => $i->amount,
                'created_at'  => $i->created_at->format('Y-m-d H:i'),
                'timestamp'   => $i->created_at->timestamp,
            ];
        });

        $merged = $expenses->concat($incomes)->sortByDesc('timestamp')->values();

        return response()->json($merged);
    }

    public function deleteTransaction(Request $request, $id): JsonResponse
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        /** @var User $user */
        $user = Auth::user();
        $type = $request->query('type');

        DB::beginTransaction();
        try {
            if ($type === 'income') {
                $income = Income::where('id', $id)->where('user_id', $user->id)->first();
                if (!$income) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

                if ($user->balance < $income->amount) {
                    return response()->json(['success' => false, 'message' => 'Gagal! Saldo tidak mencukupi untuk membatalkan pemasukan ini.'], 422);
                }
                $user->balance -= $income->amount;
                $user->save();
                $income->delete();
            } else {
                $expense = Expense::where('id', $id)->where('user_id', $user->id)->first();
                if (!$expense) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

                $user->balance += $expense->amount;
                $user->save();
                $expense->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Catatan mutasi berhasil dihapus.', 'new_balance' => $user->balance]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus transaksi.'], 500);
        }
    }

    public function chartCategory(): JsonResponse
    {
        if (!Auth::check()) return response()->json([], 401);

        $user = Auth::user();
        $rows = Expense::where('user_id', $user->id)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return response()->json($rows->map(function($r) {
            return [
                'label' => $r->category->name,
                'total' => (int) $r->total,
                'color' => $r->category->color
            ];
        }));
    }

    public function chartCashflow(Request $request): JsonResponse
    {
        if (!Auth::check()) return response()->json([], 401);

        $user = Auth::user();
        $days = [];
        $incomeData = [];
        $expenseData = [];
        $range = $request->query('range', 'weekly');

        if ($range === 'monthly') {
            for ($i = 3; $i >= 0; $i--) {
                $startDay = now()->subWeeks($i)->startOfWeek()->format('Y-m-d');
                $endDay   = now()->subWeeks($i)->endOfWeek()->format('Y-m-d ');
                $labels[] = now()->subWeeks($i)->startOfWeek()->format('d M') . ' - ' . now()->subWeeks($i)->endOfWeek()->format('d M');

                $incomeData[] = (int) DB::table('incomes')
                    ->where('user_id', $user->id)
                    ->whereRaw("strftime('%Y-%m-%d', created_at) BETWEEN ? AND ?", [$startDay, $endDay])
                    ->sum('amount');

                $expenseData[] = (int) DB::table('expenses')
                    ->where('user_id', $user->id)
                    ->whereRaw("strftime('%Y-%m-%d', created_at) BETWEEN ? AND ?", [$startDay, $endDay])
                    ->sum('amount');
            }
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $days[] = date('d M', strtotime($date));

                $incomeData[] = (int) DB::table('incomes')
                    ->where('user_id', $user->id)
                    ->whereRaw("strftime('%Y-%m-%d', created_at) = ?", [$date])
                    ->sum('amount');

                $expenseData[] = (int) DB::table('expenses')
                    ->where('user_id', $user->id)
                    ->whereRaw("strftime('%Y-%m-%d', created_at) = ?", [$date])
                    ->sum('amount');
            }
        }

        return response()->json([
            'labels'   => $days,
            'income'   => $incomeData,
            'expense'  => $expenseData
        ]);
    }

    public function chartWallets(): JsonResponse
    {
        if (!Auth::check()) return response()->json([], 401);

        $user = Auth::user();

        $wallets = [
            ['id' => 'mandiri',   'label' => 'Bank Mandiri'],
            ['id' => 'shopeepay', 'label' => 'ShopeePay'],
            ['id' => 'bank jago', 'label' => 'Bank Jago'],
            ['id' => 'gopay',     'label' => 'GoPay'],
        ];

        $data = [];

        foreach ($wallets as $w) {
            $totalExpense = (int) DB::table('expenses')
                ->where('user_id', $user->id)
                ->where(DB::raw('LOWER(source)'), '=', $w['id'])
                ->sum('amount');

            $data[] = [
                'wallet' => $w['label'],
                'total'  => $totalExpense
            ];
        }

        return response()->json($data);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }
}
