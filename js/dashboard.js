document.addEventListener("DOMContentLoaded", () => {
  const body = document.querySelector("body");
  const sidebar = document.querySelector(".sidebar");
  const toggle = document.querySelector(".sidebar .toggle");

  // Load sidebar state from localStorage
  if (localStorage.getItem("sidebarState") === "open") {
    sidebar.classList.remove("close");
  } else {
    sidebar.classList.add("close");
  }

  toggle.addEventListener("click", () => {
    sidebar.classList.toggle("close");
    // Save the sidebar state
    localStorage.setItem(
      "sidebarState",
      sidebar.classList.contains("close") ? "close" : "open"
    );
  });
});