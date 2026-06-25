//PORTFOLIO MAIN PAGE - PAGE THEME CHANGER, SCROLL DRIVEN REVEALER, CINEMATIC IMAGE LOADER, GLOBAL IMAGE ANTITHEFT

document.addEventListener("DOMContentLoaded", () => {
  
  //CURSOR FOLLOWER AND AMBIENT GLOW
  const projectItems = document.querySelectorAll(".project-item");
  const cursorPreview = document.getElementById("cursorPreview");
  const root = document.documentElement;

  //PREVENT MOUSE-TRACKING CODE ON TOUCH SCREEN DEVICES
  if (projectItems.length > 0 && cursorPreview && window.innerWidth > 900) {
    let targetX = window.innerWidth / 2;
    let targetY = window.innerHeight / 2;
    let currentX = targetX;
    let currentY = targetY;

    //CAPTURE MOUSE MOVEMENT
    window.addEventListener("mousemove", (e) => {
      targetX = e.clientX;
      targetY = e.clientY;
    });

    //POSITIONING CALCULATIONS
    function animateFollower() {
      currentX += (targetX - currentX) * 0.1;
      currentY += (targetY - currentY) * 0.1;

      cursorPreview.style.left = `${currentX}px`;
      cursorPreview.style.top = `${currentY}px`;

      requestAnimationFrame(animateFollower);
    }
    animateFollower();

    //HOVER DETECTION
    projectItems.forEach((item) => {
      item.addEventListener("mouseenter", () => {
        const coverImg = item.getAttribute("data-cover");
        const themeColor = item.getAttribute("data-theme") || "transparent";

        cursorPreview.src = coverImg;
        cursorPreview.classList.add("active");
        root.style.setProperty("--current-theme", themeColor);
      });

      item.addEventListener("mouseleave", () => {
        cursorPreview.classList.remove("active");
        root.style.setProperty("--current-theme", "transparent");
      });
    });
  }

  //SCROLL REVEAL
  const revealElements = document.querySelectorAll(".reveal-item");

  if (revealElements.length > 0) {
    const revealObserver = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      {
        root: null,
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
      },
    );

    revealElements.forEach((el) => revealObserver.observe(el));
  }

  //CINEMATIC BLUR-UP IMAGE LOADING
  const blurImages = document.querySelectorAll(".blur-load");

  blurImages.forEach((img) => {
    const highResUrl = img.getAttribute("data-src");
    if (!highResUrl) return;

    //PRELOAD HIGH-RES IMAGE IN BACKGROUND
    const highResImage = new Image();
    highResImage.src = highResUrl;

    // SWAP SOURCE AND REMOVE BLUR UPON DOWNLOAD
    highResImage.onload = () => {
      img.src = highResUrl;
      img.classList.add("loaded");
    };
  });

  //RIGHT CLICK PROTECTION - SECURITY
  document.querySelectorAll("img").forEach((img) => {
    img.addEventListener("contextmenu", (e) => e.preventDefault());
  });
});
