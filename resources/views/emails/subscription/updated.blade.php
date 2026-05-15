<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subject ?? 'Subscription Update' }}</title>
  <style>
    body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .wrap { max-width: 560px; margin: 40px auto; background: #ffffff; }
    .header { background: #111827; padding: 28px 32px; }
    .header-title { color: #facc15; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; margin: 0; }
    .body { padding: 36px 32px; }
    .greeting { font-size: 15px; color: #111827; font-weight: 700; margin: 0 0 16px; }
    .message { font-size: 14px; color: #6b7280; line-height: 1.6; margin: 0 0 28px; }
    .info-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 20px 24px; margin-bottom: 28px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #e5e7eb; }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; }
    .info-value { font-size: 13px; font-weight: 700; color: #111827; }
    .info-value.accent { color: #facc15; }
    .btn { display: inline-block; background: #facc15; color: #111827; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 13px 28px; text-decoration: none; }
    .footer { padding: 20px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <p class="header-title">{{ config('app.name') }}</p>
    </div>

    <div class="body">
      <p class="greeting">Hi {{ $user->name }},</p>

      @if ($action === 'extended')
        <p class="message">
          Great news! An administrator has extended your subscription by <strong>{{ $days }} day{{ $days !== 1 ? 's' : '' }}</strong>.
          Your access to {{ config('app.name') }} has been updated.
        </p>
      @else
        <p class="message">
          Your subscription has been successfully renewed. You now have full access to all content on {{ config('app.name') }}.
        </p>
      @endif

      <div class="info-box">
        @if ($subscription->package)
          <div class="info-row">
            <span class="info-label">Plan</span>
            <span class="info-value accent">{{ $subscription->package->name }}</span>
          </div>
        @endif
        <div class="info-row">
          <span class="info-label">Status</span>
          <span class="info-value">Active</span>
        </div>
        @if ($subscription->expires_at)
          <div class="info-row">
            <span class="info-label">Expires</span>
            <span class="info-value">{{ $subscription->expires_at->format('F j, Y') }}</span>
          </div>
        @endif
      </div>

      <a href="{{ route('account.billing') }}" class="btn">View Billing Details</a>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }}. If you did not expect this email, please
      <a href="{{ route('contact') }}" style="color: #facc15;">contact us</a>.
    </div>
  </div>
</body>
</html>
