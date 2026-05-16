# Moviebox — Feature Roadmap

- [✅] Add email verification

## Content & Discovery
- [✅] Search bar across movies, series, and coming soon
- [ ] Related / recommended content on movie & series detail pages
- [✅] Continue watching — resume from where the user left off (watch progress table already exists)
- [✅] Watchlist / favourites — users save content to watch later
- [ ] Rating & reviews — users rate and review movies after watching

## Monetization
- [ ] Payment gateway integration (Stripe, M-Pesa, PayPal) — subscriptions are currently manually managed
- [ ] Coupon / promo codes for subscription discounts
- [ ] Trial period for new subscribers (e.g. 7-day free trial)

## User Experience
- [✅] Email notifications — welcome email, subscription expiry reminder, new content alerts
- [ ] User profile page — avatar, change password, watch history
- [ ] Multiple profiles per account (like Netflix) — useful for family plan tiers
- [ ] Download for offline viewing — `download_enabled` flag exists on subscription packages but is not yet implemented

## Admin
- [ ] Dashboard analytics — signups over time, active subscribers, most watched content
- [ ] Revenue reports tied to subscription sales
- [ ] Scheduled publish date — movies publish automatically on a future date
- [ ] Bulk video upload / import

## Video Player
- [ ] Subtitles / caption tracks per video
- [ ] Multiple audio tracks (language dubbing)
- [ ] Quality selector — HLS adaptive bitrate is supported by the current streaming setup
- [ ] Skip intro button for series episodes
