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

    public function test_browse_lists_folders_and_docx_only_and_hides_history(): void
    {
        Storage::fake('local');
        $storage = new FilesystemDocxStorage();
        Storage::disk('local')->put('docx-editor/a/one.docx', 'x');
        Storage::disk('local')->put('docx-editor/a/readme.txt', 'x');
        Storage::disk('local')->put('docx-editor/a/.history/one/old.docx', 'x');
        Storage::disk('local')->put('docx-editor/a/sub/two.docx', 'x');

        $this->assertSame(['dirs' => ['sub'], 'files' => ['one.docx']], $storage->browse('a'));
        $this->assertSame(['a'], $storage->browse('')['dirs']);
    }

    public function test_write_file_backs_up_previous_version(): void
    {
        Storage::fake('local');
        $storage = new FilesystemDocxStorage();

        $storage->writeFile('a/one.docx', 'v1');
        $storage->writeFile('a/one.docx', 'v2');

        $this->assertSame('v2', $storage->readFile('a/one.docx'));
        $backups = Storage::disk('local')->files('docx-editor/a/.history/one');
        $this->assertCount(1, $backups);
        $this->assertSame('v1', Storage::disk('local')->get($backups[0]));
    }

    public function test_paths_outside_root_or_not_docx_are_rejected(): void
    {
        Storage::fake('local');
        $storage = new FilesystemDocxStorage();

        foreach (['../secret.docx', 'a/../../x.docx', '.history/x.docx', 'a/notes.txt'] as $bad) {
            try {
                $storage->readFile($bad);
                $this->fail("Путь $bad должен быть отклонён");
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame(422, $e->getStatusCode());
            }
        }
    }
}
