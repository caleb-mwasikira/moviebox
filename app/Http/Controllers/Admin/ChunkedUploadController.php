<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'integer', 'min:1', 'max:' . self::MAX_BYTES],
        ]);

        $id = ChunkedUpload::create($request->user()->id, $data['name'], (int) $data['size']);

        return response()->json(['id' => $id, 'received' => 0]);
    }

    public function status(Request $request, string $id): JsonResponse
    {
        $upload = ChunkedUpload::find($id, $request->user()->id);
        abort_unless($upload, 404);

        return response()->json(['received' => $upload->received(), 'size' => $upload->size]);
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

        if ($received > $upload->size) {
            $upload->discard();
            abort(422, 'Upload is larger than declared.');
        }

        return response()->json(['received' => $received]);
    }
}
