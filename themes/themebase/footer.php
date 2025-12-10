<footer>
	<div class="container-fluid max-w-1360">
        <div class="row">
            <div class="col-12 col-md-5">
                <div class="footer-first-row">
                    <img src="/wp-content/uploads/2025/12/footer-logo.png" alt="Footer Logo" class="footer-logo">
                </div>
                <div class="footer-second-row">
                    <p class="footer-text">Nome Cognome: +39 339 11 22 333</p>
                    <p class="footer-text">Nome Cognome: +39 339 11 22 333</p>
                    <p class="footer-text">Nome Cognome: +39 339 11 22 333</p>
                    <p class="footer-text">info@vybayachts.com</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="footer-first-row">
                    <p>WEBSITE</p>
                </div>
                <div class="footer-second-row">
                    <div class="footer-menus">
                        <!-- Menu Footer Sinistra -->
                        <div class="footer-menu-left">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer-left',
                                'menu_class'     => 'footer-menu',
                                'container'      => false,
                                'fallback_cb'    => false,
                            ));
                            ?>
                        </div>
                        
                        <!-- Menu Footer Destra -->
                        <div class="footer-menu-right">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer-right',
                                'menu_class'     => 'footer-menu',
                                'container'      => false,
                                'fallback_cb'    => false,
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="footer-first-row">
                    <p>VYBA YACHTS</p>
                </div>
                <div class="footer-second-row">
                    <p class="footer-text">Box 27 - Marina di Cala Galera</p>
                    <p class="footer-text">Località Cala Galera, 58019 Monte Argentario</p>
                    <p class="footer-text">P.IVA: 01666350531</p>
                    <p class="footer-text">©2026 tutti i diritti riservati</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<?php wp_footer(); ?>
</body>

</html>