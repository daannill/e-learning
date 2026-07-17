(function () {
    'use strict';

    // Toolbar sengaja disederhanain — cuma yang paling sering kepake buat konten materi
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Tulis isi materi di sini...',
        modules: {
            toolbar: [
                [{ size: [] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });

    // Restore konten kalau validasi sebelumnya gagal (atau lagi buka form Edit),
    // biar teacher gak kehilangan tulisannya. Pakai dangerouslyPasteHTML(), BUKAN
    // langsung quill.root.innerHTML = ... — soalnya innerHTML langsung itu cuma
    // ngubah tampilannya doang, internal state Quill (Delta) gak ke-update, efeknya
    // undo/redo & deteksi toolbar (misal tombol Bold aktif/enggak) bisa nge-bug.
    // window.materialEditorOldContent di-set dari view lewat bootstrap kecil sebelum file ini dimuat.

    const wrapper = document.getElementById('editor-wrapper');
    const contentInput = document.getElementById('content-input');
    const contentError = document.getElementById('content-error');
    const form = document.getElementById('material-form');

    if (contentInput.value) {
        quill.clipboard.dangerouslyPasteHTML(contentInput.value);
    }

    function updateReadTimeHint() {
        const text = quill.getText().trim();

        contentInput.value = quill.root.innerHTML;
    }

    // Jalanin sekali di awal biar hint & auto-fill konsisten kalau ada konten restore-an
    updateReadTimeHint();

    quill.on('text-change', updateReadTimeHint);

    readTimeInput.addEventListener('input', () => {
        readTimeInput.dataset.manual = 'true';
    });

    quill.on('selection-change', (range) => {
        wrapper.classList.toggle('is-focused', !!range);
    });

    form.addEventListener('submit', function () {
        contentInput.value = quill.root.innerHTML;
    });
})();