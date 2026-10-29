<?php

namespace Zhandos717\MoonshineDocxEditor\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\FlexibleRender;

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
        $documents = config('docx-editor.documents');
        $key = request('key', array_key_first($documents));

        return [
            FlexibleRender::make(view('moonshine-docx-editor::editor', [
                'documents' => $documents,
                'key' => array_key_exists($key, $documents) ? $key : null,
                'pageUrl' => $this->getRoute(),
            ])->render()),
        ];
    }
}
