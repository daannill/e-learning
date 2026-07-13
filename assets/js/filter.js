const form = document.getElementById('filter-form');

function setStatus(status) {
    document.getElementById('status').value = status;
    form.requestSubmit();
}

const search = form.querySelector('[name="search"]');

search.addEventListener('search', function () {
    form.requestSubmit();
});

form.addEventListener('submit', function () {
    Array.from(form.elements).forEach(function (el) {
        if (el.name && el.value === '') {
            el.disabled = true;
        }
    });
});