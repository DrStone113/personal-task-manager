document.addEventListener('DOMContentLoaded', () => {
    const body = document.querySelector('body');
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.querySelector('.sidebar .toggle');

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('close');
    });
});
