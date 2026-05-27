<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WallSync — Aplikasi pencatatan keuangan</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased min-h-screen">

    <div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

        <header class="flex justify-between items-center bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md shadow-emerald-100">
                    W
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">WallSync</h1>
                    <p class="text-xs text-slate-400 font-medium">Manajemen Arus Kas</p>
                </div>
            </div>
            <div id="auth-user-zone" class="hidden flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p id="display-user-name" class="text-sm font-semibold text-slate-700">...</p>
                    <p class="text-[11px] text-slate-400 font-medium">Sesi Aktif</p>
                </div>
                <button id="btn-logout" class="smooth-transition text-xs font-semibold text-slate-500 hover:text-rose-600 bg-slate-50 hover:bg-rose-50 px-4 py-2 rounded-xl border border-slate-100">
                    Keluar
                </button>
            </div>
        </header>

        <section id="section-auth" class="max-w-md mx-auto bg-white border border-slate-100 p-8 rounded-3xl shadow-xl shadow-slate-200/30 space-y-6 my-12">
            <div class="space-y-2 text-center">
                <h2 class="text-2xl font-bold tracking-tight text-slate-800">Selamat Datang</h2>
                <p class="text-sm text-slate-400">Masukkan nama pengguna dan sandi untuk memuat catatan keuangan Anda.</p>
            </div>
            <form id="form-setup" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Nama Pengguna</label>
                    <input type="text" id="setup-username" class="smooth-transition w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium bg-slate-50/50 focus:bg-white form-input-focus" placeholder="Contoh: lutfiana" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                    <input type="password" id="setup-password" class="smooth-transition w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium bg-slate-50/50 focus:bg-white form-input-focus" placeholder="••••••••" required>
                </div>
                <button type="submit" class="smooth-transition w-full font-semibold bg-slate-800 hover:bg-slate-900 text-white py-3.5 rounded-xl shadow-md shadow-slate-800/10 mt-2">
                    Masuk ke Dashboard
                </button>
            </form>
        </section>

        <main id="section-dashboard" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-[#1e293b] text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-emerald-500/10 rounded-full blur-3xl"></div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Saldo</p>
                    <h2 id="display-balance" class="text-3xl font-bold tracking-tight text-white mt-1.5">Rp 0</h2>
                    <div class="mt-4 pt-4 border-t border-slate-700/50 flex justify-between items-center text-xs text-slate-400 font-medium">
                        <span>Status</span>
                        <span class="text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md font-semibold">Aktif</span>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-4 rounded-2xl flex gap-3 shadow-sm">
                    <button onclick="toggleForm('income')" class="smooth-transition flex-1 text-center font-semibold text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 py-3 rounded-xl border border-emerald-100/30">
                        Pemasukan
                    </button>
                    <button onclick="toggleForm('spend')" class="smooth-transition flex-1 text-center font-semibold text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 py-3 rounded-xl border border-rose-100/30">
                        Pengeluaran
                    </button>
                </div>

                <div id="wrapper-forms" class="hidden bg-white border border-slate-100 p-6 rounded-3xl shadow-sm border-l-4 border-emerald-500 smooth-transition">

                    <div id="box-income-form" class="space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <h3 class="text-base font-bold text-slate-800">Tambah Catatan Pemasukan</h3>
                            <button onclick="closeForms()" class="text-xs text-slate-400 hover:text-slate-600 font-medium">Sembunyikan</button>
                        </div>
                        <form id="form-income" class="space-y-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Penyimpanan</label>
                                <select id="income-source" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-medium bg-slate-50 focus:bg-white form-input-focus" required>
                                    <option value="" disabled selected>Pilih Dompet...</option>
                                    <option value="mandiri">Bank Mandiri</option>
                                    <option value="shopeepay">ShopeePay</option>
                                    <option value="bank jago">Bank Jago</option>
                                    <option value="gopay">GoPay</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Deskripsi</label>
                                <input type="text" id="income-desc" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-slate-50 focus:bg-white form-input-focus" placeholder="Uang saku, gaji, dll" required>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Nominal (Rp)</label>
                                <input type="number" id="income-amount" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:bg-white form-input-focus" min="5000" placeholder="Minimal 5000" required>
                            </div>
                            <button type="submit" class="w-full smooth-transition bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-3 rounded-xl shadow-sm">
                                Simpan Pemasukan
                            </button>
                        </form>
                    </div>

                    <div id="box-spend-form" class="hidden space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <h3 class="text-base font-bold text-slate-800">Tambahkan Pengeluaran</h3>
                            <button onclick="closeForms()" class="text-xs text-slate-400 hover:text-slate-600 font-medium">Sembunyikan</button>
                        </div>
                        <form id="form-spend" class="space-y-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Kategori</label>
                                <select id="spend-category" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-medium bg-slate-50 focus:bg-white form-input-focus" required>
                                    <option value="" disabled selected>Pilih Alokasi...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Sumber Dana</label>
                                <select id="spend-source" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-medium bg-slate-50 focus:bg-white form-input-focus" required>
                                    <option value="" disabled selected>Pilih Dompet...</option>
                                    <option value="mandiri">Bank Mandiri</option>
                                    <option value="shopeepay">ShopeePay</option>
                                    <option value="bank jago">Bank Jago</option>
                                    <option value="gopay">GoPay</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Keterangan</label>
                                <input type="text" id="spend-desc" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-slate-50 focus:bg-white form-input-focus" placeholder="Beli apa?" required>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase">Nominal (Rp)</label>
                                <input type="number" id="spend-amount" class="smooth-transition px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:bg-white form-input-focus" min="1000" placeholder="Minimal 1000" required>
                            </div>
                            <button type="submit" class="w-full smooth-transition bg-emerald-600 hover:bg-red-700 text-white font-semibold text-xs py-3 rounded-xl shadow-sm">
                                Simpan Pengeluaran
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl shadow-sm space-y-4">
                    <div class="border-b border-slate-100 pb-2">
                        <h4 class="text-sm font-bold text-slate-800">Jenis Pengeluaran</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Pengeluaran berdasarkan kategori</p>
                    </div>
                    <div class="relative w-full h-40 flex justify-center items-center">
                        <canvas id="chart-category"></canvas>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl shadow-sm space-y-4">
                    <div class="border-b border-slate-100 pb-2">
                        <h4 class="text-sm font-bold text-slate-800">Sumber Dana Pengeluaran</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Jumlah dana keluar per platform</p>
                    </div>
                    <div class="relative w-full h-44">
                        <canvas id="chart-wallets"></canvas>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Arus Kas</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Komparasi data pemasukan vs pengeluaran</p>
                        </div>
                        <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200/40 text-[11px] font-bold">
                            <button onclick="changeCashflowRange('weekly')" id="btn-flow-weekly" class="px-3 py-1.5 rounded-lg smooth-transition bg-white text-slate-800 shadow-sm">
                                7 Hari
                            </button>
                            <button onclick="changeCashflowRange('monthly')" id="btn-flow-monthly" class="px-3 py-1.5 rounded-lg smooth-transition text-slate-400 hover:text-slate-600">
                                1 Bulan
                            </button>
                        </div>
                    </div>
                    <div class="relative w-full h-56">
                        <canvas id="chart-cashflow"></canvas>
                    </div>
                </div>



                <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Riwayat Berkas Transaksi</h3>
                            <p class="text-xs text-slate-400 font-medium">Log kronologis mutasi arus kas keluar dan masuk</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="month" id="filter-month" class="smooth-transition px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 bg-slate-50 focus:bg-white focus:outline-none">
                            <button id="btn-refresh" class="smooth-transition text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
                                Segarkan
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-slate-400 text-xs font-semibold tracking-wider border-b border-slate-100">
                                    <th class="pb-3 pl-2">Tanggal</th>
                                    <th class="pb-3">Aliran & Akun</th>
                                    <th class="pb-3">Keterangan</th>
                                    <th class="pb-3 text-right">Jumlah</th>
                                    <th class="pb-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="history-rows" class="divide-y divide-slate-50 font-medium text-slate-600">
                                </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="toast-box" class="fixed bottom-6 right-6 px-5 py-3.5 rounded-2xl font-semibold shadow-xl text-sm transform transition-all duration-300 translate-y-10 opacity-0 hidden z-50"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    (function () {
        const HEADERS_JSON = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        };

        const $sectionAuth = document.getElementById('section-auth');
        const $sectionDash = document.getElementById('section-dashboard');
        const $authZone = document.getElementById('auth-user-zone');
        const $displayUser = document.getElementById('display-user-name');
        const $balanceDisplay = document.getElementById('display-balance');
        const $historyRows = document.getElementById('history-rows');
        const $filterMonth = document.getElementById('filter-month');
        const $setupForm = document.getElementById('form-setup');
        const $spendForm = document.getElementById('form-spend');
        const $incomeForm = document.getElementById('form-income');
        const $wrapperForms = document.getElementById('wrapper-forms');
        const $boxSpendForm = document.getElementById('box-spend-form');
        const $boxIncomeForm = document.getElementById('box-income-form');
        const $btnRefresh = document.getElementById('btn-refresh');
        const $btnLogout = document.getElementById('btn-logout');

        let chartCategoryInstance = null;
        let chartCashflowInstance = null;
        let chartWalletsInstance = null;
        let currentCashflowRange = 'weekly';

        const today = new Date();
        $filterMonth.value = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast-box');
            toast.textContent = msg;
            toast.classList.remove('hidden', 'translate-y-10', 'opacity-0');
            toast.className = type === 'error'
                ? "fixed bottom-6 right-6 px-5 py-3.5 rounded-2xl font-semibold shadow-xl text-sm bg-rose-50 border border-rose-100 text-rose-600 z-50 transition-all duration-300"
                : "fixed bottom-6 right-6 px-5 py-3.5 rounded-2xl font-semibold shadow-xl text-sm bg-emerald-50 border border-emerald-100 text-emerald-600 z-50 transition-all duration-300";

            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        window.toggleForm = function(type) {
            $wrapperForms.classList.remove('hidden');
            if (type === 'spend') {
                $wrapperForms.className = "bg-white border border-slate-100 p-6 rounded-3xl shadow-sm border-l-4 smooth-transition";
                $boxSpendForm.classList.remove('hidden');
                $boxIncomeForm.classList.add('hidden');
            } else {
                $wrapperForms.className = "bg-white border border-slate-100 p-6 rounded-3xl shadow-sm border-l-4 border-emerald-500 smooth-transition";
                $boxIncomeForm.classList.remove('hidden');
                $boxSpendForm.classList.add('hidden');
            }
        };

        window.closeForms = function() { $wrapperForms.classList.add('hidden'); };

        window.changeCashflowRange = function(range) {
            currentCashflowRange = range;
            const $btnWeekly = document.getElementById('btn-flow-weekly');
            const $btnMonthly = document.getElementById('btn-flow-monthly');

            const activeClasses = ["bg-white", "text-slate-800", "shadow-sm"];
            const inactiveClasses = ["text-slate-400", "hover:text-slate-600"];

            if (range === 'monthly') {
                $btnMonthly.classList.add(...activeClasses);
                $btnMonthly.classList.remove(...inactiveClasses);
                $btnWeekly.classList.remove(...activeClasses);
                $btnWeekly.classList.add(...inactiveClasses);
            } else {
                $btnWeekly.classList.add(...activeClasses);
                $btnWeekly.classList.remove(...inactiveClasses);
                $btnMonthly.classList.remove(...activeClasses);
                $btnMonthly.classList.add(...inactiveClasses);
            }
            loadChartCashflow();
        };

        async function checkAuth() {
            try {
                const res = await fetch('/api/auth-check');
                const data = await res.json();
                if (data.logged_in) {
                    $sectionAuth.classList.add('hidden');
                    $sectionDash.classList.remove('hidden');
                    $authZone.classList.remove('hidden');
                    $displayUser.textContent = data.name;
                    $balanceDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.balance)}`;
                    loadHistory();
                    renderAllCharts();
                } else {
                    $sectionAuth.classList.remove('hidden');
                    $sectionDash.classList.add('hidden');
                    $authZone.classList.add('hidden');
                }
            } catch { showToast('Gagal memvalidasi koneksi.', 'error'); }
        }

        async function handleSetup(e) {
            e.preventDefault();
            const payload = {
                username: document.getElementById('setup-username').value,
                password: document.getElementById('setup-password').value
            };
            try {
                const res = await fetch('/api/setup', { method: 'POST', headers: HEADERS_JSON, body: JSON.stringify(payload) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);
                showToast(data.message);
                checkAuth();
            } catch (err) { showToast(err.message, 'error'); }
        }

        async function handleSpend(e) {
            e.preventDefault();
            const payload = {
                category_id: parseInt(document.getElementById('spend-category').value),
                source: document.getElementById('spend-source').value,
                description: document.getElementById('spend-desc').value,
                amount: parseInt(document.getElementById('spend-amount').value)
            };
            try {
                const res = await fetch('/api/spend', { method: 'POST', headers: HEADERS_JSON, body: JSON.stringify(payload) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                showToast(data.message);
                $balanceDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.new_balance)}`;
                $spendForm.reset();
                closeForms();
                loadHistory();
                renderAllCharts();
            } catch (err) { showToast(err.message, 'error'); }
        }

        async function handleIncome(e) {
            e.preventDefault();
            const payload = {
                source: document.getElementById('income-source').value,
                description: document.getElementById('income-desc').value,
                amount: parseInt(document.getElementById('income-amount').value)
            };
            try {
                const res = await fetch('/api/income', { method: 'POST', headers: HEADERS_JSON, body: JSON.stringify(payload) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                showToast(data.message);
                $balanceDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.new_balance)}`;
                $incomeForm.reset();
                closeForms();
                loadHistory();
                renderAllCharts();
            } catch (err) { showToast(err.message, 'error'); }
        }

        async function loadHistory() {
            try {
                const currentMonth = $filterMonth.value;
                const res = await fetch(`/api/history?month=${currentMonth}`);
                const data = await res.json();

                if (data.length === 0) {
                    $historyRows.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-slate-400 text-xs font-medium">Belum ada mutasi kas transaksi.</td></tr>`;
                    return;
                }

                $historyRows.innerHTML = data.map(e => {
                    const isInc = e.type === 'income';
                    const sign = isInc ? '+Rp ' : '-Rp ';
                    const color = isInc ? 'text-emerald-600' : 'text-rose-600';
                    const badgeBackground = isInc ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 bg-slate-100';
                    return `
                        <tr class="hover:bg-slate-50/70 transition duration-150 text-xs sm:text-sm">
                            <td class="p-3 text-slate-400 font-normal">${e.created_at}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-lg text-[11px] font-semibold tracking-wide border border-transparent ${badgeBackground}">
                                    ${e.label}
                                </span>
                            </td>
                            <td class="p-3 text-slate-700 font-medium">${escapeHtml(e.description)}</td>
                            <td class="p-3 text-right font-semibold tracking-tight ${color}">${sign}${new Intl.NumberFormat('id-ID').format(e.amount)}</td>
                            <td class="p-3 text-center">
                                <button onclick="deleteRow(${e.id}, '${e.type}')" class="smooth-transition text-xs font-semibold text-rose-500 hover:text-white bg-rose-50 hover:bg-rose-600 px-2.5 py-1 rounded-lg border border-rose-100/40">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            } catch { showToast('Gagal memuat rekap kas.', 'error'); }
        }

        window.deleteRow = async function(id, type) {
            if (!confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')) return;
            try {
                const res = await fetch(`/api/transaction/${id}?type=${type}`, { method: 'DELETE', headers: HEADERS_JSON });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                showToast(data.message);
                $balanceDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.new_balance)}`;
                loadHistory();
                renderAllCharts();
            } catch (err) { showToast(err.message, 'error'); }
        };

        function renderAllCharts() {
            loadChartCategory();
            loadChartCashflow();
            loadChartWallets();
        }

        async function loadChartCategory() {
            try {
                const res = await fetch('/api/chart/category');
                const data = await res.json();
                const ctx = document.getElementById('chart-category').getContext('2d');
                if (chartCategoryInstance) chartCategoryInstance.destroy();

                const labels = data.length ? data.map(item => item.label) : ['Belum Ada Data'];
                const totals = data.length ? data.map(item => item.total) : [1];
                const colors = data.length ? data.map(item => item.color) : ['#f1f5f9'];

                chartCategoryInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: totals,
                            backgroundColor: colors,
                            borderWidth: 2, borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } }, cutout: '75%'
                    }
                });
            } catch (err) { console.error('Gagal memuat grafik alokasi:', err); }
        }

        async function loadChartCashflow() {
            try {
                const res = await fetch(`/api/chart/cashflow?range=${currentCashflowRange}`);
                const data = await res.json();
                const $canvas = document.getElementById('chart-cashflow');
                if (!$canvas) return;

                const ctx = $canvas.getContext('2d');

                if (chartCashflowInstance !== null) {
                    chartCashflowInstance.destroy();
                    chartCashflowInstance = null;
                }

                const labels = data.labels || [];
                const income = data.income || [];
                const expense = data.expense || [];

                chartCashflowInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Masuk', data: income, backgroundColor: '#10b981', borderRadius: 6 },
                            { label: 'Keluar', data: expense, backgroundColor: '#f43f5e', borderRadius: 6 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { display: false }, ticks: { display: false } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (err) { console.error('Gagal memuat grafik arus kas:', err); }
        }

        async function loadChartWallets() {
            try {
                const res = await fetch('/api/chart/wallets');
                const data = await res.json();
                const ctx = document.getElementById('chart-wallets').getContext('2d');
                if (chartWalletsInstance) chartWalletsInstance.destroy();

                const labels = data.length ? data.map(i => i.wallet) : ['Bank Mandiri', 'ShopeePay', 'Bank Jago', 'GoPay'];
                const totals = data.length ? data.map(i => i.total) : [0, 0, 0, 0];

                chartWalletsInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{ data: totals, backgroundColor: '#64748b', borderRadius: 6 }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { grid: { display: false }, ticks: { display: false } }, y: { grid: { display: false } } }
                    }
                });
            } catch (err) { console.error('Gagal memuat grafik utilitas dompet:', err); }
        }

        async function handleLogout() {
            try {
                const res = await fetch('/api/logout', { method: 'POST', headers: HEADERS_JSON });
                if (res.ok) { showToast('Berhasil keluar.'); checkAuth(); }
            } catch { showToast('Gagal logout.', 'error'); }
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(String(str)));
            return div.innerHTML;
        }

        $setupForm.addEventListener('submit', handleSetup);
        $spendForm.addEventListener('submit', handleSpend);
        $incomeForm.addEventListener('submit', handleIncome);
        $btnLogout.addEventListener('click', handleLogout);
        $btnRefresh.addEventListener('click', loadHistory);
        $filterMonth.addEventListener('change', loadHistory);

        document.addEventListener('DOMContentLoaded', checkAuth);
    })();
    </script>
</body>
</html>
