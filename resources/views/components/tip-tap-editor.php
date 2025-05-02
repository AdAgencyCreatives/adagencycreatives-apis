<!-- Using Remix Icon CDN -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
    .tiptap-editor-container {
        max-width: 800px;
        margin: 0 0 20px 0;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .tiptap-editor-header {
        padding: 10px 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        border-bottom: 1px solid #e2e8f0;
        background-color: #f8fafc;
    }

    .tiptap-editor-button {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 4px;
        background: transparent;
        cursor: pointer;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tiptap-editor-button:hover {
        background-color: #e2e8f0;
        color: #334155;
    }

    .tiptap-editor-button.is-active {
        background-color: #e2e8f0;
        color: #334155;
    }

    .tiptap-editor-content {
        padding: 15px;
        min-height: 200px;
        outline: none;
        flex: 1;
        overflow: auto;
    }

    .tiptap-editor-statusbar {
        padding: 8px 15px;
        font-size: 12px;
        color: #64748b;
        border-top: 1px solid #e2e8f0;
        background-color: #f8fafc;
        display: flex;
        justify-content: space-between;
    }

    .tiptap-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #334155;
    }

    /* Hidden textarea */
    .tip-tap-editor {
        display: none !important;
    }

    /* ProseMirror styling */
    .ProseMirror {
        min-height: 200px;
    }

    .ProseMirror:focus {
        outline: none;
    }

    .ProseMirror h1 {
        font-size: 2em;
        margin: 0.67em 0;
    }

    .ProseMirror h2 {
        font-size: 1.5em;
        margin: 0.75em 0;
    }

    .ProseMirror h3 {
        font-size: 1.17em;
        margin: 0.83em 0;
    }

    .ProseMirror h4 {
        font-size: 1em;
        margin: 1em 0;
    }

    .ProseMirror h5 {
        font-size: 0.83em;
        margin: 1.17em 0;
    }

    .ProseMirror h6 {
        font-size: 0.67em;
        margin: 1.33em 0;
    }

    .ProseMirror ul,
    .ProseMirror ol {
        padding: 0 1.5em;
    }

    .ProseMirror p,
    .ProseMirror p {
        margin: 0;
        padding: 0;
    }

    .ProseMirror a {
        color: #3b82f6;
        text-decoration: underline;
    }
</style>
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

    function capitalize(str) {
        if (!str) return str; // handle empty string

        if (str.length === 1) {
            return str.toUpperCase(); // capitalize single character
        }

        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    // Initialize all editors
    function initEditors() {
        document.querySelectorAll('.tip-tap-editor').forEach(textarea => {
            // Hide original textarea
            textarea.style.display = 'none';

            // Create editor container
            const container = document.createElement('div');
            container.className = 'tiptap-editor-container';
            textarea.after(container);

            // Create header with buttons
            const header = document.createElement('div');
            header.className = 'tiptap-editor-header';
            container.appendChild(header);

            // Create editor content area
            const content = document.createElement('div');
            content.className = 'tiptap-editor-content';
            container.appendChild(content);

            // Create status bar
            const statusbar = document.createElement('div');
            statusbar.className = 'tiptap-editor-statusbar';
            statusbar.innerHTML = `
                    <span class="status-hierarchy">Paragraph</span>
                    <a href="https://tiptap.dev/" target="_blank">Powered by Tiptap</a>
                `;
            container.appendChild(statusbar);

            // Initialize editor
            const editor = new Editor({
                element: content,
                extensions: [
                    StarterKit,
                    Bold,
                    Italic,
                    Underline,
                    Heading.configure({
                        levels: [1, 2, 3, 4, 5, 6],
                    }),
                    BulletList,
                    OrderedList,
                    Link.configure({
                        openOnClick: false,
                    }),
                ],
                content: textarea.value.replaceAll("<p>", "").replaceAll("</p>", "<br>").replace(/(<br>)+$/g, ""),
                onUpdate: ({
                    editor
                }) => {
                    textarea.value = editor.getHTML().replaceAll("<p>", "").replaceAll("</p>", "<br>").replace(/(<br>)+$/g, "");
                    updateStatusBar(editor, statusbar);
                },
                onSelectionUpdate: ({
                    editor
                }) => {
                    updateStatusBar(editor, statusbar);
                },
            });

            // Add toolbar buttons
            addToolbarButtons(editor, header);
        });
    }

    // Add toolbar buttons
    function addToolbarButtons(editor, container) {
        const buttons = [{
                icon: 'ri-bold',
                title: 'Bold',
                action: () => editor.chain().focus().toggleBold().run(),
                active: () => editor.isActive('bold')
            },
            {
                icon: 'ri-italic',
                title: 'Italic',
                action: () => editor.chain().focus().toggleItalic().run(),
                active: () => editor.isActive('italic')
            },
            {
                icon: 'ri-underline',
                title: 'Underline',
                action: () => editor.chain().focus().toggleUnderline().run(),
                active: () => editor.isActive('underline')
            },
            {
                icon: 'ri-h-1',
                title: 'Heading 1',
                action: () => editor.chain().focus().toggleHeading({
                    level: 1
                }).run(),
                active: () => editor.isActive('heading', {
                    level: 1
                })
            },
            {
                icon: 'ri-h-2',
                title: 'Heading 2',
                action: () => editor.chain().focus().toggleHeading({
                    level: 2
                }).run(),
                active: () => editor.isActive('heading', {
                    level: 2
                })
            },
            {
                icon: 'ri-h-3',
                title: 'Heading 3',
                action: () => editor.chain().focus().toggleHeading({
                    level: 3
                }).run(),
                active: () => editor.isActive('heading', {
                    level: 3
                })
            },
            {
                icon: 'ri-h-4',
                title: 'Heading 4',
                action: () => editor.chain().focus().toggleHeading({
                    level: 4
                }).run(),
                active: () => editor.isActive('heading', {
                    level: 4
                })
            },
            {
                icon: 'ri-h-5',
                title: 'Heading 5',
                action: () => editor.chain().focus().toggleHeading({
                    level: 5
                }).run(),
                active: () => editor.isActive('heading', {
                    level: 5
                })
            },
            {
                icon: 'ri-h-6',
                title: 'Heading 6',
                action: () => editor.chain().focus().toggleHeading({
                    level: 6
                }).run(),
                active: () => editor.isActive('heading', {
                    level: 6
                })
            },
            {
                icon: 'ri-list-unordered',
                title: 'Bullet List',
                action: () => editor.chain().focus().toggleBulletList().run(),
                active: () => editor.isActive('bulletList')
            },
            {
                icon: 'ri-list-ordered',
                title: 'Numbered List',
                action: () => editor.chain().focus().toggleOrderedList().run(),
                active: () => editor.isActive('orderedList')
            },
            {
                icon: 'ri-link',
                title: 'Link',
                action: () => {
                    const previousUrl = editor.getAttributes('link').href;
                    const url = window.prompt('URL', previousUrl);

                    if (url === null) return;

                    if (url === '') {
                        editor.chain().focus().extendMarkRange('link').unsetLink().run();
                        return;
                    }

                    editor.chain().focus().extendMarkRange('link').setLink({
                        href: url
                    }).run();
                },
                active: () => editor.isActive('link')
            },
            {
                icon: 'ri-format-clear',
                title: 'Clear Formatting',
                action: () => editor.chain().focus().clearNodes().unsetAllMarks().run()
            }
        ];

        buttons.forEach(btn => {
            const button = document.createElement('button');
            button.className = 'tiptap-editor-button';
            button.title = btn.title;
            button.type = 'button';
            button.innerHTML = `<i class="${btn.icon}"></i>`;
            button.addEventListener('click', btn.action);

            if (btn.active) {
                editor.on('transaction', () => {
                    button.classList.toggle('is-active', btn.active());
                });
            }

            container.appendChild(button);
        });
    }

    // Update status bar
    function updateStatusBar(editor, statusbar) {
        const statusElement = statusbar.querySelector('.status-hierarchy');
        let hierarchy = [];

        const {
            from,
            to
        } = editor.state.selection;

        editor.state.doc.nodesBetween(from, to, (node, pos) => {
            if (node.type.name === 'text') return;

            const nodeInfo = {
                type: node.type.name,
                attrs: node.attrs
            };

            if (node.type.name === 'heading') {
                nodeInfo.type = `Heading ${node.attrs.level}`;
            }

            hierarchy.push(nodeInfo.type);
        });

        statusElement.textContent = hierarchy.length > 0 ? hierarchy.map(item => capitalize(item)).join(' > ') :
            'Paragraph';
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', initEditors);
</script>