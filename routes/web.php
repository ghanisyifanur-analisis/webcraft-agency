<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BriefController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VaultController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| WebCraft Agency Routes
|--------------------------------------------------------------------------
*/

// Public & Landing Page (Pricing Calculator)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication & Role Quick-Switcher
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/login/quick/{role}', [AuthController::class, 'quickLogin'])->name('login.quick');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Orders & Tracking Portal
Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

// Invoices & Payments (Midtrans Simulator & Webhook)
Route::get('/invoices/{id}', [PaymentController::class, 'show'])->name('invoices.show');
Route::post('/invoices/{id}/simulate', [PaymentController::class, 'simulate'])->name('invoices.simulate');
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

// Brief Onboarding
Route::get('/orders/{orderId}/brief', [BriefController::class, 'create'])->name('briefs.create');
Route::post('/orders/{orderId}/brief', [BriefController::class, 'store'])->name('briefs.store');
Route::post('/briefs/{briefId}/review', [BriefController::class, 'review'])->name('briefs.review');

// Milestones & Digital Sign-off
Route::post('/milestones/{id}/update', [MilestoneController::class, 'update'])->name('milestones.update');
Route::post('/milestones/{id}/signoff', [MilestoneController::class, 'signoff'])->name('milestones.signoff');

// Revisions & Tickets
Route::post('/orders/{orderId}/revisions', [RevisionController::class, 'store'])->name('revisions.store');
Route::post('/revisions/{id}/update', [RevisionController::class, 'update'])->name('revisions.update');

// Credential Vault & Digital BAST
Route::post('/orders/{orderId}/vault', [VaultController::class, 'store'])->name('vault.store');
Route::get('/vault/{id}/reveal', [VaultController::class, 'showPassword'])->name('vault.reveal');
Route::get('/orders/{orderId}/bast', [VaultController::class, 'downloadBast'])->name('vault.bast');

// Role-Based Dashboards
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/admin', [DashboardController::class, 'adminOverview'])->name('dashboard.admin');
Route::get('/dashboard/pm', [DashboardController::class, 'pmDashboard'])->name('dashboard.pm');
Route::get('/dashboard/developer', [DashboardController::class, 'devTasks'])->name('dashboard.developer');
Route::get('/dashboard/finance', [DashboardController::class, 'financeInvoices'])->name('dashboard.finance');
Route::get('/dashboard/client', [DashboardController::class, 'clientDashboard'])->name('dashboard.client');

// Staff Assignment & COGS Expense
Route::post('/orders/{orderId}/assign', [DashboardController::class, 'assignStaff'])->name('orders.assign');
Route::post('/finance/logs', [DashboardController::class, 'storeFinancialLog'])->name('finance.logs.store');
