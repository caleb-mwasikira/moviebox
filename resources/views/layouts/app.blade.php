<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
@include('layouts.partials.head')

<body class="flex min-h-screen flex-col">
  @include('layouts.partials.header')
  @include('layouts.partials.search')

  @include('layouts.partials.flash')

  <main id="main-content" class="grow">
    @yield('content')
  </main>

  @hasSection('no-footer')
  @else
    <x-footer />
  @endif

  @include('layouts.partials.auth-modal')
  @include('layouts.partials.trailer-modal')

  <script>
    window.APP_AUTH = @json(auth()->check());
    window.OPEN_AUTH_MODAL = @json($errors->getBag('login')->any() || session('show_auth_modal'));

    function toggleTheme() {
      var dark = !document.documentElement.classList.contains('dark');
      document.documentElement.classList.toggle('dark', dark);
      try { localStorage.setItem('str1-color-theme', dark ? 'dark' : 'light'); } catch (e) {}
    }

    function openAuthModal() {
      var modal = document.getElementById('login-register');
      if (modal && modal.showModal && !modal.open) modal.showModal();
    }

    function watchOrLogin(url) {
      if (window.APP_AUTH) {
        window.location.href = url;
      } else {
        try { localStorage.setItem('moviebox_watch_intended', url); } catch (e) {}
        openAuthModal();
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      if (window.APP_AUTH) {
        try {
          var intended = localStorage.getItem('moviebox_watch_intended');
          if (intended) {
            localStorage.removeItem('moviebox_watch_intended');
            window.location.replace(intended);
            return;
          }
        } catch (e) {}
      }
      if (window.OPEN_AUTH_MODAL) openAuthModal();
    });

    document.addEventListener('alpine:init', function () {
      Alpine.data('watchlistToggle', function (initialState, url) {
        return {
          watchlisted: initialState,
          busy: false,
          toggle: function () {
            if (this.busy) return;
            this.busy = true;
            var self = this;
            fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
              }
            })
            .then(function (r) { return r.json(); })
            .then(function (d) { self.watchlisted = d.watchlisted; self.busy = false; })
            .catch(function () { self.busy = false; });
          }
        };
      });
    });
  </script>

  @stack('scripts')
</body>
</html>
