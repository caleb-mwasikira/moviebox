To start everything (Reverb WebSocket server + queue worker + dev server):

`
composer run dev       # starts php serve, queue:listen, pail, and vite together
php artisan reverb:start --debug   # in a separate terminal

`