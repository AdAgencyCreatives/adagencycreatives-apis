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
        line-height: 1.5;
    }

    .ProseMirror a {
        color: #3b82f6;
        text-decoration: underline;
    }

    .btn-close {
        padding: 0;
        border: none;
    }

    .btn-close i {
        font-size: 1.25rem;
        line-height: 1;
        color: #000;
    }
</style>