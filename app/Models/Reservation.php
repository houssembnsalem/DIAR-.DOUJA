<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reservation_number', 'property_id', 'client_id', 'created_by',
        'check_in', 'check_out', 'guests_count', 'price_per_night',
        'total_amount', 'discount', 'final_amount', 'amount_paid',
        'payment_status', 'status', 'actual_check_in', 'actual_check_out', 'notes', 'source',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime',
        'price_per_night' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function services()
    {
        return $this->hasMany(ReservationService::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    public function getAmountRemainingAttribute(): float
    {
        return max(0, $this->final_amount - $this->amount_paid);
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'confirmed' => 'Confirmée',
            'pending' => 'En attente',
            'checked_in' => 'En cours',
            'checked_out' => 'Terminée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'checked_in' => 'info',
            'checked_out' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getPaymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'paid' => 'Payé',
            'partial' => 'Partiel',
            'pending' => 'Non payé',
            default => $this->payment_status,
        };
    }

    public function getPaymentStatusBadge(): string
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'partial' => 'warning',
            'pending' => 'danger',
            default => 'secondary',
        };
    }

    public function updatePaymentStatus(): void
    {
        if ($this->amount_paid >= $this->final_amount) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'pending';
        }
        $this->save();
    }

    public function isUpcoming(): bool
    {
        return $this->check_in->isFuture();
    }

    public function isCurrent(): bool
    {
        return $this->check_in->isPast() && $this->check_out->isFuture();
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->count() + 1;
        return 'RES-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
