@php
  $__uid = Auth::id() ?? 0;
  $__exp = time() + 7200;
  $__sig = hash_hmac('sha256', $__uid . ':' . $__exp, config('services.streaming.secret'));
  $__hlsToken = base64_encode($__uid . ':' . $__exp . ':' . $__sig);
@endphp

<div id="trailer-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/90 p-4 backdrop-blur-sm" onclick="if(event.target===this)closeTrailerModal()">
  <button type="button" onclick="closeTrailerModal()" class="absolute right-4 top-4 grid size-10 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20" aria-label="Close trailer">
    <x-ui.icon name="x" />
  </button>
  <video id="trailer-modal-video" class="aspect-video w-full max-w-5xl rounded-2xl bg-black shadow-pop" controls playsinline></video>
</div>

<script>
(function () {
  var hlsToken = @json($__hlsToken);
  var hlsInst = null;
  var hlsLoading = null;

  function loadHls() {
    if (window.Hls) return Promise.resolve();
    if (hlsLoading) return hlsLoading;
    hlsLoading = new Promise(function (resolve) {
      var s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
      s.onload = resolve; s.onerror = resolve;
      document.head.appendChild(s);
    });
    return hlsLoading;
  }

  window.openTrailerModal = function (url) {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    var start = function () {
      if (window.Hls && Hls.isSupported() && url.indexOf('.m3u8') !== -1) {
        if (hlsInst) hlsInst.destroy();
        hlsInst = new Hls({
          xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken); },
          startLevel: -1, abrEwmaDefaultEstimate: 1000000,
          maxBufferLength: 60, maxMaxBufferLength: 120, maxBufferSize: 60 * 1000 * 1000,
        });
        hlsInst.loadSource(url);
        hlsInst.attachMedia(video);
      } else {
        video.src = url;
      }
      video.play().catch(function () {});
    };

    if (url.indexOf('.m3u8') !== -1) loadHls().then(start); else start();
  };

  window.closeTrailerModal = function () {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');
    video.pause();
    if (hlsInst) { hlsInst.destroy(); hlsInst = null; }
    video.removeAttribute('src');
    video.load();
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  };

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') window.closeTrailerModal();
  });
})();
</script>
