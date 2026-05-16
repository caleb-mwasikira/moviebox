# Moviebox

## Development

To start everything (Reverb WebSocket server + queue worker + dev server):

```bash
composer run dev                    # starts php serve, queue:listen, pail, and vite together
php artisan reverb:start --debug    # in a separate terminal
```

## Email Notifications

The app sends three types of email notifications:

| Notification | Trigger |
|---|---|
| **Welcome** | Sent to the user after their email is verified |
| **Subscription expiry reminder** | Sent daily to users whose subscription expires within 3 days |
| **New content alert** | Sent to all users when a movie or series is published by an admin |

### Queue worker

New content alerts are dispatched as queued jobs to avoid blocking the admin request. A queue worker must be running:

```bash
php artisan queue:work
```

Alternatively, set `QUEUE_CONNECTION=sync` in `.env` to send emails synchronously (suitable for development, not recommended in production).

### Task scheduler

Subscription expiry reminders are sent by the `subscriptions:remind` artisan command, which runs daily at 09:00 via Laravel's scheduler. Add the following cron entry on the server to activate it:

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

You can also trigger the command manually at any time:

```bash
php artisan subscriptions:remind
```