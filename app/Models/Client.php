<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'id_number', 'nationality', 'address', 'notes',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->reservations()->sum('amount_paid');
    }

    public function getActiveReservationsCount(): int
    {
        return $this->reservations()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();
    }
}
