<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
    class="relative z-0"
>
    <div x-data="{ state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }}, initialized: false, editorChanged: false }"
        x-init="(() => {
            window.addEventListener('DOMContentLoaded', () => initCKEditor())
            $nextTick(() => initCKEditor())

            const initCKEditor = () => {
                if(initialized) return

                if(typeof CKEDITOR === undefined || !$refs.ckeditor) {
                    console.error('[CKEDITOR] not found or [CKEDITOR element] not found')
                    return
                }

                CKEDITOR.ClassicEditor.create($refs.ckeditor, {
                    toolbar: {
                        items: [
                            'exportPDF',
                            'exportWord',
                            '|',
                            'findAndReplace',
                            'selectAll',
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'strikethrough',
                            'underline',
                            'code',
                            'subscript',
                            'superscript',
                            'removeFormat',
                            '|',
                            'bulletedList',
                            'numberedList',
                            'outdent',
                            'indent',
                            '|',
                            'undo',
                            'redo',
                            'fontSize',
                            'fontFamily',
                            'fontColor',
                            'fontBackgroundColor',
                            'highlight',
                            '|',
                            'alignment',
                            'link',
                            'insertImage',
                            'blockQuote',
                            'insertTable',
                            'mediaEmbed',
                            'codeBlock',
                            'htmlEmbed',
                            'specialCharacters',
                            'horizontalLine',
                            'pageBreak',
                            '|',
                            'sourceEditing',
                        ],
                        shouldNotGroupWhenFull: true,
                    },
                    list: {
                        properties: {
                            styles: true,
                            startIndex: true,
                            reversed: true,
                        },
                    },
                    heading: {
                        options: [
                            {
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph',
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1',
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2',
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3',
                            },
                            {
                                model: 'heading4',
                                view: 'h4',
                                title: 'Heading 4',
                                class: 'ck-heading_heading4',
                            },
                            {
                                model: 'heading5',
                                view: 'h5',
                                title: 'Heading 5',
                                class: 'ck-heading_heading5',
                            },
                            {
                                model: 'heading6',
                                view: 'h6',
                                title: 'Heading 6',
                                class: 'ck-heading_heading6',
                            },
                        ],
                    },
                    placeholder: '. . .',
                    fontFamily: {
                        options: [
                            'default',
                            'Arial, Helvetica, sans-serif',
                            'Courier New, Courier, monospace',
                            'Georgia, serif',
                            'Lucida Sans Unicode, Lucida Grande, sans-serif',
                            'Tahoma, Geneva, sans-serif',
                            'Times New Roman, Times, serif',
                            'Trebuchet MS, Helvetica, sans-serif',
                            'Verdana, Geneva, sans-serif',
                            'Montserrat, sans-serif'
                        ],
                        supportAllValues: true,
                    },
                    htmlSupport: {
                        allow: [
                            {
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true,
                            },
                        ],
                    },
                    htmlEmbed: {
                        showPreviews: true,
                    },
                    link: {
                        decorators: {
                            addTargetToExternalLinks: true,
                            defaultProtocol: 'https://',
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file',
                                },
                            },
                        },
                    },
                    removePlugins: [
                        'CKBox',
                        'CKFinder',
                        'EasyImage',
                        'RealTimeCollaborativeComments',
                        'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory',
                        'PresenceList',
                        'Comments',
                        'TrackChanges',
                        'TrackChangesData',
                        'RevisionHistory',
                        'Pagination',
                        'WProofreader',
                        'MathType',
                    ],
                }).then(editor => {
                    if(state) editor.setData(state)

                    editor.model.document.on('change:data', () => { editorChanged = true })

                    editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                        if(isFocused || !editorChanged) return

                        state = editor.getData()
                        editorChanged = false
                    })
                });

                initialized = true
            }
        })()"
        x-cloak
        wire:ignore
    >
        @unless($isDisabled())
            <input
                id="ck-editor-{{ $getId() }}"
                type="hidden"
                x-ref="ckeditor"
                placeholder="{{ $getPlaceholder() }}"
            >
        @else
            <div
                x-html="state"
                style="font-size: 13px"
                @class([
                    'prose ck-content block w-full max-w-none rounded-lg border border-gray-300 bg-white p-3 opacity-70 shadow-xs transition duration-75',
                    'dark:prose-invert dark:bg-gray-700 dark:border-gray-600 dark:text-white' => true,
                ])
            ></div>
        @endunless
    </div>
</x-dynamic-component>

@once
    @push('scripts')
        <script src="{{ asset('assets/vendor/css/ckeditor/ckeditor.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fonts.css') }}">
    @endpush
@endonce
