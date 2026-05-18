<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>You've received a gift — {{ config('app.name') }}</title>
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

          {{-- Gift image --}}
          <tr>
            <td style="padding-bottom:32px;">
              <img src="{{ url('assets/img/gift.jpg') }}"
                alt="Gift"
                width="560"
                style="display:block;width:100%;max-width:560px;height:auto;object-fit:contain;">
            </td>
          </tr>

          {{-- Heading --}}
          <tr>
            <td style="padding-bottom:12px;">
              <h1 style="margin:0;font-size:24px;font-weight:700;color:#111827;line-height:1.2;letter-spacing:-0.02em;">
                🎁 You've got a gift!
              </h1>
            </td>
          </tr>

          {{-- Body text --}}
          <tr>
            <td style="padding-bottom:28px;">
              <p style="margin:0;font-size:15px;font-weight:400;color:#6b7280;line-height:1.75;">
                <strong style="color:#111827;">{{ $gift->sender->name }}</strong> has gifted you a
                <strong style="color:#111827;">{{ $gift->package->name }}</strong> subscription to {{ config('app.name') }}.
                Stream movies and series anytime — no ads, no limits.
              </p>
            </td>
          </tr>

          @if ($gift->message)
          {{-- Personal message --}}
          <tr>
            <td style="padding-bottom:28px;">
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="border-left:3px solid #facc15;padding:14px 16px;background:#fefce8;">
                    <p style="margin:0;font-size:14px;font-style:italic;color:#374151;line-height:1.7;">
                      "{{ $gift->message }}"
                    </p>
                    <p style="margin:8px 0 0;font-size:12px;font-weight:600;color:#9ca3af;">
                      — {{ $gift->sender->name }}
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          @endif

          {{-- Package details --}}
          <tr>
            <td style="padding-bottom:32px;">
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr><td colspan="2" style="border-top:1px solid #e5e7eb;"></td></tr>

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Plan</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:700;color:#facc15;text-align:right;border-bottom:1px solid #f3f4f6;">{{ $gift->package->name }}</td>
                </tr>

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">Duration</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">1 {{ ucfirst($gift->package->interval) }}</td>
                </tr>

                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;border-bottom:1px solid #f3f4f6;">From</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">{{ $gift->sender->name }}</td>
                </tr>

                @if ($gift->expires_at)
                <tr>
                  <td style="padding:13px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;">Claim by</td>
                  <td style="padding:13px 0;font-size:13px;font-weight:600;color:#111827;text-align:right;">{{ $gift->expires_at->format('F j, Y') }}</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>

          {{-- CTA --}}
          <tr>
            <td style="padding-bottom:48px;">
              <a href="{{ route('gift.accept', $gift->token) }}"
                style="display:inline-block;background:#facc15;color:#111827;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;padding:16px 32px;text-decoration:none;">
                Claim Your Gift
              </a>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="border-top:1px solid #e5e7eb;padding-top:24px;">
              <p style="margin:0;font-size:11px;font-weight:400;color:#9ca3af;line-height:1.7;">
                &copy; {{ date('Y') }} {{ config('app.name') }}.
                This gift was sent to {{ $gift->recipient_email }}.
                If you did not expect this email, you can safely ignore it.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
