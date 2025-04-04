@extends('layouts.app')

@section('title', Str::ucfirst($page))

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<style>
.ql-editor {
    font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif; 
    font-size: 12pt;
    color: #000;
}
.ql-editor strong {
    font-weight: bolder;
}
.ql-toolbar.ql-snow {
    border-radius: .2rem .2rem 0 0;
}
.ql-container.ql-snow {
    border-radius:  0 0 .2rem .2rem;
}
</style>
@endsection

@section('scripts')
    <!-- <script src="https://cdn.tiny.cloud/1/niqd0bqfftqm2iti1rxdr0ddt1b46akht531kj0uv4snnaie/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
        $(document).ready(function() {
            // tinymce.init({
            //     selector: 'textarea',
            //     menubar: false,
            //     plugins: 'anchor autolink codesample emoticons link lists visualblocks image preview',
            //     toolbar: 'bold italic underline strikethrough | blocks fontfamily fontsize  | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            //     font_family_formats: 'JOST=JOST;ALTA=ALTA'
            // });


            document.querySelectorAll('.editor-container').forEach((editor, index) => {
                var textarea = document.getElementById(`editor-textarea${index}`);
                var value = textarea.value;
                
                var quill = new Quill(editor, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],        
                            [{ 'header': [1, 2, false] }],
                            [{ 'font': [] }],
                            [{ 'size': [] }],
                            ['link', 'image'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['blockquote', 'code-block'],
                            ['clean']
                        ]
                    }
                });

                // Ensure Quill is ready to insert content
                setTimeout(() => {
                    if (value) {
                        // Use quill.clipboard.dangerouslyPasteHTML to insert content
                        quill.clipboard.dangerouslyPasteHTML(0, value); // Load HTML content at the start
                    }
                }, 100); // Short delay to ensure Quill is fully initialized

                // Sync Quill content to textarea before submitting
                quill.on('text-change', () => {
                    textarea.value = quill.root.innerHTML; // Save content back to the textarea
                });
            });

        });
    </script>


@endsection

@section('styles')
    <style>
        .ck-editor__editable[role="textbox"] {
            /* editing area */
            min-height: 200px;
        }

        .ck-content .image {
            /* block images */
            max-width: 80%;
            margin: 20px auto;
        }
    </style>
@endsection
@section('content')

    @if (session('success'))
        <x-alert type="success"></x-alert>
    @endif


    <div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
        <div class="alert-message">
            <strong>Error!</strong> Please fix the following issues:
            <ul></ul>
        </div>
    </div>

    <h1 class="h3 mb-3">{{ Str::ucfirst($page) }} page</h1>
    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('pages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="page" value="{{ $page }}">
                        <div class="row">

                            <div class="col-md-12">

                                @foreach ($data as $key => $item)
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ Str::ucfirst($item->key) }}</label>
                                        <textarea class="form-control editor-textarea d-none" name="{{ $item->key }}"
                                            placeholder="Enter {{ Str::ucfirst($item->key) }}" id="editor-textarea{{$key}}">{{ $item->value }}</textarea>
                                        <div id="editor-container{{$key}}" class="editor-container"></div>
                                    </div>
                                @endforeach

                                {{-- @foreach ($data as $item)
                                        <div class="mb-3">
                                            <label for="name" class="form-label"> {{ Str::ucfirst($item->key) }}</label>
                                            <input type="text" class="form-control" name="{{ $item->key }}"
                                                value="{{ $item->value }}">
                                        </div>
                                    @endforeach --}}


                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
