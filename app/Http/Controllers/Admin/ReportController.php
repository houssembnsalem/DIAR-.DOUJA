<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'month';
        [$startDate, $endDate] = $this->getPeriodDates($period, $request);

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Basic stats
        $totalRevenue = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $profit = $totalRevenue - $totalExpenses;
        $totalReservations = Reservation::whereBetween('check_in', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])->count();
        $totalNights = Reservation::whereBetween('check_in', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->sum('nights');

        // Properties stats
        $allProperties = Property::all();
        $propertiesStats = $allProperties->map(function ($p) use ($startDate, $endDate) {
            $revenue = Payment::whereHas('reservation', fn($q) => $q->where('property_id', $p->id))
                ->whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
            $resCount = Reservation::where('property_id', $p->id)
                ->whereBetween('check_in', [$startDate, $endDate])
                ->whereNotIn('status', ['cancelled'])->count();
            $nights = Reservation::where('property_id', $p->id)
                ->whereBetween('check_in', [$startDate, $endDate])
                ->whereNotIn('status', ['cancelled'])
                ->get()->sum('nights');
                
            return [
                'name' => $p->name,
                'revenue' => (float)$revenue,
                'occupancy' => $p->getOccupancyRate($startDate, $endDate),
                'reservations' => $resCount,
                'nights' => $nights
            ];
        });

        // Occupancy rate global
        $totalPossibleNights = 0;
        $diffDays = $start->diffInDays($end) + 1;
        foreach($allProperties as $p) {
            $totalPossibleNights += $diffDays;
        }
        $globalOccupancy = $totalPossibleNights > 0 ? round(($totalNights / $totalPossibleNights) * 100, 1) : 0;

        // Top properties
        $topProperties = $propertiesStats->sortByDesc('revenue')->take(5)->values()->toArray();

        // Monthly chart data
        $monthlyData = [];
        $current = $start->copy()->startOfMonth();
        $chartEnd = $end->copy()->endOfMonth();
        while ($current->lte($chartEnd)) {
            $mStart = $current->format('Y-m-d');
            $mEnd = $current->copy()->endOfMonth()->format('Y-m-d');
            $monthlyData[] = [
                'month' => $current->translatedFormat('M Y'),
                'revenue' => (float)Payment::whereBetween('payment_date', [$mStart, $mEnd])->sum('amount'),
                'expenses' => (float)Expense::whereBetween('expense_date', [$mStart, $mEnd])->sum('amount'),
                'profit' => (float)(Payment::whereBetween('payment_date', [$mStart, $mEnd])->sum('amount') - Expense::whereBetween('expense_date', [$mStart, $mEnd])->sum('amount')),
            ];
            $current->addMonth();
        }

        $stats = [
            'revenue' => $totalRevenue,
            'expenses' => $totalExpenses,
            'profit' => $profit,
            'total_reservations' => $totalReservations,
            'total_nights' => $totalNights,
            'occupancy_rate' => $globalOccupancy,
            'top_properties' => $topProperties,
            'properties_stats' => $propertiesStats->values()->toArray(),
            'monthly_data' => $monthlyData
        ];

        return view('admin.reports.index', compact('period', 'startDate', 'endDate', 'stats'));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->period ?? 'month';
        [$startDate, $endDate] = $this->getPeriodDates($period, $request);

        $data = $this->gatherReportData($startDate, $endDate);
        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($data, [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]));
        $pdf->setPaper('A4');
        return $pdf->download('rapport-' . $period . '-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $period = $request->period ?? 'month';
        [$startDate, $endDate] = $this->getPeriodDates($period, $request);

        // Simple CSV export as fallback
        $reservations = Reservation::with(['property', 'client'])
            ->whereBetween('check_in', [$startDate, $endDate])
            ->get();

        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => 'attachment; filename=rapport-' . $period . '.csv'];
        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, ['N° Réservation', 'Logement', 'Client', 'Téléphone', 'Check-in', 'Check-out', 'Nuits', 'Montant', 'Payé', 'Reste', 'Statut'], ';');
            foreach ($reservations as $r) {
                fputcsv($file, [
                    $r->reservation_number,
                    $r->property->name ?? '',
                    $r->client->full_name ?? '',
                    $r->client->phone ?? '',
                    $r->check_in->format('d/m/Y'),
                    $r->check_out->format('d/m/Y'),
                    $r->nights,
                    $r->final_amount,
                    $r->amount_paid,
                    $r->amount_remaining,
                    $r->getStatusLabel(),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function gatherReportData(string $startDate, string $endDate): array
    {
        return [
            'totalReservations' => Reservation::whereBetween('check_in', [$startDate, $endDate])->whereNotIn('status', ['cancelled'])->count(),
            'totalRevenue' => Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'totalExpenses' => Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount'),
            'properties' => Property::all()->map(function ($p) use ($startDate, $endDate) {
                $p->occupancy = $p->getOccupancyRate($startDate, $endDate);
                return $p;
            }),
        ];
    }

    private function getPeriodDates(string $period, Request $request): array
    {
        return match($period) {
            'today' => [Carbon::today()->format('Y-m-d'), Carbon::today()->format('Y-m-d')],
            'week' => [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')],
            'month' => [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')],
            'year' => [Carbon::now()->startOfYear()->format('Y-m-d'), Carbon::now()->endOfYear()->format('Y-m-d')],
            'custom' => [$request->date_from ?? Carbon::now()->startOfMonth()->format('Y-m-d'), $request->date_to ?? Carbon::now()->format('Y-m-d')],
            default => [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')],
        };
    }
}
