<?php

namespace Zhandos717\MoonshineDocxEditor;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\MenuManager\MenuItem;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;
use Zhandos717\MoonshineDocxEditor\Pages\DocxEditorPage;

class DocxEditorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/docx-editor.php', 'docx-editor');

        $this->app->bind(DocxStorage::class, fn ($app) => $app->make(config('docx-editor.storage')));
    }

    public function boot(): void
    {
        $core = $this->app->make(CoreContract::class);
        $menu = $this->app->make(MenuManagerContract::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'moonshine-docx-editor');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'moonshine-docx-editor');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $core->pages([DocxEditorPage::class]);

        if (config('docx-editor.auto_menu')) {
            $menu->add([MenuItem::make(DocxEditorPage::class, __('moonshine-docx-editor::ui.title'))]);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/docx-editor.php' => config_path('docx-editor.php'),
            ], 'moonshine-docx-editor-config');

            $this->publishes([
                __DIR__.'/../dist' => public_path('vendor/moonshine-docx-editor'),
            ], 'moonshine-docx-editor-assets');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/moonshine-docx-editor'),
            ], 'moonshine-docx-editor-views');

            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/moonshine-docx-editor'),
            ], 'moonshine-docx-editor-lang');
        }
    }
}
