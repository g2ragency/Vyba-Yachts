/**
 * Charter Gallery - Interactive gallery with lightbox
 */
document.addEventListener("DOMContentLoaded", function () {
  const gallerySection = document.querySelector(".charter-gallery-section");
  if (!gallerySection) return;

  const mainImage = document.querySelector(".charter-main-img");
  const mainImageContainer = document.querySelector(".charter-main-image");
  const thumbnails = document.querySelectorAll(".charter-thumb");
  const prevBtn = document.querySelector(".charter-nav-prev");
  const nextBtn = document.querySelector(".charter-nav-next");

  let currentIndex = 0;
  const totalImages = thumbnails.length;

  // Function to update main image
  function updateMainImage(index) {
    if (index < 0 || index >= totalImages) return;

    const thumb = thumbnails[index];
    const largeUrl = thumb.getAttribute("data-large");
    const fullUrl = thumb.getAttribute("data-full");

    // Update main image
    mainImage.style.opacity = "0";
    setTimeout(() => {
      mainImage.src = largeUrl;
      mainImage.setAttribute("data-full", fullUrl);
      mainImage.style.opacity = "1";
    }, 150);

    // Update active thumbnail
    thumbnails.forEach((t) => t.classList.remove("active"));
    thumb.classList.add("active");

    // Scroll thumbnail into view
    thumb.scrollIntoView({
      behavior: "smooth",
      block: "nearest",
      inline: "center",
    });

    currentIndex = index;
  }

  // Thumbnail click
  thumbnails.forEach((thumb, index) => {
    thumb.addEventListener("click", () => {
      updateMainImage(index);
    });
  });

  // Arrow navigation
  if (prevBtn) {
    prevBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      const newIndex = currentIndex > 0 ? currentIndex - 1 : totalImages - 1;
      updateMainImage(newIndex);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      const newIndex = currentIndex < totalImages - 1 ? currentIndex + 1 : 0;
      updateMainImage(newIndex);
    });
  }

  // Keyboard navigation
  document.addEventListener("keydown", (e) => {
    if (!document.querySelector(".charter-lightbox")) {
      if (e.key === "ArrowLeft") {
        const newIndex = currentIndex > 0 ? currentIndex - 1 : totalImages - 1;
        updateMainImage(newIndex);
      } else if (e.key === "ArrowRight") {
        const newIndex = currentIndex < totalImages - 1 ? currentIndex + 1 : 0;
        updateMainImage(newIndex);
      }
    }
  });

  // ==========================================
  // LIGHTBOX
  // ==========================================

  function createLightbox() {
    const lightbox = document.createElement("div");
    lightbox.className = "charter-lightbox";
    lightbox.innerHTML = `
      <div class="charter-lightbox-overlay"></div>
      <div class="charter-lightbox-content">
        <button class="charter-lightbox-close" aria-label="Chiudi">×</button>
        <button class="charter-lightbox-prev" aria-label="Precedente">
          <svg width="11" height="22" viewBox="0 0 11 22" fill="none">
            <path d="M10 1L1 11L10 21" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
        <button class="charter-lightbox-next" aria-label="Successiva">
          <svg width="11" height="22" viewBox="0 0 11 22" fill="none">
            <path d="M1 1L10 11L1 21" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
        <img class="charter-lightbox-img" src="" alt="" />
        <div class="charter-lightbox-counter"></div>
      </div>
    `;
    document.body.appendChild(lightbox);
    return lightbox;
  }

  function openLightbox(index) {
    let lightbox = document.querySelector(".charter-lightbox");
    if (!lightbox) {
      lightbox = createLightbox();
    }

    const img = lightbox.querySelector(".charter-lightbox-img");
    const counter = lightbox.querySelector(".charter-lightbox-counter");
    const closeBtn = lightbox.querySelector(".charter-lightbox-close");
    const lbPrev = lightbox.querySelector(".charter-lightbox-prev");
    const lbNext = lightbox.querySelector(".charter-lightbox-next");

    function showImage(idx) {
      if (idx < 0 || idx >= totalImages) return;
      const thumb = thumbnails[idx];
      const fullUrl = thumb.getAttribute("data-full");
      img.src = fullUrl;
      counter.textContent = `${idx + 1} / ${totalImages}`;
      currentIndex = idx;
    }

    showImage(index);
    lightbox.classList.add("active");
    document.body.style.overflow = "hidden";

    // Close
    closeBtn.onclick = () => closeLightbox();
    lightbox.querySelector(".charter-lightbox-overlay").onclick = () =>
      closeLightbox();

    // Navigation
    lbPrev.onclick = (e) => {
      e.stopPropagation();
      const newIdx = currentIndex > 0 ? currentIndex - 1 : totalImages - 1;
      showImage(newIdx);
    };

    lbNext.onclick = (e) => {
      e.stopPropagation();
      const newIdx = currentIndex < totalImages - 1 ? currentIndex + 1 : 0;
      showImage(newIdx);
    };

    // Keyboard
    const keyHandler = (e) => {
      if (e.key === "Escape") closeLightbox();
      if (e.key === "ArrowLeft") {
        const newIdx = currentIndex > 0 ? currentIndex - 1 : totalImages - 1;
        showImage(newIdx);
      }
      if (e.key === "ArrowRight") {
        const newIdx = currentIndex < totalImages - 1 ? currentIndex + 1 : 0;
        showImage(newIdx);
      }
    };
    document.addEventListener("keydown", keyHandler);

    lightbox._keyHandler = keyHandler;
  }

  function closeLightbox() {
    const lightbox = document.querySelector(".charter-lightbox");
    if (!lightbox) return;

    lightbox.classList.remove("active");
    document.body.style.overflow = "";

    if (lightbox._keyHandler) {
      document.removeEventListener("keydown", lightbox._keyHandler);
    }
  }

  // Open lightbox on main image click
  if (mainImageContainer) {
    mainImageContainer.addEventListener("click", (e) => {
      // Don't open if clicking arrow buttons
      if (e.target.closest(".charter-nav-arrow")) return;
      openLightbox(currentIndex);
    });
  }
});
