document.addEventListener("DOMContentLoaded", function () {
  const heroSection = document.querySelector(".hero-section");

  if (!heroSection) return;

  const minZoom = 1.0; // Zoom iniziale (1 = 100%)
  const maxZoom = 1.6; // Zoom finale (1.6 = 160%)
  const scrollStart = heroSection.offsetTop;
  const scrollEnd = scrollStart + heroSection.offsetHeight;

  // Trova il video o l'immagine di sfondo
  const videoElement = heroSection.querySelector("video");
  const hasVideo = !!videoElement;

  // Funzione che aggiorna lo zoom in tempo reale
  function zoomEffect() {
    const scrollPos = window.scrollY;
    const scrollRange = scrollEnd - scrollStart;
    const scrollProgress = Math.min(
      Math.max(scrollPos - scrollStart, 0),
      scrollRange,
    );

    // Calcoliamo lo zoom in base alla posizione dello scroll
    const zoom = minZoom + (scrollProgress / scrollRange) * (maxZoom - minZoom);

    if (hasVideo && videoElement) {
      // Per il video usiamo transform scale
      videoElement.style.transform = `scale(${zoom})`;
    } else {
      // Per l'immagine di sfondo usiamo backgroundSize
      const zoomPercent = zoom * 100;
      heroSection.style.backgroundSize = `${zoomPercent}%`;
    }
  }

  // Iniziamo l'animazione quando l'utente scrolla
  window.addEventListener("scroll", zoomEffect);

  // Chiamiamo una volta all'inizio
  zoomEffect();
});

// Mobile button click animation
document.addEventListener("DOMContentLoaded", function () {
  if (window.innerWidth <= 992) {
    const buttons = document.querySelectorAll(".hov-btn.learn-more");

    buttons.forEach((button) => {
      button.addEventListener("touchstart", function (e) {
        this.classList.add("clicked");
      });

      button.addEventListener("touchend", function (e) {
        const self = this;
        setTimeout(function () {
          self.classList.remove("clicked");
        }, 300);
      });

      button.addEventListener("click", function (e) {
        if (window.innerWidth <= 992) {
          this.classList.add("clicked");
          const self = this;
          setTimeout(function () {
            self.classList.remove("clicked");
          }, 300);
        }
      });
    });
  }
});
