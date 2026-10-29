<?php

namespace Zhandos717\MoonshineDocxEditor\Pages;

use Illuminate\Support\Facades\Gate;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\FlexibleRender;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Zhandos717\MoonshineDocxEditor\Contracts\BrowsableDocxStorage;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;
use Zhandos717\MoonshineDocxEditor\Support\StoragePath;
use Zhandos717\MoonshineDocxEditor\Support\DocumentCatalog;

#[Icon('document-text')]
class DocxEditorPage extends Page
{
    public function getTitle(): string
    {
        return $this->title ?: __('moonshine-docx-editor::ui.title');
    }

    public function getBreadcrumbs(): array
    {
        return ['#' => $this->getTitle()];
    }

    protected function components(): iterable
    {
        if ($ability = config('docx-editor.ability')) {
            Gate::forUser(MoonShineAuth::getGuard()->user())->authorize($ability);
        }

        $documents = DocumentCatalog::all(app(DocxStorage::class));
        $canCreate = (bool) config('docx-editor.allow_create');

        $browser = (bool) config('docx-editor.browser') && app(DocxStorage::class) instanceof BrowsableDocxStorage;

        $file = null;
        if ($browser && is_string(request('file')) && request('file') !== '') {
            try {
                $file = StoragePath::docx(request('file'));
            } catch (HttpException) {
                $file = null;
            }
        }

        $key = (string) request('key', (string) array_key_first($documents));
        $isKnown = array_key_exists($key, $documents);
        if (! $isKnown && ! ($canCreate && DocumentCatalog::isValidKey($key))) {
            $key = (string) array_key_first($documents);
        }
        if ($file !== null) {
            $key = '';
        }

        return [
            FlexibleRender::make(view('moonshine-docx-editor::editor', [
                'documents' => $documents,
                'key' => $key !== '' ? $key : null,
                'file' => $file,
                'browser' => $browser,
                'title' => $file !== null ? basename($file) : ($documents[$key] ?? request('title', $key)),
                'canCreate' => $canCreate,
                'pageUrl' => $this->getRoute(),
            ])->render()),
        ];
    }
}
