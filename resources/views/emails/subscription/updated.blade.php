<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscription Updated — {{ config('app.name') }}</title>
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
                @if ($action === 'extended') Subscription extended @else Subscription renewed @endif
              </h1>
            </td>
          </tr>

          {{-- Body text --}}
          <tr>
            <td style="padding-bottom:36px;">
              <p style="margin:0;font-size:15px;font-weight:400;color:#6b7280;line-height:1.75;">
                Hi {{ $user->name }},
                @if ($action === 'extended')
                  an administrator has extended your subscription by <strong style="color:#111827;font-weight:600;">{{ $days }} {{ $days !== 1 ? 'days' : 'day' }}</strong>. Your access to {{ config('app.name') }} has been updated.
                @else
                  your subscription has been successfully renewed. You now have full access to all content on {{ config('app.name') }}.
                @endif
              </p>
            </td>
          </tr>

          {{-- Info rows --}}
          <tr>
            <td style="padding-bottom:36px;">
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr><td colspan="2" style="border-top:1px solid #e5e7eb;"></td></tr>

                @if ($subscription->package)
                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Plan</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#facc15;text-align:right;border-bottom:1px solid #f3f4f6;">{{ $subscription->package->name }}</td>
                </tr>
                @endif

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Status</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">Active</td>
                </tr>

                @if ($subscription->expires_at)
                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;">Expires</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;">{{ $subscription->expires_at->format('F j, Y') }}</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>

          {{-- CTA --}}
          <tr>
            <td style="padding-bottom:48px;">
              <a href="{{ route('account.billing') }}"
                style="display:inline-block;background:#111827;color:#ffffff;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;padding:14px 28px;text-decoration:none;">
                View Billing Details
              </a>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="border-top:1px solid #e5e7eb;padding-top:24px;">
              <p style="margin:0;font-size:11px;font-weight:400;color:#9ca3af;line-height:1.7;">
                &copy; {{ date('Y') }} {{ config('app.name') }}.
                If you did not expect this email, <a href="{{ route('contact') }}" style="color:#111827;text-decoration:underline;">contact us</a>.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
