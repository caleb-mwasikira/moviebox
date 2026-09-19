{{--
  Shared playback engine: HLS loading, resume position, skip / keyboard shortcuts.
  The video element must already exist on the page (id = $id).
--}}
@props(['id', 'type', 'uuid', 'stream' => null, 'fallback' => null, 'saved' => null])

@php
  $__uid = Auth::id() ?? 0;
  $__exp = time() + 7200;
  $__sig = hash_hmac('sha256', $__uid . ':' . $__exp, config('services.streaming.secret'));
  $__hlsToken = base64_encode($__uid . ':' . $__exp . ':' . $__sig);
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
(function () {
  const hlsToken = @json($__hlsToken);
  const video = document.getElementById(@json($id));
  if (!video) return;

  // ── HLS ──
  function loadHls(src) {
    if (window.Hls && Hls.isSupported()) {
      const hls = new Hls({
        xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken); },
        startLevel: -1,
        abrEwmaDefaultEstimate: 1000000,
        maxBufferLength: 60,
        maxMaxBufferLength: 120,
        maxBufferSize: 60 * 1000 * 1000,
      });
      hls.loadSource(src);
      hls.attachMedia(video);
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = src;
    }
  }
  const hlsSrc = @json($stream);
  if (hlsSrc) loadHls(hlsSrc); else video.src = @json($fallback);

  // ── Resume playback ──
  const _key       = 'mvbox_pos_' + @json($uuid);
  const _uuid      = @json($uuid);
  const _isAuth    = @json(auth()->check());
  const _csrf      = @json(csrf_token());
  const _serverPos = @json($saved);
  let _saveTimer   = null;

  function _syncServer(position, duration) {
    fetch('/watch/progress', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
      body: JSON.stringify({ type: @json($type), uuid: _uuid, position: position, duration: duration }),
      keepalive: true,
    }).catch(function () {});
  }
  function _save() {
    const t = video.currentTime, d = video.duration;
    if (!t || !d || t < 5) return;
    if (d - t < 30) {
      localStorage.removeItem(_key);
    } else {
      localStorage.setItem(_key, t.toFixed(1));
      if (_isAuth) _syncServer(t, d);
    }
  }
  video.addEventListener('timeupdate', function () {
    if (_saveTimer) return;
    _saveTimer = setTimeout(function () { _saveTimer = null; _save(); }, 5000);
  });
  video.addEventListener('pause', _save);
  video.addEventListener('ended', function () { localStorage.removeItem(_key); });
  video.addEventListener('loadedmetadata', function () {
    const localPos  = parseFloat(localStorage.getItem(_key) || '0');
    const resumePos = (_serverPos && _serverPos > localPos) ? _serverPos : localPos;
    if (resumePos > 5 && video.duration && resumePos < video.duration - 30) {
      video.currentTime = resumePos;
    }
  });

  // ── Skip & keyboard shortcuts ──
  const PLAY_ICON  = '<svg width="34" height="34" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
  const PAUSE_ICON = '<svg width="34" height="34" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';

  function showFlash(id, html) {
    const el = document.getElementById(id);
    if (!el) return;
    if (html !== undefined) el.innerHTML = html;
    el.classList.remove('active');
    void el.offsetWidth;
    el.classList.add('active');
  }
  window.skipVideo = function (seconds) {
    video.currentTime = Math.max(0, Math.min(video.duration || Infinity, video.currentTime + seconds));
    showFlash(seconds < 0 ? 'flash-back' : 'flash-fwd');
  };
  document.addEventListener('keydown', function (e) {
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) || e.target.isContentEditable) return;
    switch (e.key) {
      case ' ': case 'k':
        e.preventDefault();
        if (video.paused) { video.play(); showFlash('flash-pp', PLAY_ICON); }
        else { video.pause(); showFlash('flash-pp', PAUSE_ICON); }
        break;
      case 'ArrowLeft':
        e.preventDefault(); skipVideo(-10); break;
      case 'ArrowRight':
        e.preventDefault(); skipVideo(10); break;
      case 'f': case 'F':
        e.preventDefault();
        document.fullscreenElement ? document.exitFullscreen()
          : (video.requestFullscreen && video.requestFullscreen().catch(() => {}));
        break;
    }
  });
})();
</script>
@endpush
