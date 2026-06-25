//GALLERY - LAYOUT (MASONRY), FILTERS, LAZY LOADING OPTIMIZATION, MODAL 

//GLOBAL TRACKER VARIABLES
window.allImages = [];
window.filteredImages = [];
window.currentGalleryPage = 1;
window.galleryHasMore = false;
window.galleryPageSize = 24;
window.galleryIsLoading = false;
window.galleryLoadedCount = 0;
window.galleryTotalCount = 0;

//CLEAN LAYOUT FOR FILTERED DISPLAY
function clearGallery() {
  const galleryColumns = document.querySelectorAll("#gallery .collumn");
  galleryColumns.forEach((col) => (col.innerHTML = ""));
}

//1 & 2 - UPDATE UI ELEMENTS BASED ON SERVER TRACKING VARIABLES

//1 - TEXT LABELS
function updateGalleryInfo() {
  const info = document.getElementById("gallery-results-info");
  const empty = document.getElementById("gallery-empty");

  if (info) {
    if (window.galleryTotalCount > 0) {
      info.textContent = `Showing ${window.galleryLoadedCount} of ${window.galleryTotalCount} image(s)`;
    } else {
      info.textContent = "";
    }
  }

  if (empty) {
    empty.style.display =
      !window.galleryIsLoading && window.galleryLoadedCount === 0
        ? "block"
        : "none";
  }
}

//2 - LOAD MORE BUTTON
function updateLoadMoreButton() {
  const btn = document.getElementById("gallery-load-more");
  const status = document.getElementById("gallery-load-more-status");

  if (!btn) return;

  btn.style.display = window.galleryHasMore ? "inline-block" : "none";
  btn.disabled = window.galleryIsLoading;

  if (window.galleryIsLoading && window.currentGalleryPage > 1) {
    btn.innerHTML = "Loading...";
    btn.style.opacity = "0.5";
  } else {
    btn.innerHTML = "Load more";
    btn.style.opacity = "1";
  }
}

function showLoadingSpinner() {
  const el = document.getElementById("gallery-loading");
  if (el) el.style.display = "block";
}

function hideLoadingSpinner() {
  const el = document.getElementById("gallery-loading");
  if (el) el.style.display = "none";
}

//ASYNCHRONOUS FEED FECTHER 
function fetchGalleryPage(reset = false) {
  if (window.galleryIsLoading) return; //DISABLE SECONDARY CLICKS IF PAGE IS STILL LOADING

  //COMPLETE RESET FOR CATEGORY SELECTION
  if (reset) {
    window.currentGalleryPage = 1;
    window.allImages = [];
    window.filteredImages = [];
    window.galleryLoadedCount = 0;
    window.galleryTotalCount = 0;
    clearGallery();
  }

  window.galleryIsLoading = true;
  updateLoadMoreButton();
  updateGalleryInfo();

  if (window.currentGalleryPage === 1) {
    showLoadingSpinner();
  }

  //READ CHOSEN FILTERS
  const typeFilter = document.getElementById("type-filter")?.value || "";
  const resolutionFilter =
    document.getElementById("resolution-filter")?.value || "";
  const editFilter = document.getElementById("edit-filter")?.value || "";

  //APPENDS RULES INTO URL QUERY STRING STRUCTURE
  const params = new URLSearchParams();
  if (typeFilter) params.set("type", typeFilter);
  if (resolutionFilter) params.set("resolution", resolutionFilter);
  if (editFilter) params.set("edit", editFilter);
  params.set("page", window.currentGalleryPage);
  params.set("limit", window.galleryPageSize);

  //AJAX DATABASE CALL
  fetch(`${window.GALLERY_FEED_URL}?${params.toString()}`)

    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok " + response.statusText); //CATCH ERROR 
      }
      return response.json(); //TRANSLATE INTO STRUCTURED JS OBJECTS
    })

    //REPLACE MEMORY ARRAY OR MERGE ROWS
    .then((data) => {
      const items = Array.isArray(data.items) ? data.items : [];

      if (reset) {
        window.allImages = items; 
      } else {
        window.allImages = window.allImages.concat(items);
      }
      
      //SYNCHRONYZE GLOBAL VARIABLES WITH METADATA FEEDBACK NUMBERS FROM BACK-END SCRIPT
      window.filteredImages = window.allImages.slice();
      window.galleryLoadedCount = Number(data.loaded_count || 0);
      window.galleryTotalCount = Number(data.total_count || 0);
      window.galleryHasMore = !!data.has_more;

      const firstNewElement = populateGallery(items, !reset); //HAND RAW IMAGES TO MASONRY BUILDER 

      updateURL(); //BASED ON FILTER

      //VIEWPORT ADJUSTMENT MATH
      if (!reset && firstNewElement) {
        setTimeout(() => {
          const rect = firstNewElement.getBoundingClientRect();
          const targetY = window.scrollY + rect.top - 120;

          window.scrollTo({
            top: Math.max(0, targetY),
            behavior: "smooth",
          });
        }, 80);
      }
    })

    //CATCH ERRORS
    .catch((error) => {
      console.error("Error loading images:", error);
    })

    //SUCCESS FINAL STATE 
    .finally(() => {
      window.galleryIsLoading = false;

      if (window.currentGalleryPage === 1) {
        hideLoadingSpinner();
      }

      updateLoadMoreButton();
      updateGalleryInfo();
    });
}

