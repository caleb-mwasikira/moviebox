import Alpine from 'alpinejs';
import './resumable-upload';

// Bundled locally (instead of a CDN) so pages don't stall waiting on a third-party host.
window.Alpine = Alpine;
document.addEventListener('DOMContentLoaded', () => Alpine.start());

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// When the app is opened over the LAN, "localhost" would point at the visitor's own device.
const configuredHost = import.meta.env.VITE_REVERB_HOST;
const wsHost = ['localhost', '127.0.0.1'].includes(configuredHost) ? window.location.hostname : configuredHost;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
