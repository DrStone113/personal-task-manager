// Menu
const menu = document.querySelector(".navbar_links");
const menuButton = document.querySelector(".navbar_icons");

menuButton.addEventListener("click", () => {
    menu.classList.toggle("navbar_open");
    menuButton.classList.toggle("open");
});
