// Personalizza il messaggio di errore nell'editor a blocchi
(function () {
  if (wp && wp.data && wp.hooks) {
    // Intercetta l'errore REST API e mostra messaggio personalizzato
    var originalFetch = window.fetch;

    window.fetch = function () {
      return originalFetch.apply(this, arguments).then(function (response) {
        // Clona la risposta per poterla leggere
        var clonedResponse = response.clone();

        // Se è un errore per posto_barca, controlla il messaggio
        if (
          !response.ok &&
          response.url &&
          response.url.includes("posto_barca")
        ) {
          return clonedResponse
            .json()
            .then(function (data) {
              // Se c'è il nostro messaggio personalizzato, mostralo
              if (
                data.message &&
                data.message.includes("Indicare obbligatoriamente")
              ) {
                setTimeout(function () {
                  wp.data
                    .dispatch("core/notices")
                    .createErrorNotice(data.message, {
                      id: "tipo-required",
                      isDismissible: true,
                    });
                }, 100);
              }

              // Ritorna la risposta originale
              return response;
            })
            .catch(function () {
              return response;
            });
        }

        return response;
      });
    };
  }
})();
