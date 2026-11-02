<?php

namespace App\Http\Controllers\Backend\Order;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusCustomerMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'ILIKE', "%{$q}%")
                    ->orWhere('customer_email', 'ILIKE', "%{$q}%")
                    ->orWhere('target', 'ILIKE', "%{$q}%")
                    ->orWhere('product_name', 'ILIKE', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = Order::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('backend.order.index', [
            'orders'   => $orders,
            'statuses' => Order::STATUSES,
            'counts'   => $counts,
            'current'  => $request->status,
        ]);
    }

    public function show(Order $order)
    {
        return view('backend.order.show', ['order' => $order]);
    }

    /** Tandai sedang diproses. */
    public function process(Order $order)
    {
        if (! $order->isPaid()) {
            return back()->with('error', 'Pesanan belum lunas.');
        }
        $order->update(['status' => 'processing', 'processed_at' => $order->processed_at ?? now()]);

        return back()->with('success', 'Pesanan ' . $order->order_number . ' ditandai SEDANG DIPROSES.');
    }

    /** Selesaikan pesanan (setelah top up manual di VIP Reseller). */
    public function complete(Request $request, Order $order)
    {
        $request->validate([
            'serial_number' => 'nullable|string|max:1000',
            'admin_note'    => 'nullable|string|max:1000',
        ]);

        if (! $order->isPaid()) {
            return back()->with('error', 'Pesanan belum lunas, tidak bisa diselesaikan.');
        }

        $order->update([
            'status'        => 'success',
            'serial_number' => $request->serial_number,
            'admin_note'    => $request->admin_note,
            'completed_at'  => now(),
        ]);

        $this->notify($order, 'success');

        activity()->useLog('order')->causedBy(auth()->user())->performedOn($order)
            ->log('Menyelesaikan pesanan ' . $order->order_number);

        return back()->with('success', 'Pesanan ' . $order->order_number . ' ditandai BERHASIL & email dikirim ke pelanggan.');
    }

    /** Tandai gagal (untuk refund manual). */
    public function fail(Request $request, Order $order)
    {
        $request->validate(['admin_note' => 'required|string|max:1000'], [], ['admin_note' => 'alasan']);

        $order->update([
            'status'       => 'failed',
            'admin_note'   => $request->admin_note,
            'completed_at' => now(),
        ]);

        $this->notify($order, 'failed');

        activity()->useLog('order')->causedBy(auth()->user())->performedOn($order)
            ->log('Menandai gagal pesanan ' . $order->order_number);

        return back()->with('success', 'Pesanan ' . $order->order_number . ' ditandai GAGAL & email dikirim ke pelanggan.');
    }

    private function notify(Order $order, string $type): void
    {
        try {
            Mail::to($order->customer_email)->send(new OrderStatusCustomerMail($order, $type));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email status order ' . $order->order_number . ': ' . $e->getMessage());
        }
    }
}
