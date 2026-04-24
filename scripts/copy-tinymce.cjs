/* Sync self-hosted TinyMCE from node_modules into public (no API key). Run via npm postinstall. */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const src = path.join(root, 'node_modules', 'tinymce');
const dest = path.join(root, 'public', 'vendor', 'tinymce');

if (!fs.existsSync(src)) {
    console.warn('[copy-tinymce] node_modules/tinymce not found — skip (run npm install).');
    process.exit(0);
}

fs.rmSync(dest, { recursive: true, force: true });
fs.mkdirSync(path.dirname(dest), { recursive: true });
fs.cpSync(src, dest, { recursive: true });
console.log('[copy-tinymce] synced to public/vendor/tinymce');