//ASPECT RATIO MATH
function getEstimatedAspectRatio(image) {
  if (image.resolution && /^\d+x\d+$/i.test(image.resolution)) {
    const [w, h] = image.resolution.toLowerCase().split("x").map(Number);
    if (w > 0 && h > 0) {
      return h / w;
    }
  }
  return 1.5;
}

//GRID LAYOUT CONSTRUCTOR
function populateGallery(images, append = false) {
  const galleryColumns = document.querySelectorAll("#gallery .collumn");
  if (!galleryColumns.length) return null;

  if (!append) {
    galleryColumns.forEach((col) => (col.innerHTML = ""));
  }

  //HEIGHT
  const columnHeights = Array.from(galleryColumns).map(
    (col) => col.scrollHeight || 0,
  );

  //WIDTH
  const columnWidth =
    galleryColumns[0]?.clientWidth ||
    Math.max(
      250,
      Math.floor(window.innerWidth / Math.max(galleryColumns.length, 3)) - 40,
    );

  let firstNewElement = null;

  images.forEach((image, index) => {
    //BUILD WRAPPER
    const wrapper = document.createElement("div");
    wrapper.classList.add("gallery-item-wrapper");

    if (append) {
      wrapper.style.opacity = "0";
      wrapper.style.transform = "translateY(18px)";
    }

    //BUILD IMAGE
    const imgElement = document.createElement("img");
    imgElement.dataset.src = `${window.BASE_URL}/${image.src}`;
    imgElement.alt = image.title || image.type_names || "Gallery image";
    imgElement.classList.add("lazy", "loading");
    imgElement.loading = "lazy";
    imgElement.decoding = "async";

    imgElement.addEventListener("load", () => {
      imgElement.classList.remove("loading");
      imgElement.classList.add("loaded");
    });

    //QUICK ACTION GLASS PANEL
    const quickActions = document.createElement("div");
    quickActions.classList.add("item-quick-actions");

    //VIEW ACTION BUTTON
    const viewBtn = document.createElement("button");
    viewBtn.classList.add("action-btn", "view-btn");
    viewBtn.innerHTML = '<i data-lucide="eye"></i>';
    viewBtn.addEventListener("click", (e) => {
      e.stopPropagation(); //PREVENTS DUPLICATE CLICKS
      const globalIndex = window.filteredImages.findIndex(
        (item) => item.id === image.id,
      );
      if (globalIndex >= 0) openModal(globalIndex);
    });

    //BUY ACTION BUTTON
    const buyBtn = document.createElement("a");
    buyBtn.classList.add("action-btn", "buy-btn");
    buyBtn.innerHTML = '<i data-lucide="shopping-bag"></i>';
    if (image.slug) {
      buyBtn.href = `${window.BASE_URL}/pages/gallery_item.php?slug=${encodeURIComponent(image.slug)}`;
    } else {
      buyBtn.style.display = "none";
    }

    //OVERLAY DOM STRUCTURE AS CSS EXPECTS
    quickActions.appendChild(viewBtn);
    quickActions.appendChild(buyBtn);
    wrapper.appendChild(imgElement);
    wrapper.appendChild(quickActions);

    //MASONRY GRID MATH - FIND SHORTEST COLUMN
    let shortestColumnIndex = 0;
    for (let i = 1; i < columnHeights.length; i++) {
      if (columnHeights[i] < columnHeights[shortestColumnIndex]) {
        shortestColumnIndex = i;
      }
    }

    galleryColumns[shortestColumnIndex].appendChild(wrapper);

    if (append && index === 0) {
      firstNewElement = wrapper;
    }

    const estimatedHeight = columnWidth * getEstimatedAspectRatio(image);
    const spacingCompensation = 35;
    columnHeights[shortestColumnIndex] += estimatedHeight + spacingCompensation;

    if (append) {
      requestAnimationFrame(() => {
        wrapper.style.opacity = "1";
        wrapper.style.transform = "translateY(0)";
      });
    }
  });

  //OPTIMIZATION AND SECURITY
  initializeLazyLoading();
  disableRightClickOnImages();

  //LUCIDE ICONS
  if (typeof lucide !== "undefined") {
    lucide.createIcons();
  }

  return firstNewElement;
}

//RIGHT CLICK PREVENTION - SECURITY
function disableRightClickOnImages() {
  const galleryImages = document.querySelectorAll("#gallery img");
  galleryImages.forEach((img) => {
    img.addEventListener("contextmenu", (e) => {
      e.preventDefault();
    });
  });
}

