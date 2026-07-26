<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'month';
        [$startDate, $endDate] = $this->getPeriodDates($period, $request);

        $revenues = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->with(['reservation.property', 'reservation.client'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->with(['category', 'property'])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalRevenue = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $profit = $totalRevenue - $totalExpenses;

        // $stats array expected by the view for the summary cards
        $stats = [
            'revenue'  => $totalRevenue,
            'expenses' => $totalExpenses,
            'profit'   => $profit,
        ];

        // Recent payments / expenses for the tables
        $recentPayments = $revenues->take(10);
        $recentExpenses = $expenses->take(10);

        // Revenue by property: [{name, revenue}, ...] for Chart.js
        $revenueByProperty = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->with('reservation.property')
            ->get()
            ->groupBy(fn($p) => $p->reservation->property->name ?? 'Inconnu')
            ->map(fn($group, $name) => ['name' => $name, 'revenue' => $group->sum('amount')])
            ->sortByDesc('revenue')
            ->values()
            ->toArray();

        // Expenses by category: [{name, total}, ...] for Chart.js
        $expensesByCategory = $expenses
            ->groupBy(fn($e) => $e->category->name ?? 'Autres')
            ->map(fn($group, $name) => ['name' => $name, 'total' => $group->sum('amount')])
            ->values()
            ->toArray();

        $categories = ExpenseCategory::all();
        $properties  = Property::all();

        return view('admin.finances.index', compact(
            'stats', 'recentPayments', 'recentExpenses',
            'revenueByProperty', 'expensesByCategory',
            'categories', 'properties',
            'period', 'startDate', 'endDate'
        ));
    }

    public function createExpense()
    {
        $categories = ExpenseCategory::all();
        $properties = Property::orderBy('name')->get();
        return view('admin.finances.create-expense', compact('categories', 'properties'));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:expense_categories,id',
            'property_id' => 'nullable|exists:properties,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        if ($request->filled('notes')) {
            $validated['description'] = ($validated['description'] ? $validated['description'] . "\n" : "") . "Notes: " . $request->notes;
        }

        Expense::create(array_merge($validated, ['created_by' => auth()->id()]));
        return redirect()->route('admin.finances.index')->with('success', 'Dépense ajoutée!');
    }

    public function destroyExpense(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Dépense supprimée!');
    }

    private function getPeriodDates(string $period, Request $request): array
    {
        return match($period) {
            'today' => [Carbon::today()->format('Y-m-d'), Carbon::today()->format('Y-m-d')],
            'week' => [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')],
            'month' => [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')],
            'quarter' => [Carbon::now()->startOfQuarter()->format('Y-m-d'), Carbon::now()->endOfQuarter()->format('Y-m-d')],
            'year' => [Carbon::now()->startOfYear()->format('Y-m-d'), Carbon::now()->endOfYear()->format('Y-m-d')],
            'custom' => [$request->from ?? Carbon::now()->startOfMonth()->format('Y-m-d'), $request->to ?? Carbon::now()->format('Y-m-d')],
            default => [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')],
        };
    }
}
