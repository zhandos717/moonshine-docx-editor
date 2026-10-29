<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Zhandos717\MoonshineDocxEditor\Controllers\DocxEditorController;
use Zhandos717\MoonshineDocxEditor\Controllers\DocxFilesController;

Route::moonshine(static function (Router $router): void {
    $router->middleware(moonshineConfig()->getAuthMiddleware())->group(static function (Router $router): void {
        $router->get('docx-editor/files/browse', [DocxFilesController::class, 'browse'])->name('docx-editor.files.browse');
        $router->get('docx-editor/files/content', [DocxFilesController::class, 'content'])->name('docx-editor.files.content');
        $router->post('docx-editor/files/save', [DocxFilesController::class, 'save'])->name('docx-editor.files.save');
        $router->post('docx-editor/files/mkdir', [DocxFilesController::class, 'mkdir'])->name('docx-editor.files.mkdir');

        $router->get('docx-editor/{key}/file', [DocxEditorController::class, 'file'])
            ->where('key', '[A-Za-z0-9_-]+')->name('docx-editor.file');
        $router->post('docx-editor/{key}', [DocxEditorController::class, 'save'])
            ->where('key', '[A-Za-z0-9_-]+')->name('docx-editor.save');
    });
});
