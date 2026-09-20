<?php

namespace App\Http\Middleware;

use App\Support\ChunkedUpload;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Turns "{field}_upload_id" inputs (a file the browser already sent in chunks) into a normal
 * uploaded file on "{field}", so controllers and validation don't need to know the difference.
 */
class ResolveChunkedUploads
{
    /** @var list<ChunkedUpload> */
    private array $claimed = [];

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($request->input() as $key => $id) {
            if (! is_string($id) || ! str_ends_with($key, '_upload_id')) {
                continue;
            }

            $upload = ChunkedUpload::find($id, $request->user()?->id ?? 0);

            if (! $upload || ! $upload->isComplete()) {
                abort(422, 'The uploaded file is incomplete. Retry the upload to finish it.');
            }

            $request->files->set(substr($key, 0, -10), new UploadedFile($upload->path(), $upload->name, null, null, true));
            $this->claimed[] = $upload;
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Keep the file when the form bounced back with errors so a retry doesn't re-upload it.
        $failed = $response->getStatusCode() >= 400 || ($request->hasSession() && $request->session()->has('errors'));

        if (! $failed) {
            foreach ($this->claimed as $upload) {
                \App\Models\VideoUpload::whereKey($upload->id)->delete();
                $upload->discard();
            }
        }
    }
}
