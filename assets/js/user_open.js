const userButton = document.getElementById('userButton');

const dropdownMenu = document.getElementById('dropdownMenu');


userButton.addEventListener('click', () => {

    dropdownMenu.classList.toggle('show');

});

document.addEventListener('click', (e) => {

    if(
        !userButton.contains(e.target)
        &&
        !dropdownMenu.contains(e.target)
    ){

        dropdownMenu.classList.remove('show');

    }

});