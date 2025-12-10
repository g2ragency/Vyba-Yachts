document.addEventListener('DOMContentLoaded', function() {
  const heroSection = document.querySelector('.hero-section');
  const maxZoom = 100;  // Zoom iniziale in %
  const minZoom = 160;  // Zoom finale in %
  const scrollStart = heroSection.offsetTop;
  const scrollEnd = scrollStart + heroSection.offsetHeight;

  // Funzione che aggiorna la dimensione dello sfondo in tempo reale
  function zoomEffect() {
    const scrollPos = window.scrollY;  // Posizione corrente dello scroll
    const scrollRange = scrollEnd - scrollStart; // La distanza che l'utente può scrollare
    const scrollProgress = Math.min(Math.max(scrollPos - scrollStart, 0), scrollRange); // Normalizza il valore dello scroll

    // Calcoliamo la percentuale di zoom in base alla posizione dello scroll
    const zoom = maxZoom - ((scrollProgress / scrollRange) * (maxZoom - minZoom));

    // Applichiamo la nuova dimensione dello sfondo
    heroSection.style.backgroundSize = `${zoom}%`;
  }

  // Iniziamo l'animazione quando l'utente scrolla
  window.addEventListener('scroll', zoomEffect);
});
