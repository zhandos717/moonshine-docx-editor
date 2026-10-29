@if ($key === null && ! $canCreate)
    <p>{{ __('moonshine-docx-editor::ui.no_documents') }}</p>
@else
    <div data-docx-editor
         @if ($key !== null)
         data-file-url="{{ route('moonshine.docx-editor.file', $key) }}"
         data-save-url="{{ route('moonshine.docx-editor.save', $key) }}"
         @endif
         data-page-url="{{ $pageUrl }}"
         style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
            @foreach ($documents as $docKey => $docTitle)
                <a href="{{ $pageUrl }}?key={{ $docKey }}"
                   class="btn {{ $docKey === $key ? 'btn-primary' : '' }}">{{ $docTitle }}</a>
            @endforeach
            @if ($canCreate)
                <input type="text" data-docx-new-title class="form-input" style="max-width:220px"
                       placeholder="{{ __('moonshine-docx-editor::ui.new_title') }}">
                <button type="button" class="btn" data-docx-new>{{ __('moonshine-docx-editor::ui.create') }}</button>
            @endif
        </div>
        @if ($key !== null)
            <docen-document filename="{{ $title }}.docx" style="display:block;height:75vh"></docen-document>
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
                <input type="text" name="note" maxlength="500" class="form-input" style="max-width:360px"
                       placeholder="{{ __('moonshine-docx-editor::ui.note') }}">
                <label style="display:flex;gap:6px;align-items:center">
                    <input type="checkbox" name="requires_reaccept">
                    {{ __('moonshine-docx-editor::ui.requires_reaccept') }}
                </label>
                <button type="button" class="btn btn-primary" data-docx-save>{{ __('moonshine-docx-editor::ui.save') }}</button>
                <label class="btn" style="cursor:pointer">
                    {{ __('moonshine-docx-editor::ui.upload') }}
                    <input type="file" accept=".docx" data-docx-upload hidden>
                </label>
                <a class="btn" href="{{ route('moonshine.docx-editor.file', $key) }}?download=1">{{ __('moonshine-docx-editor::ui.download') }}</a>
                <span data-docx-status></span>
            </div>
        @endif
    </div>
    <script type="module" src="{{ asset('vendor/moonshine-docx-editor/docx-editor.js') }}"></script>
@endif
