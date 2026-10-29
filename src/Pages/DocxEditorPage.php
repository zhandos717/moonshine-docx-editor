<?php

namespace Zhandos717\MoonshineDocxEditor\Pages;

use Illuminate\Support\Facades\Gate;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\FlexibleRender;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;
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

        $key = (string) request('key', (string) array_key_first($documents));
        $isKnown = array_key_exists($key, $documents);
        if (! $isKnown && ! ($canCreate && DocumentCatalog::isValidKey($key))) {
            $key = (string) array_key_first($documents);
        }

        return [
            FlexibleRender::make(view('moonshine-docx-editor::editor', [
                'documents' => $documents,
                'key' => $key !== '' ? $key : null,
                'title' => $documents[$key] ?? request('title', $key),
                'canCreate' => $canCreate,
                'pageUrl' => $this->getRoute(),
            ])->render()),
        ];
    }
}
