
document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', (e) => {
        const el = e.target.closest('.confirm-delete');
        if (el && !confirm(el.dataset.confirm || 'Confirmer la suppression ?')) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});

function setupImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('change', () => {
        const file = input.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
}
