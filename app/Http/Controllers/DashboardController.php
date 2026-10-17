<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Client;
use App\Models\User;
use App\Models\RevisionTicket;
use App\Models\BriefSubmission;
use App\Models\FinancialLog;
use App\Models\ServicePackage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return match ($user->role) {
            'admin' => redirect()->route('admin.overview'),
            'pm' => redirect()->route('pm.dashboard'),
            'developer' => redirect()->route('dev.tasks'),
            'finance' => redirect()->route('finance.invoices'),
            default => redirect()->route('client.dashboard'),
        };
    }

    // 1. Super Admin Overview
    public function adminOverview()
    {
        $totalOrders = Order::count();
        $activeOrders = Order::whereIn('status', ['WAITING_DP', 'BRIEF_PHASE', 'IN_PROGRESS', 'STAGING_REVIEW'])->count();
        $completedOrders = Order::where('status', 'COMPLETED')->count();

        $totalRevenue = Payment::where('status', 'success')->sum('amount_paid');
        $totalCogs = FinancialLog::sum('expense_amount');
        $grossProfit = $totalRevenue - $totalCogs;

        $recentOrders = Order::with(['client.user', 'package', 'assignedPm', 'assignedDev'])->orderBy('created_at', 'desc')->take(10)->get();
        $allUsers = User::orderBy('id', 'desc')->take(10)->get();
        $packages = ServicePackage::withCount('orders')->get();

        return view('dashboards.admin', compact(
            'totalOrders', 'activeOrders', 'completedOrders',
            'totalRevenue', 'totalCogs', 'grossProfit',
            'recentOrders', 'allUsers', 'packages'
        ));
    }

    // 2. Project Manager Dashboard
    public function pmDashboard()
    {
        $user = Auth::user();
        $query = Order::with(['client.user', 'package', 'brief', 'milestones', 'revisionTickets']);
        
        if ($user->role === 'pm') {
            $query->where(function($q) use ($user) {
                $q->where('assigned_pm_id', $user->id)->orWhereNull('assigned_pm_id');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        $pendingBriefs = BriefSubmission::with('order.client')->where('status', 'SUBMITTED')->get();
        $openTickets = RevisionTicket::with('order.client')->where('status', 'open')->get();
        $developers = User::where('role', 'developer')->get();

        return view('dashboards.pm', compact('orders', 'pendingBriefs', 'openTickets', 'developers'));
    }

    // 3. Developer / Designer Tasks
    public function devTasks()
    {
        $user = Auth::user();
        $assignedOrders = Order::with(['client.user', 'package', 'brief', 'milestones', 'revisionTickets', 'credentialVaults'])
            ->where('assigned_dev_id', $user->id)
            ->orWhereIn('status', ['IN_PROGRESS', 'STAGING_REVIEW'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTickets = RevisionTicket::with(['order.package', 'milestone'])
            ->whereIn('status', ['open', 'approved_for_work', 'in_progress'])
            ->orderBy('id', 'desc')
            ->get();

        return view('dashboards.developer', compact('assignedOrders', 'activeTickets'));
    }

    // 4. Finance & Accounting Dashboard
    public function financeInvoices()
    {
        $invoices = Invoice::with(['order.client.user', 'order.package', 'payments'])->orderBy('id', 'desc')->get();
        $payments = Payment::with('invoice.order.client')->orderBy('paid_at', 'desc')->take(20)->get();
        $financialLogs = FinancialLog::with('order')->orderBy('expense_date', 'desc')->get();

        $totalPaid = Payment::where('status', 'success')->sum('amount_paid');
        $totalPending = Invoice::where('status', 'UNPAID')->sum('amount');
        $totalExpenses = FinancialLog::sum('expense_amount');
        $netIncome = $totalPaid - $totalExpenses;

        $orders = Order::orderBy('id', 'desc')->get();

        return view('dashboards.finance', compact(
            'invoices', 'payments', 'financialLogs',
            'totalPaid', 'totalPending', 'totalExpenses', 'netIncome', 'orders'
        ));
    }

    // 5. Client Portal Dashboard
    public function clientDashboard()
    {
        $user = Auth::user();
        $client = $user->clientProfile;

        $orders = $client ? Order::with(['package', 'brief', 'milestones', 'invoices', 'credentialVaults'])
            ->where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        return view('dashboards.client', compact('user', 'client', 'orders'));
    }

    public function storeFinancialLog(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'expense_title' => 'required|string|max:150',
            'expense_amount' => 'required|numeric|min:0',
            'category' => 'required|in:domain,hosting,plugin,freelance,other',
            'vendor_name' => 'nullable|string|max:100',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        FinancialLog::create($request->all());

        return back()->with('success', 'Catatan pengeluaran modal (COGS) berhasil ditambahkan.');
    }

    public function assignStaff(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->update([
            'assigned_pm_id' => $request->assigned_pm_id ?: $order->assigned_pm_id,
            'assigned_dev_id' => $request->assigned_dev_id ?: $order->assigned_dev_id,
        ]);

        return back()->with('success', 'Penugasan staf pelaksana berhasil diperbarui.');
    }
}
