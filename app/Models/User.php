<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'balance'])]
#[Hidden(['password', 'remember_token'])]
#[Casts([balance::class => 'integer'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
}
