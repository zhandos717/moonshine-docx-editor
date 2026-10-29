<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Zhandos717\MoonshineDocxEditor\Controllers\DocxEditorController;

Route::moonshine(static function (Router $router): void {
    $router->middleware(moonshineConfig()->getAuthMiddleware())->group(static function (Router $router): void {
        $router->get('docx-editor/{key}/file', [DocxEditorController::class, 'file'])
            ->where('key', '[A-Za-z0-9_-]+')->name('docx-editor.file');
        $router->post('docx-editor/{key}', [DocxEditorController::class, 'save'])
            ->where('key', '[A-Za-z0-9_-]+')->name('docx-editor.save');
    });
});
