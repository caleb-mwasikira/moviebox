<dialog id="login-register" class="modal" onclick="if(event.target===this)this.close()">
  <div class="p-6 sm:p-8">
    <div class="mb-6 flex items-start justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-fg">Sign in to continue</h2>
        <p class="mt-1 text-sm text-muted">An account is required to watch.</p>
      </div>
      <button type="button" onclick="document.getElementById('login-register').close()" class="btn btn-ghost btn-icon -mr-2 -mt-1" aria-label="Close"><x-ui.icon name="x" /></button>
    </div>

    <form method="POST" action="{{ url('/login') }}" class="space-y-4">
      @csrf
      <x-ui.field name="email" type="email" label="Email" id="modal-login-email" bag="login" :value="old('email')" autocomplete="email" required />
      <x-ui.field name="password" type="password" label="Password" id="modal-login-password" bag="login" autocomplete="current-password" required />
      <div class="flex flex-wrap items-center justify-between gap-2">
        <label class="flex cursor-pointer items-center gap-2 text-sm text-fg-soft">
          <input type="checkbox" name="remember" id="modal-remember-me" class="check"> Remember me
        </label>
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand hover:underline">Forgot password?</a>
        @endif
      </div>
      <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
      New here? <a href="{{ route('register') }}" class="font-semibold text-brand hover:underline">Create an account</a>
    </p>
  </div>
</dialog>
