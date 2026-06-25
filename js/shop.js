//ASYNCHRONOUS PRODUCT FILTERING

document.addEventListener("DOMContentLoaded", () => {
  const navWrapper = document.getElementById("categoryNav");
  const gridContainer = document.getElementById("productGridContainer");

  if (!navWrapper || !gridContainer) return;

  //INTERCEPT CATEGORY CLICK
  navWrapper.addEventListener("click", async (e) => {
    const btn = e.target.closest(".pill-btn");
    if (!btn || btn.classList.contains("active")) return;

    //TAB SELECTOR
    document
      .querySelectorAll(".pill-btn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");
    gridContainer.classList.add("loading");

    const categoryId = btn.dataset.categoryId;
    const currentUrl = new URL(window.location.href);

    // UPDATE URL QUERY PARAMETERS
    if (categoryId === "0") {
      currentUrl.searchParams.delete("category");
    } else {
      currentUrl.searchParams.set("category", categoryId);
    }

    //EXECUTE THE ASYNCHRONOUS BACKGROUND REQUEST 
    try {
      const response = await fetch(currentUrl.toString(), {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) throw new Error("Network response was not ok");

      const html = await response.text();

      //INJECT CONTENT & REFRESH INTERFACE GRAPHICS
      gridContainer.innerHTML = html;

      if (typeof lucide !== "undefined") {
        lucide.createIcons();
      }

      window.history.pushState(
        { path: currentUrl.toString() },
        "",
        currentUrl.toString(),
      );

    //ERROR CATCHING 
    } catch (error) {
      console.error("Error fetching products:", error);
      gridContainer.innerHTML =
        '<div class="empty-state glass-panel"><p>Error loading collections. Please refresh.</p></div>';
    } finally {
      gridContainer.classList.remove("loading");
    }
  });

  //BACK/FORWARD EVENTS
  window.addEventListener("popstate", () => {
    window.location.reload();
  });
});