//MODAL 
function openModal(index) {
  const modal = document.getElementById("modal");
  const modalImg = document.getElementById("modal-img");
  const modalBgBlur = document.querySelector(".modal-bg-blur"); 
  const images = window.filteredImages;

  if (!images[index]) return;

  //SCROLL LOCK
  document.body.classList.add("modal-open");

  modal.style.display = "flex";
  requestAnimationFrame(() => {
    modal.classList.add("show");
  });

  const imgSrc = `${window.BASE_URL}/${images[index].modal_src}`;

  //ACTUAL IMAGE
  modalImg.src = imgSrc;
  modalImg.dataset.index = index;

  //BACKGROUND IMAGE FOR COLOR EFFECT
  if (modalBgBlur) {
    modalBgBlur.style.backgroundImage = `url('${imgSrc}')`;
  }

  updateNavigation();
}

function closeModal() {
  const modal = document.getElementById("modal");
  modal.classList.remove("show");

  //UNLOCK SCROLL
  document.body.classList.remove("modal-open");

  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}

//UI VISIBILITY BASED ON ACTUAL STATE
function updateNavigation() {
  const modalImg = document.getElementById("modal-img");
  const images = window.filteredImages;
  const currentIndex = parseInt(modalImg.dataset.index, 10);

  const prevBtn = document.querySelector(".prev");
  const nextBtn = document.querySelector(".next");

  if (prevBtn) {
    prevBtn.style.display = currentIndex > 0 ? "block" : "none";
  }

  if (nextBtn) {
    nextBtn.style.display = currentIndex < images.length - 1 ? "block" : "none";
  }
}

//NAV UI FUCNTIONS
function showPrevImage() {
  const modalImg = document.getElementById("modal-img");
  const currentIndex = parseInt(modalImg.dataset.index, 10);
  if (currentIndex > 0) {
    openModal(currentIndex - 1);
  }
}

function showNextImage() {
  const modalImg = document.getElementById("modal-img");
  const currentIndex = parseInt(modalImg.dataset.index, 10);
  const images = window.filteredImages;
  if (currentIndex < images.length - 1) {
    openModal(currentIndex + 1);
  }
}

//FILTERS
function applyFilters() {
  window.currentGalleryPage = 1;
  fetchGalleryPage(true);
}

function attachFilterListeners() {
  document
    .getElementById("type-filter")
    ?.addEventListener("change", applyFilters);
  document
    .getElementById("resolution-filter")
    ?.addEventListener("change", applyFilters);
  document
    .getElementById("edit-filter")
    ?.addEventListener("change", applyFilters);

  document
    .getElementById("gallery-load-more")
    ?.addEventListener("click", () => {
      if (!window.galleryHasMore || window.galleryIsLoading) return;
      window.currentGalleryPage += 1;
      fetchGalleryPage(false);
    });
}

//LAZY LOADING IMPLEMENTATION - OPTIMIZATION
function initializeLazyLoading() {
  const lazyImages = document.querySelectorAll(".lazy");

  if ("IntersectionObserver" in window) {
    const lazyLoad = (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove("lazy");
          observer.unobserve(img);
        }
      });
    };

    const observer = new IntersectionObserver(lazyLoad, {
      rootMargin: "200px 0px",
      threshold: 0.01,
    });

    lazyImages.forEach((img) => observer.observe(img));
  } else {
    lazyImages.forEach((img) => {
      img.src = img.dataset.src;
      img.classList.remove("lazy");
      img.classList.remove("loading");
    });
  }
}

//URL SYNCHRONIZATION
function updateURL() {
  const typeFilter = document.getElementById("type-filter")?.value || "";
  const resolutionFilter =
    document.getElementById("resolution-filter")?.value || "";
  const editFilter = document.getElementById("edit-filter")?.value || "";

  const urlParams = new URLSearchParams();

  if (typeFilter) urlParams.set("type", typeFilter);
  if (resolutionFilter) urlParams.set("resolution", resolutionFilter);
  if (editFilter) urlParams.set("edit", editFilter);

  const newQuery = urlParams.toString();
  const newURL = newQuery
    ? `${window.location.pathname}?${newQuery}`
    : window.location.pathname;

  history.replaceState(null, "", newURL);
}

//URL LINK READER
function applyFiltersFromURL() {
  const urlParams = new URLSearchParams(window.location.search);

  const typeFilter = urlParams.get("type") || "";
  const resolutionFilter = urlParams.get("resolution") || "";
  const editFilter = urlParams.get("edit") || "";

  const typeEl = document.getElementById("type-filter");
  const resEl = document.getElementById("resolution-filter");
  const editEl = document.getElementById("edit-filter");

  if (typeEl) typeEl.value = typeFilter;
  if (resEl) resEl.value = resolutionFilter;
  if (editEl) editEl.value = editFilter;

  fetchGalleryPage(true);
}

//DOCUMENT EVENTS
document.querySelector(".close")?.addEventListener("click", closeModal);
document.querySelector(".prev")?.addEventListener("click", showPrevImage);
document.querySelector(".next")?.addEventListener("click", showNextImage);

window.addEventListener("load", () => {
  attachFilterListeners();
  applyFiltersFromURL();
});
