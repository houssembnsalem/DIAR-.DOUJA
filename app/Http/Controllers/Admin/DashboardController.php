<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Property stats
        $totalProperties = Property::count();
        $availableProperties = Property::where('status', 'available')->count();

        // Reservation stats
        $activeReservations = Reservation::whereIn('status', ['confirmed', 'checked_in'])->count();
        $todayCheckIns = Reservation::whereDate('check_in', $today)->whereNotIn('status', ['cancelled'])->count();
        $todayCheckOuts = Reservation::whereDate('check_out', $today)->whereNotIn('status', ['cancelled'])->count();

        // Monthly revenue
        $monthlyRevenue = Payment::whereMonth('payment_date', $today->month)
            ->whereYear('payment_date', $today->year)
            ->sum('amount');

        $monthlyExpenses = Expense::whereMonth('expense_date', $today->month)
            ->whereYear('expense_date', $today->year)
            ->sum('amount');

        $monthlyProfit = $monthlyRevenue - $monthlyExpenses;

        // Recent reservations
        $recentReservations = Reservation::with(['property', 'client'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming check-ins (next 7 days)
        $upcomingCheckIns = Reservation::with(['property', 'client'])
            ->whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', $today->copy()->addDays(7))
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('check_in')
            ->limit(5)
            ->get();

        // Today's checkouts
        $pendingCheckouts = Reservation::with(['property', 'client'])
            ->where('status', 'checked_in')
            ->whereDate('check_out', '<=', $today)
            ->get();

        // Monthly revenue chart (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $rev = Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount');
            $exp = Expense::whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
            $chartData[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => $rev,
                'expenses' => $exp,
                'profit' => $rev - $exp,
            ];
        }

        // Properties with occupancy
        $propertiesOccupancy = Property::withCount([
            'reservations as active_reservations' => function ($q) use ($today) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                  ->where('check_in', '<=', $today)
                  ->where('check_out', '>=', $today);
            }
        ])->get()->map(function ($p) {
            $p->is_occupied = $p->active_reservations > 0;
            return $p;
        });

        $totalClients = Client::count();

        return view('admin.dashboard.index', compact(
            'totalProperties', 'availableProperties', 'activeReservations',
            'todayCheckIns', 'todayCheckOuts', 'monthlyRevenue', 'monthlyExpenses',
            'monthlyProfit', 'recentReservations', 'upcomingCheckIns', 'pendingCheckouts',
            'chartData', 'propertiesOccupancy', 'totalClients'
        ));
    }

    public function stats(Request $request)
    {
        // API endpoint for AJAX chart data
        $period = $request->get('period', 'month');
        $data = [];

        if ($period === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i);
                $data[] = [
                    'label' => $day->translatedFormat('D'),
                    'revenue' => Payment::whereDate('payment_date', $day)->sum('amount'),
                ];
            }
        } elseif ($period === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $data[] = [
                    'label' => $month->translatedFormat('M'),
                    'revenue' => Payment::whereMonth('payment_date', $month->month)
                        ->whereYear('payment_date', $month->year)->sum('amount'),
                ];
            }
        }

        return response()->json($data);
    }
}
