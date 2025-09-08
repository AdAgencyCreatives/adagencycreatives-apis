@extends('layouts.app')

@section('title', __('NEWS Blog'))

@section('styles')
@include('pages.articles.tip-tap-editor')
@endsection

@section('scripts')
<script type="module">
    import {
        Editor
    } from 'https://esm.sh/@tiptap/core@2.1.12'
    import StarterKit from 'https://esm.sh/@tiptap/starter-kit@2.1.12'
    import Bold from 'https://esm.sh/@tiptap/extension-bold@2.1.12'
    import Italic from 'https://esm.sh/@tiptap/extension-italic@2.1.12'
    import Underline from 'https://esm.sh/@tiptap/extension-underline@2.1.12'
    import Heading from 'https://esm.sh/@tiptap/extension-heading@2.1.12'
    import BulletList from 'https://esm.sh/@tiptap/extension-bullet-list@2.1.12'
    import OrderedList from 'https://esm.sh/@tiptap/extension-ordered-list@2.1.12'
    import ListItem from 'https://esm.sh/@tiptap/extension-list-item@2.1.12'
    import Link from 'https://esm.sh/@tiptap/extension-link@2.1.12'

    class TipTapEditor {
        constructor(element, content) {
            this.element = element;
            this.content = content;
            this.init();
        }

        init() {
            // Clear the target element first
            this.element.innerHTML = '';

            // Create a container for the entire editor instance
            const editorContainer = document.createElement('div');
            editorContainer.className = 'tiptap-editor-container';

            // Create the header (toolbar)
            const header = document.createElement('div');
            header.className = 'tiptap-editor-header';
            editorContainer.appendChild(header);

            // Create the content area
            const contentArea = document.createElement('div');
            contentArea.className = 'tiptap-editor-content';
            editorContainer.appendChild(contentArea);

            // Append the complete container to the target element
            this.element.appendChild(editorContainer);

            this.editor = new Editor({
                element: contentArea,
                extensions: [
                    StarterKit,
                    Bold,
                    Italic,
                    Underline,
                    BulletList,
                    OrderedList,
                    Link.configure({
                        openOnClick: false,
                    }),
                    Heading.configure({
                        levels: [1, 2, 3, 4, 5, 6],
                    }),
                ],
                content: this.content,
            });

            this.addToolbarButtons(header);
        }

        addToolbarButtons(header) {
            const buttons = [{
                icon: 'ri-bold',
                title: 'Bold',
                action: () => this.editor.chain().focus().toggleBold().run(),
                active: () => this.editor.isActive('bold')
            }, {
                icon: 'ri-italic',
                title: 'Italic',
                action: () => this.editor.chain().focus().toggleItalic().run(),
                active: () => this.editor.isActive('italic')
            }, {
                icon: 'ri-underline',
                title: 'Underline',
                action: () => this.editor.chain().focus().toggleUnderline().run(),
                active: () => this.editor.isActive('underline')
            }, {
                icon: 'ri-list-unordered',
                title: 'Bullet List',
                action: () => this.editor.chain().focus().toggleBulletList().run(),
                active: () => this.editor.isActive('bulletList')
            }, {
                icon: 'ri-list-ordered',
                title: 'Numbered List',
                action: () => this.editor.chain().focus().toggleOrderedList().run(),
                active: () => this.editor.isActive('orderedList')
            }, {
                icon: 'ri-link',
                title: 'Link',
                action: () => {
                    const previousUrl = this.editor.getAttributes('link').href;
                    // Use a custom modal instead of window.prompt
                    showPromptModal('URL', previousUrl, (url) => {
                        if (url === null) return;
                        if (url === '') {
                            this.editor.chain().focus().extendMarkRange('link').unsetLink().run();
                            return;
                        }
                        this.editor.chain().focus().extendMarkRange('link').setLink({
                            href: url
                        }).run();
                    });
                },
                active: () => this.editor.isActive('link')
            }, {
                icon: 'ri-h-1',
                title: 'Heading 1',
                action: () => this.editor.chain().focus().toggleHeading({
                    level: 1
                }).run(),
                active: () => this.editor.isActive('heading', {
                    level: 1
                })
            }, {
                icon: 'ri-h-2',
                title: 'Heading 2',
                action: () => this.editor.chain().focus().toggleHeading({
                    level: 2
                }).run(),
                active: () => this.editor.isActive('heading', {
                    level: 2
                })
            }, {
                icon: 'ri-h-3',
                title: 'Heading 3',
                action: () => this.editor.chain().focus().toggleHeading({
                    level: 3
                }).run(),
                active: () => this.editor.isActive('heading', {
                    level: 3
                })
            }, {
                icon: 'ri-h-4',
                title: 'Heading 4',
                action: () => this.editor.chain().focus().toggleHeading({
                    level: 4
                }).run(),
                active: () => this.editor.isActive('heading', {
                    level: 4
                })
            }, {
                icon: 'ri-h-5',
                title: 'Heading 5',
                action: () => this.editor.chain().focus().toggleHeading({
                    level: 5
                }).run(),
                active: () => this.editor.isActive('heading', {
                    level: 5
                })
            }, {
                icon: 'ri-h-6',
                title: 'Heading 6',
                action: () => this.editor.chain().focus().toggleHeading({
                    level: 6
                }).run(),
                active: () => this.editor.isActive('heading', {
                    level: 6
                })
            }, {
                icon: 'ri-format-clear',
                title: 'Clear Formatting',
                action: () => this.editor.chain().focus().clearNodes().unsetAllMarks().run()
            }];

            buttons.forEach(btn => {
                const button = document.createElement('button');
                button.className = 'tiptap-editor-button';
                button.title = btn.title;
                button.type = 'button';
                button.innerHTML = `<i class="${btn.icon}"></i>`;
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    btn.action();
                });
                if (btn.active) {
                    this.editor.on('transaction', () => {
                        button.classList.toggle('is-active', btn.active());
                    });
                }
                header.appendChild(button);
            });
        }

        destroy() {
            if (this.editor) {
                this.editor.destroy();
            }
        }
    }

    window.TipTapEditor = TipTapEditor;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('/assets/js/custom.js') }}"></script>

