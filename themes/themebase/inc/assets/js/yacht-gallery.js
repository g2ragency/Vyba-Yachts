/**
 * Yacht Gallery - Interactive gallery with lightbox
 */
document.addEventListener("DOMContentLoaded", function () {
  const gallerySection = document.querySelector(".yacht-gallery-section");
  if (!gallerySection) return;

  const mainImage = document.querySelector(".yacht-main-img");
  const mainImageContainer = document.querySelector(".yacht-main-image");
  const thumbnails = document.querySelectorAll(".yacht-thumb");
  const prevBtn = document.querySelector(".yacht-nav-prev");
  const nextBtn = document.querySelector(".yacht-nav-next");

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
    if (!document.querySelector(".yacht-lightbox")) {
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
    lightbox.className = "yacht-lightbox";
    lightbox.innerHTML = `
      <div class="yacht-lightbox-overlay"></div>
      <div class="yacht-lightbox-content">
        <button class="yacht-lightbox-close" aria-label="Chiudi">×</button>
        <button class="yacht-lightbox-prev" aria-label="Precedente">
          <svg width="11" height="22" viewBox="0 0 11 22" fill="none">
            <path d="M10 1L1 11L10 21" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
        <button class="yacht-lightbox-next" aria-label="Successiva">
          <svg width="11" height="22" viewBox="0 0 11 22" fill="none">
            <path d="M1 1L10 11L1 21" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
        <img class="yacht-lightbox-img" src="" alt="" />
        <div class="yacht-lightbox-counter"></div>
      </div>
    `;
    document.body.appendChild(lightbox);
    return lightbox;
  }

  function openLightbox(index) {
    let lightbox = document.querySelector(".yacht-lightbox");
    if (!lightbox) {
      lightbox = createLightbox();
    }

    const img = lightbox.querySelector(".yacht-lightbox-img");
    const counter = lightbox.querySelector(".yacht-lightbox-counter");
    const closeBtn = lightbox.querySelector(".yacht-lightbox-close");
    const lbPrev = lightbox.querySelector(".yacht-lightbox-prev");
    const lbNext = lightbox.querySelector(".yacht-lightbox-next");

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
    lightbox.querySelector(".yacht-lightbox-overlay").onclick = () =>
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
    const lightbox = document.querySelector(".yacht-lightbox");
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
      if (e.target.closest(".yacht-nav-arrow")) return;
      openLightbox(currentIndex);
    });
  }
});
