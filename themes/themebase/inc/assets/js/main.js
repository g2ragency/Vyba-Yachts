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
