<?php

namespace Zhandos717\MoonshineDocxEditor\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;

class DocxEditorController extends Controller
{
    private const MIME = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    public function __construct(private readonly DocxStorage $storage)
    {
    }

    public function file(string $key): Response
    {
        $this->ensureKnown($key);

        $contents = $this->storage->read($key);
        abort_if($contents === null, 404);

        return response($contents, 200, ['Content-Type' => self::MIME, 'Cache-Control' => 'no-store']);
    }

    public function save(Request $request, string $key): JsonResponse
    {
        $this->ensureKnown($key);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:'.config('docx-editor.max_size_kb')],
            'note' => ['nullable', 'string', 'max:500'],
            'requires_reaccept' => ['nullable', 'boolean'],
        ]);

        $contents = $data['file']->get();
        abort_unless(str_starts_with($contents, "PK\x03\x04"), 422, 'Файл не похож на DOCX');

        $result = $this->storage->write($key, $contents, [
            'note' => $data['note'] ?? null,
            'requires_reaccept' => (bool) ($data['requires_reaccept'] ?? false),
            'user' => $request->user('moonshine')?->email ?? $request->user()?->email,
        ]);

        return response()->json(['success' => true] + $result);
    }

    private function ensureKnown(string $key): void
    {
        abort_unless(array_key_exists($key, config('docx-editor.documents')), 404);
    }
}
