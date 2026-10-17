<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CredentialVault;

class VaultController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'service_label' => 'required|string|max:80',
            'login_url' => 'required|url|max:255',
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:255',
        ]);

        $isUnlocked = $order->settlementInvoice && $order->settlementInvoice->isPaid();

        $vault = new CredentialVault([
            'order_id' => $order->id,
            'service_label' => $request->service_label,
            'login_url' => $request->login_url,
            'username' => $request->username,
            'is_unlocked' => $isUnlocked,
        ]);
        $vault->plain_password = $request->password;
        $vault->save();

        return back()->with('success', 'Aset kredensial ' . $vault->service_label . ' berhasil disimpan dengan enkripsi AES-256.');
    }

    public function showPassword($id)
    {
        $vault = CredentialVault::with('order.settlementInvoice')->findOrFail($id);
        $order = $vault->order;

        if (!$vault->is_unlocked && (!$order->settlementInvoice || !$order->settlementInvoice->isPaid())) {
            return response()->json([
                'success' => false,
                'message' => 'Brankas kredensial terkunci (Gated Release). Kredensial hanya dapat diakses setelah invoice Termin 2 (Pelunasan) diselesaikan.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'service_label' => $vault->service_label,
            'username' => $vault->username,
            'password' => $vault->decrypted_password,
        ]);
    }

    public function downloadBast($orderId)
    {
        $order = Order::with(['client.user', 'package', 'addons', 'milestones', 'invoices'])->findOrFail($orderId);

        if (!$order->settlementInvoice || !$order->settlementInvoice->isPaid()) {
            return back()->with('error', 'Berita Acara Serah Terima (BAST) baru dapat diterbitkan setelah pelunasan proyek selesai.');
        }

        return view('vault.bast', compact('order'));
    }
}
