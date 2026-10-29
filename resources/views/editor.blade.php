@if ($key === null)
    <p>{{ __('moonshine-docx-editor::ui.no_documents') }}</p>
@else
    <div data-docx-editor
         data-file-url="{{ route('moonshine.docx-editor.file', $key) }}"
         data-save-url="{{ route('moonshine.docx-editor.save', $key) }}"
         style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
            @foreach ($documents as $docKey => $title)
                <a href="{{ $pageUrl }}?key={{ $docKey }}"
                   class="btn {{ $docKey === $key ? 'btn-primary' : '' }}">{{ $title }}</a>
            @endforeach
        </div>
        <docen-document filename="{{ $documents[$key] }}.docx" style="display:block;height:75vh"></docen-document>
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
            <input type="text" name="note" maxlength="500" class="form-input" style="max-width:360px"
                   placeholder="{{ __('moonshine-docx-editor::ui.note') }}">
            <label style="display:flex;gap:6px;align-items:center">
                <input type="checkbox" name="requires_reaccept">
                {{ __('moonshine-docx-editor::ui.requires_reaccept') }}
            </label>
            <button type="button" class="btn btn-primary" data-docx-save>{{ __('moonshine-docx-editor::ui.save') }}</button>
            <span data-docx-status></span>
        </div>
    </div>
    <script type="module" src="{{ asset('vendor/moonshine-docx-editor/docx-editor.js') }}"></script>
@endif