<script>
    var currentPage = 1;
    var totalPages = 1;
    var perPage = 10;
    var filters = {};
    var editorInstance = null;
    var articleModal = new bootstrap.Modal(document.getElementById('editArticleModal'));
    var promptModal = new bootstrap.Modal(document.getElementById('promptModal'));
    var promptModalCallback = null;

    function showPromptModal(title, defaultValue, callback) {
        document.getElementById('promptModalTitle').innerText = title;
        document.getElementById('promptInput').value = defaultValue;
        promptModalCallback = callback;
        promptModal.show();
    }

    document.getElementById('promptSaveButton').addEventListener('click', () => {
        const value = document.getElementById('promptInput').value;
        if (promptModalCallback) {
            promptModalCallback(value);
        }
        promptModal.hide();
    });

    // New function to format the date
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(date);
    }

    function fetchArticles() {
        $.ajax({
            url: 'api/v1/articles',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateGroupFilter_title(response.data, '#article');
            },
            error: function() {
                // Using a custom modal/message box instead of alert()
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to fetch NEWS Blog from the API.',
                    icon: 'error'
                });
            }
        });
    }

    function populateGroupFilter_title(articles, selectId) {
        var selectElement = $(selectId);
        selectElement.empty().append('<option value="-100">Select NEWS Blog</option>');
        if (Array.isArray(articles)) {
            $.each(articles, function(index, article) {
                var option = $('<option>', {
                    value: article.uuid,
                    text: article.title
                });
                selectElement.append(option);
            });
        } else {
            console.error("The data from the API is not an array:", articles);
        }
    }

    function fetchData(page, filters = []) {
        var requestData = {
            page: page,
            per_page: perPage
        };
        var selectedArticle = $('#article option:selected').text();
        filters = {};
        if (selectedArticle != 'Select NEWS Blog') {
            filters['title'] = selectedArticle;
        }
        Object.keys(filters).forEach(function(key) {
            if (filters[key] !== '-100') {
                requestData[`filter[${key}]`] = filters[key];
            }
        });
        $.ajax({
            url: 'api/v1/articles',
            method: 'GET',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                totalPages = response.meta.last_page;
                populateTable(response.data);
                updatePaginationButtons(response.links, response.meta.links);
                updateTableInfo(response.meta);
            },
            error: function() {
                // Using a custom modal/message box instead of alert()
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to filter NEWS Blogs from the API.',
                    icon: 'error'
                });
            }
        });
    }

    function populateTable(articles) {
        var tbody = $('#articles-table tbody');
        tbody.empty();
        if (articles.length === 0) {
            displayNoRecordsMessage(7);
        }
        $.each(articles, function(index, article) {
            var roleBasedActions = '';
            roleBasedActions = '<a href="#" class="delete-article-btn" data-id="' + article.uuid + '">Delete</a>';
            var row = '<tr>' +
                '<td>' + article.id + '</td>' +
                '<td class="article-title" data-id="' + article.uuid + '" data-col="title">' + article.title + '</td>' +
                '<td class="article-sub_title" data-id="' + article.uuid + '" data-col="sub_title">' + article.sub_title + '</td>' +
                '<td class="article-article_date" data-id="' + article.uuid + '" data-col="article_date" data-original-date="' + article.article_date + '">' + formatDate(article.article_date) + '</td>' +
                // New Featured column with a class for double-clicking
                '<td class="article-is_featured" data-id="' + article.uuid + '" data-col="is_featured">' + (article.is_featured ? 'Yes' : 'No') + '</td>' +
                '<td class="article-description" data-id="' + article.uuid + '" data-col="description">' + article.description + '</td>' +
                '<td>' + roleBasedActions + '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    $(document).ready(function() {
        fetchData();
        fetchArticles();
        $(document).on('click', '.delete-article-btn', function() {
            var resourceId = $(this).data('id');
            var csrfToken = '{{ csrf_token() }}';
            deleteConfirmation(resourceId, 'article', 'articles', csrfToken);
        });
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            fetchData(currentPage);
        });

        // Corrected logic to initialize the TipTap editor
        $('table').on('dblclick', '.article-description', function() {
            var uuid = $(this).data('id');
            var currentContent = $(this).html();

            $('#article-uuid').val(uuid);

            // Destroy any existing editor instance before creating a new one
            if (editorInstance) {
                editorInstance.destroy();
            }

            // Initialize the TipTap editor on the specific div
            editorInstance = new TipTapEditor(document.getElementById('article-description-editor'), currentContent);

            articleModal.show();
        });

        $('#save-article-changes').on('click', function() {
            if (!editorInstance) {
                return;
            }
            var uuid = $('#article-uuid').val();
            var newData = editorInstance.editor.getHTML();
            var col = 'description';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                }
            });
            $.ajax({
                url: '/api/v1/articles/' + uuid,
                method: 'PUT',
                data: {
                    [col]: newData
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: "Successfully updated!",
                        icon: 'success'
                    });
                    articleModal.hide();
                    fetchData(currentPage);
                },
                error: function(error) {
                    if (error.responseJSON && error.responseJSON.errors) {
                        var errorMessages = Object.values(error.responseJSON.errors).flat().join('\n');
                        Swal.fire({
                            title: 'Validation Error',
                            text: errorMessages,
                            icon: 'error'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: "An error occurred during the update.",
                            icon: 'error'
                        });
                    }
                    fetchData(currentPage);
                }
            });
        });

        function handleOtherEdit() {
            var self = $(this);
            var uuid = self.data('id');
            var col = self.data('col');
            if (self.hasClass('editing')) {
                return;
            }
            var inputType = (col === 'article_date') ? 'date' : 'text';
            var valueToUse = (col === 'article_date') ? self.data('original-date') : self.text();
            var inputField = $('<input>', {
                type: inputType,
                value: valueToUse
            });
            self.html(inputField).addClass('editing');
            inputField.focus();
            inputField.on('blur', function() {
                var newData = $(this).val();
                saveData(uuid, col, newData, self);
                if (col === 'article_date') {
                    self.html(formatDate(newData)).removeClass('editing');
                } else {
                    self.html(newData).removeClass('editing');
                }
            });
        }

        function saveData(uuid, col, newData, targetCell) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                }
            });
            $.ajax({
                url: '/api/v1/articles/' + uuid,
                method: 'PUT',
                data: {
                    [col]: newData
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: "Successfully updated!",
                        icon: 'success'
                    });
                },
                error: function(error) {
                    if (error.responseJSON && error.responseJSON.errors) {
                        var errorMessages = Object.values(error.responseJSON.errors).flat().join('\n');
                        Swal.fire({
                            title: 'Validation Error',
                            text: errorMessages,
                            icon: 'error'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: "An error occurred during the update.",
                            icon: 'error'
                        });
                    }
                    fetchData(currentPage);
                }
            });
        }

        function handleIsFeaturedEdit() {
            var self = $(this);
            var uuid = self.data('id');
            var col = self.data('col');
            if (self.hasClass('editing')) {
                return;
            }
            var currentValue = self.text().trim();
            var selectField = $('<select>', {
                class: 'form-control',
            }).append(
                $('<option>', {
                    value: '1',
                    text: 'Yes'
                }),
                $('<option>', {
                    value: '0',
                    text: 'No'
                })
            );
            if (currentValue === 'Yes') {
                selectField.val('1');
            } else {
                selectField.val('0');
            }
            self.html(selectField).addClass('editing');
            selectField.focus();

            selectField.on('blur change', function() {
                var newData = $(this).val();
                saveData(uuid, col, newData, self);
                self.html(newData === '1' ? 'Yes' : 'No').removeClass('editing');
            });
        }

        $('table').on('dblclick', '.article-title, .article-sub_title, .article-article_date', handleOtherEdit);
        $('table').on('dblclick', '.article-is_featured', handleIsFeaturedEdit);
    });
</script>
@endsection

@section('content')
@include('pages.articles.filters')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="datatables-reponsive_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="table_length" id="table_length"><label>Show
                                    <select name="datatables-reponsive_length" id="per-page-select" class="form-select form-select-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    entries</label>
                            </div>
                        </div>
                    </div>
                    <div class="row dt-row">
                        <div class="col-sm-12">
                            <table id="articles-table" class="table table-striped dataTable no-footer dtr-inline"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Sub-Title</th>
                                        <th>Date</th>
                                        <th>Featured</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" id="table_entries_info" role="status" aria-live="polite">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="datatables-reponsive_paginate">
                                <ul class="pagination"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editArticleModal" tabindex="-1" aria-labelledby="editArticleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArticleModalLabel">Edit NEWS Blog Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-fill"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editArticleForm">
                    <input type="hidden" id="article-uuid">
                    <div class="mb-3">
                        <label for="article-description-editor" class="form-label">Description</label>
                        {{-- This is the container that will be replaced by the TipTap editor --}}
                        <div id="article-description-editor"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-article-changes">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Custom Prompt Modal -->
<div class="modal fade" id="promptModal" tabindex="-1" aria-labelledby="promptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promptModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="promptInput">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="promptSaveButton">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection