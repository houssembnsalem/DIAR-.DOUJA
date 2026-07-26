<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'description', 'price_per_night', 'weekend_price', 'summer_price', 'capacity',
        'bedrooms', 'surface', 'amenities', 'status', 'location', 'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'price_per_night' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'summer_price' => 'decimal:2',
    ];

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('sort_order');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(PropertyPhoto::class)->where('is_primary', true);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'bungalow' => 'Bungalow',
            'apartment' => 'Appartement',
            'room' => 'Chambre',
            default => $this->type,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'available' => 'Disponible',
            'unavailable' => 'Indisponible',
            'maintenance' => 'En maintenance',
            default => $this->status,
        };
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'available' => 'success',
            'unavailable' => 'danger',
            'maintenance' => 'warning',
            default => 'secondary',
        };
    }

    public function isAvailableForDates(string $checkIn, string $checkOut, $excludeReservationId = null): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        $query = $this->reservations()
            ->whereNotIn('status', ['cancelled', 'checked_out']);

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return !$query->where(function ($q) use ($checkIn, $checkOut) {
            $q->where('check_in', '<', $checkOut)
              ->where('check_out', '>', $checkIn);
        })->exists();
    }

    public function getOccupancyRate(string $startDate, string $endDate): float
    {
        $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays($endDate);
        if ($totalDays === 0) return 0;

        $occupiedDays = $this->reservations()
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->where('check_in', '<', $endDate)
            ->where('check_out', '>', $startDate)
            ->get()
            ->sum(function ($res) use ($startDate, $endDate) {
                $start = max(\Carbon\Carbon::parse($res->check_in), \Carbon\Carbon::parse($startDate));
                $end = min(\Carbon\Carbon::parse($res->check_out), \Carbon\Carbon::parse($endDate));
                return $start->diffInDays($end);
            });

        return round(($occupiedDays / $totalDays) * 100, 1);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
