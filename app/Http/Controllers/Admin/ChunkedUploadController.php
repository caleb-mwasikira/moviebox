<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\VideoUpload;
use App\Support\ChunkedUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Resumable video uploads: the browser sends a file in small pieces and, after a failure,
 * asks how many bytes the server already holds so it can continue from there.
 */
class ChunkedUploadController extends Controller
{
    private const MAX_BYTES = 20 * 1024 * 1024 * 1024;

    public function init(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'size'        => ['required', 'integer', 'min:1', 'max:' . self::MAX_BYTES],
            'target_type' => ['nullable', 'in:movie,series,episode'],
            'target_id'   => ['required_with:target_type', 'integer'],
            'kind'        => ['required_with:target_type', 'in:video,trailer'],
        ]);

        // Uploads for a specific title are tracked so lists and edit pages can show their progress.
        if (isset($data['target_type'])) {
            $model  = ['movie' => Movie::class, 'series' => Series::class, 'episode' => Episode::class][$data['target_type']];
            $target = $model::findOrFail($data['target_id']);

            // A new upload for the same slot replaces an unfinished one.
            $target->videoUploads()->where('kind', $data['kind'])->get()->each->forget();

            $upload = VideoUpload::start($request->user()->id, $target, $data['kind'], $data['name'], (int) $data['size']);

            return response()->json(['id' => $upload->id, 'received' => 0]);
        }

        $id = ChunkedUpload::create($request->user()->id, $data['name'], (int) $data['size']);

        return response()->json(['id' => $id, 'received' => 0]);
    }

    public function status(Request $request, string $id): JsonResponse
    {
        $upload = ChunkedUpload::find($id, $request->user()->id);
        abort_unless($upload, 404);

        return response()->json(['received' => $upload->received(), 'size' => $upload->size]);
    }

    /** Progress of every unfinished upload for one title, for pages that poll. */
    public function progress(Request $request): JsonResponse
    {
        $ids = array_filter((array) $request->query('ids'), 'is_string');

        return response()->json(
            VideoUpload::whereIn('id', $ids)->get()->mapWithKeys(fn (VideoUpload $u) => [$u->id => [
                'received' => $u->received, 'size' => $u->file_size, 'percent' => $u->percent(), 'stalled' => $u->isStalled(),
            ]])
        );
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        VideoUpload::find($id)?->forget();
        ChunkedUpload::find($id, $request->user()->id)?->discard();

        return response()->json(['ok' => true]);
    }

    public function append(Request $request, string $id): JsonResponse
    {
        $upload = ChunkedUpload::find($id, $request->user()->id);
        abort_unless($upload, 404);

        $offset = (int) $request->header('X-Upload-Offset', -1);

        // The client's idea of progress is out of date (e.g. a chunk landed but its reply was lost).
        if ($offset !== $upload->received()) {
            return response()->json(['received' => $upload->received()], 409);
        }

        $received = $upload->append($request->getContent(true));
        VideoUpload::whereKey($id)->update(['received' => $received, 'updated_at' => now()]);

        if ($received > $upload->size) {
            $upload->discard();
            abort(422, 'Upload is larger than declared.');
        }

        return response()->json(['received' => $received]);
    }
}
