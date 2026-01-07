(function () {
  "use strict";

  // Crea il popup HTML
  function createPopup() {
    if (document.getElementById("posto-barca-popup")) return;

    const popupHTML = `
      <div id="posto-barca-popup" class="posto-barca-popup-overlay">
        <div class="posto-barca-popup">
          <button class="posto-barca-popup__close" aria-label="Chiudi">
            <span class="sr-only">Chiudi</span>
          </button>
          
          <div class="posto-barca-popup__content">
            <h3 class="posto-barca-popup__title">Richiedi Informazioni</h3>
            <p class="posto-barca-popup__subtitle">Compila il form per ricevere maggiori informazioni sul posto barca selezionato.</p>
            
            <div class="posto-barca-popup__form">
              ${
                postoBarcaData.shortcode ||
                "<p>Configura il Contact Form 7 nelle impostazioni.</p>"
              }
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML("beforeend", popupHTML);
  }

  // Apri popup
  function openPopup(postoBarcaName) {
    const popup = document.getElementById("posto-barca-popup");
    if (!popup) return;

    // Trova il campo del posto barca e popolalo (sia hidden che text)
    setTimeout(() => {
      const inputs = popup.querySelectorAll('input[name="posto-barca"]');
      inputs.forEach((input) => {
        input.value = postoBarcaName;
      });
    }, 100);

    popup.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  // Chiudi popup
  function closePopup() {
    const popup = document.getElementById("posto-barca-popup");
    if (!popup) return;

    popup.classList.remove("active");
    document.body.style.overflow = "";

    // Reset form dopo chiusura
    setTimeout(() => {
      const form = popup.querySelector(".wpcf7-form");
      if (form && typeof wpcf7 !== "undefined") {
        form.reset();
      }
    }, 300);
  }

  // Inizializza
  function init() {
    createPopup();

    // Event listener per chiusura
    const popup = document.getElementById("posto-barca-popup");
    if (popup) {
      const closeBtn = popup.querySelector(".posto-barca-popup__close");
      const overlay = popup;

      if (closeBtn) {
        closeBtn.addEventListener("click", closePopup);
      }

      overlay.addEventListener("click", function (e) {
        if (e.target === overlay) {
          closePopup();
        }
      });

      // ESC per chiudere
      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && popup.classList.contains("active")) {
          closePopup();
        }
      });

      // Listener per successo invio CF7
      document.addEventListener("wpcf7mailsent", function (event) {
        setTimeout(() => {
          closePopup();
        }, 2000);
      });
    }

    // Event listener per bottoni "Richiedi Info"
    document.addEventListener("click", function (e) {
      const btn = e.target.closest(".posto-barca-grid-card__cta .hov-btn");
      if (btn) {
        e.preventDefault();
        const card = btn.closest(".posto-barca-grid-card");
        const title = card
          ? card.querySelector(".posto-barca-grid-card__title")
          : null;
        const postoBarcaName = title ? title.textContent.trim() : "";

        openPopup(postoBarcaName);
      }
    });
  }

  // Avvia quando DOM è pronto
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
