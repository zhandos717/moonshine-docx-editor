<?php

namespace Zhandos717\MoonshineDocxEditor\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use MoonShine\Laravel\MoonShineAuth;
use Zhandos717\MoonshineDocxEditor\Contracts\BrowsableDocxStorage;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;
use Zhandos717\MoonshineDocxEditor\Support\StoragePath;

class DocxFilesController extends Controller
{
    private const MIME = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    public function __construct(private readonly DocxStorage $storage)
    {
    }

    public function browse(Request $request): JsonResponse
    {
        $path = StoragePath::clean((string) $request->query('path', ''));
        $this->authorizeAccess($path);

        return response()->json(['path' => $path] + $this->files()->browse($path));
    }

    public function content(Request $request): Response
    {
        $path = StoragePath::docx((string) $request->query('path'));
        $this->authorizeAccess($path);

        $contents = $this->files()->readFile($path);
        abort_if($contents === null, 404);

        $headers = ['Content-Type' => self::MIME, 'Cache-Control' => 'no-store'];
        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="'.basename($path).'"';
        }

        return response($contents, 200, $headers);
    }

    public function save(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'file' => ['required', 'file', 'max:'.config('docx-editor.max_size_kb')],
        ]);

        $path = StoragePath::docx($data['path']);
        $this->authorizeAccess($path);

        $contents = $data['file']->get();
        abort_unless(str_starts_with($contents, "PK\x03\x04"), 422, 'Файл не похож на DOCX');

        $this->files()->writeFile($path, $contents);

        return response()->json(['success' => true, 'path' => $path]);
    }

    public function mkdir(Request $request): JsonResponse
    {
        $data = $request->validate(['path' => ['required', 'string', 'max:500']]);

        $path = StoragePath::clean($data['path']);
        $this->authorizeAccess($path);

        $this->files()->makeDirectory($path);

        return response()->json(['success' => true, 'path' => $path]);
    }

    private function files(): BrowsableDocxStorage
    {
        abort_unless(config('docx-editor.browser') && $this->storage instanceof BrowsableDocxStorage, 404);

        return $this->storage;
    }

    private function authorizeAccess(string $path): void
    {
        if ($ability = config('docx-editor.ability')) {
            Gate::forUser(MoonShineAuth::getGuard()->user())->authorize($ability, $path);
        }
    }
}
