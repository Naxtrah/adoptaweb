    <!--Enlaces bootstrap y js-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--Scripts específicos-->
    <?php if (isset($scripts_adicionales)) echo $scripts_adicionales; ?>
    <!--Footer contenido-->
    <footer class="bg-success text-white py-5 mt-5">
    <div class="container">
        <div class="row text-center">
            <!--Columna 1 Newsletter-->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Newsletter</h5>
                <p class="text-white-50 small mb-3">
                    Suscríbete a nuestras noticias
                </p>
                <div class="input-group mb-3 mx-auto" style="max-width: 300px;">
                    <input type="email" class="form-control" placeholder="Tu email">
                    <button class="btn btn-light" type="button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <!--Columna 2 Contacto-->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Contacto</h5>
                <p class="text-white-50 mb-1">
                    <i class="fas fa-envelope me-2"></i>info@adoptaweb.com
                </p>
                <p class="text-white-50 mb-1">
                    <i class="fas fa-phone me-2"></i>+34 900 123 456
                </p>
                <p class="text-white-50">
                    <i class="fas fa-map-marker-alt me-2"></i>Madrid, España
                </p>
            </div>
            <!--Columna 3 Legal-->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Legal</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/terminos" class="text-white-50">Términos</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/privacidad" class="text-white-50">Privacidad</a></li>
                    <li><a href="<?= BASE_URL ?>/contacto" class="text-white-50">Contacto</a></li>
                </ul>
            </div>
        </div>
        <div class="align-items-center text-center" >
                    <a class="navbar-brand mb-3" href="<?= BASE_URL ?>/">
                        <i class="fas fa-paw me-2"></i>AdoptaWeb
                    </a>
                    <div class="d-flex justify-content-center gap-4 mt-3">
                        <a href="#" class="text-white fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white fs-5"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
        <!--Apartado de copyright-->
        <div class="text-center pt-4 border-white-50">
            <p class="text-white-50 mb-0">
                &copy; 2025 AdoptaWeb. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>
</body>
</html>