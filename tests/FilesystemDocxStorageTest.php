<?php

namespace Zhandos717\MoonshineDocxEditor\Tests;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use Zhandos717\MoonshineDocxEditor\Storage\FilesystemDocxStorage;

class FilesystemDocxStorageTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('docx-editor', ['disk' => 'local', 'directory' => 'docx-editor']);
    }

    public function test_read_returns_null_without_document(): void
    {
        Storage::fake('local');

        $this->assertNull((new FilesystemDocxStorage())->read('offer'));
    }

    public function test_read_returns_latest_edition(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('docx-editor/offer/2026-10-01_000000.docx', 'old');
        Storage::disk('local')->put('docx-editor/offer/2026-10-28_000000.docx', 'new');

        $this->assertSame('new', (new FilesystemDocxStorage())->read('offer'));
    }

    public function test_write_creates_new_edition_and_keeps_old_one(): void
    {
        Storage::fake('local');
        $storage = new FilesystemDocxStorage();

        $first = $storage->write('offer', 'one');
        Storage::disk('local')->move("docx-editor/offer/{$first['version']}.docx", 'docx-editor/offer/2000-01-01_000000.docx');
        $second = $storage->write('offer', 'two');

        $this->assertCount(2, Storage::disk('local')->files('docx-editor/offer'));
        $this->assertSame('two', $storage->read('offer'));
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}_\d{6}$/', $second['version']);
    }

    public function test_list_returns_existing_document_keys(): void
    {
        Storage::fake('local');
        $storage = new FilesystemDocxStorage();
        $this->assertSame([], $storage->list());

        $storage->write('offer', 'x');
        $storage->write('privacy-policy', 'y');

        $this->assertSame(['offer' => 'offer', 'privacy-policy' => 'privacy-policy'], $storage->list());
    }
}
