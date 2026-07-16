const input = document.getElementById('course_thumbnail');
const preview = document.getElementById('thumbnail-preview');
const placeholder = document.querySelector('.upload-placeholder');
const actions = document.getElementById('upload-actions');

const changeButton = document.getElementById('change-thumbnail');
const removeButton = document.getElementById('remove-thumbnail');

function resetThumbnail(){
    input.value = '';

    preview.src = '';
    preview.hidden = true;

    placeholder.hidden = false;
    actions.hidden = true;
}

input.addEventListener('change', function () {

    const file = this.files[0];

    if (!file) {
        resetThumbnail();
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {

        preview.src = e.target.result;
        preview.hidden = false;

        placeholder.hidden = true;
        actions.hidden = false;

    };

    reader.readAsDataURL(file);

});

changeButton.addEventListener('click', function () {
    input.click();
});

removeButton.addEventListener('click', function () {
    resetThumbnail();
});