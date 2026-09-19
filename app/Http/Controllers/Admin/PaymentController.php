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

        // Every word must match somewhere (name, email, phone or receipt); partial words are fine,
        // and word order doesn't matter ("doe jane" finds "Jane Doe").
        $terms = preg_split('/\s+/', trim((string) $request->input('search')), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($terms as $term) {
            $like = '%' . mb_strtolower($term) . '%';

            // Phones are stored in international form (2547…); let admins type 07…, +254 7… or a fragment.
            $digits = preg_replace('/\D+/', '', $term);
            $phoneCore = preg_match('/^(?:254|0)?(\d{2,})$/', $digits, $m) ? $m[1] : null;

            $query->where(function ($q) use ($like, $phoneCore, $term) {
                $q->whereHas('user', fn ($u) => $u->whereRaw('LOWER(name) LIKE ?', [$like])
                                                   ->orWhereRaw('LOWER(email) LIKE ?', [$like]))
                  ->orWhereRaw('LOWER(mpesa_receipt_number) LIKE ?', [$like])
                  ->orWhere('phone', 'like', '%' . $term . '%');

                if ($phoneCore) {
                    $q->orWhere('phone', 'like', '%' . $phoneCore . '%');
                }
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
