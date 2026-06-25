//MOBILE MENU & ICONS

lucide.createIcons();

document.addEventListener("DOMContentLoaded", () => {
  const mobileBtn = document.querySelector(".mobile-menu-btn");
  const closeBtn = document.querySelector(".mobile-close-btn");
  const overlay = document.getElementById("mobileMenuOverlay");

  //OPEN MENU
  if (mobileBtn && overlay) {
    mobileBtn.addEventListener("click", () => {
      overlay.classList.add("active");

      document.body.style.overflow = "hidden";
    });
  }

  //CLOSE MENU
  if (closeBtn && overlay) {
    closeBtn.addEventListener("click", () => {
      overlay.classList.remove("active");

      document.body.style.overflow = "";
    });
  }
});
