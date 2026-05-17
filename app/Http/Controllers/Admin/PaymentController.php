<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = MpesaPayment::with(['user', 'package'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                   ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhere('mpesa_receipt_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(25)->withQueryString();

        $summary = [
            'total_paid'    => MpesaPayment::where('status', 'paid')->sum('amount'),
            'count_paid'    => MpesaPayment::where('status', 'paid')->count(),
            'count_pending' => MpesaPayment::where('status', 'pending')->count(),
            'count_failed'  => MpesaPayment::where('status', 'failed')->count(),
        ];

        return view('pages.admin.payments.index', compact('payments', 'summary'));
    }
}
