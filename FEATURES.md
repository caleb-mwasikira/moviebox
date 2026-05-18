# Moviebox — Feature Roadmap

- [✅] Add email verification

## Content & Discovery
- [✅] Search bar across movies, series, and coming soon
- [✅] Related / recommended content on movie & series detail pages
- [✅] Continue watching — resume from where the user left off (watch progress table already exists)
- [✅] Watchlist / favourites — users save content to watch later
- [ ] Rating & reviews — users rate and review movies after watching
- [ ] Trending / Top 10 — homepage row ranked by weekly watch count
- [ ] Coming soon with countdown — titles with a future release date show a countdown timer
- [ ] Curated collections — admin creates themed playlists (e.g. "Date Night Picks", "Award Winners")

## Monetization
- [✅] Payment integration (Mpesa)
- [✅] Trial period for new subscribers (e.g. 7-day free trial)
- [ ] Coupon / promo codes for subscription discounts
- [ ] Gift subscriptions — purchase and send a subscription to another user by email
- [ ] Referral programme — users earn free days/credit for each friend who subscribes
- [ ] Pay-per-view / rental — one-off payment to unlock a single title for 48 hours

## User Experience
- [✅] Email notifications — welcome email, subscription expiry reminder, new content alerts
- [✅] User profile page — avatar, change password, watch history
- [✅] Multiple profiles per account (like Netflix) — useful for family plan tiers
- [✅] Download for offline viewing — `download_enabled` flag exists on subscription packages but is not yet implemented
- [✅] Sharing of movies & series by paid user accounts
- [ ] Autoplay next episode — countdown prompt before the next episode starts, with cancel option
- [✅] Watch party — invite friends to a synchronised viewing session with live chat
- [ ] Login with Google and Facebook
- [ ] Playback speed control — 0.5× to 2× in the video player
- [✅] Picture-in-picture / mini player — keep watching while browsing the site

## Admin
- [✅] Dashboard analytics — signups over time, active subscribers, most watched content
- [✅] Revenue reports tied to subscription sales
- [✅] Bulk video upload / import
- [ ] Scheduled publish date — movies publish automatically on a future date
- [ ] Content licensing expiry — auto-unpublish a title after a set date
- [ ] User management panel — search, warn, suspend, or ban individual accounts
- [ ] Review moderation — approve or reject user reviews before they go public

## Video Player
- [ ] Subtitles / caption tracks per video
- [ ] Multiple audio tracks (language dubbing)
- [ ] Quality selector — HLS adaptive bitrate is supported by the current streaming setup
- [ ] Skip intro button for series episodes
- [ ] Skip recap button — skip the "previously on…" segment at the start of an episode
- [ ] Chapter markers — clickable timestamps on the seek bar for movies
- [ ] 10-second skip forward / back buttons
- [ ] Keyboard shortcuts — space to pause, arrow keys to seek, F for fullscreen
