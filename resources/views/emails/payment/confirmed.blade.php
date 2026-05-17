<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Confirmed — {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:'Instrument Sans',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;color:#111827;-webkit-font-smoothing:antialiased;">

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td align="center" style="padding:48px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:560px;">

          {{-- Header --}}
          <tr>
            <td style="padding-bottom:32px;">
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="border-left:3px solid #facc15;padding-left:12px;">
                    <span style="font-size:13px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#111827;">
                      {{ config('app.name') }}
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Divider --}}
          <tr><td style="border-top:1px solid #e5e7eb;padding-bottom:36px;"></td></tr>

          {{-- Heading --}}
          <tr>
            <td style="padding-bottom:12px;">
              <h1 style="margin:0;font-size:24px;font-weight:700;color:#111827;line-height:1.2;letter-spacing:-0.02em;">
                Payment confirmed
              </h1>
            </td>
          </tr>

          {{-- Body text --}}
          <tr>
            <td style="padding-bottom:36px;">
              <p style="margin:0;font-size:15px;font-weight:400;color:#6b7280;line-height:1.75;">
                Hi {{ $user->name }}, we've received your M-Pesa payment and your subscription is now active. Thank you for subscribing to {{ config('app.name') }}.
              </p>
            </td>
          </tr>

          {{-- Receipt rows --}}
          <tr>
            <td style="padding-bottom:36px;">
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr><td colspan="2" style="border-top:1px solid #e5e7eb;"></td></tr>

                @if ($payment->mpesa_receipt_number)
                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">M-Pesa Receipt</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:700;color:#facc15;text-align:right;border-bottom:1px solid #f3f4f6;font-family:'Courier New',Courier,monospace;letter-spacing:0.04em;">{{ $payment->mpesa_receipt_number }}</td>
                </tr>
                @endif

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Amount Paid</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">
                    {{ $payment->package?->currency ?? 'KES' }} {{ number_format($payment->amount) }}
                  </td>
                </tr>

                @if ($payment->package)
                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Plan</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">{{ $payment->package->name }}</td>
                </tr>
                @endif

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Phone</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;font-family:'Courier New',Courier,monospace;">+{{ $payment->phone }}</td>
                </tr>

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;">Date</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;">{{ $payment->created_at->format('F j, Y · H:i') }}</td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- CTA --}}
          <tr>
            <td style="padding-bottom:48px;">
              <a href="{{ route('browse') }}"
                style="display:inline-block;background:#111827;color:#ffffff;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;padding:14px 28px;text-decoration:none;">
                Start Watching
              </a>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="border-top:1px solid #e5e7eb;padding-top:24px;">
              <p style="margin:0;font-size:11px;font-weight:400;color:#9ca3af;line-height:1.7;">
                &copy; {{ date('Y') }} {{ config('app.name') }}.
                If you did not make this payment, <a href="{{ route('contact') }}" style="color:#111827;text-decoration:underline;">contact us immediately</a>.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
