    <!--Enlaces bootstrap y js-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--Javascript adicional-->
    <?php if (isset($scripts_adicionales)) echo $scripts_adicionales; ?>
    <!--Footer contenido-->
<footer class="bg-success text-white py-5 mt-5">
    <div class="container">
        <div class="row text-center text-md-start">
            
            <!--Newsletter-->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="mb-3">Newsletter</h5>
                <p class="text-white-50 small mb-3">
                    Suscríbete a nuestras noticias y recibe novedades sobre adopciones
                </p>
                <form id="newsletter-form" class="mx-auto mx-md-0" style="max-width: 320px;">
                    <div class="input-group">
                        <input type="email" class="form-control" id="newsletter-email" placeholder="Tu email" required>
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div id="newsletter-message" class="small mt-2"></div>
                </form>
            </div>

            <!--Contacto-->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="mb-3">Contacto</h5>
                <p class="text-white-50 mb-2">
                    <i class="fas fa-envelope me-2"></i>
                    <a href="mailto:info@adoptaweb.com" class="text-white-50">info@adoptaweb.com</a>
                </p>
                <p class="text-white-50 mb-2">
                    <i class="fas fa-phone me-2"></i>
                    <a href="tel:+34900123456" class="text-white-50">+34 900 123 456</a>
                </p>
                <p class="text-white-50 mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>Madrid, España
                </p>

                <a target="_blank" href="<?= BASE_URL ?>/contacto.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-envelope me-1"></i>Formulario de contacto
                </a>
            </div>

            <!--Legal-->
            <div class="col-lg-4 col-md-12">
                <h5 class="mb-3">Legal</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/terminos.php" class="text-white-50">
                            <i class="fas fa-file-contract me-1"></i>Términos y condiciones
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/privacidad.php" class="text-white-50">
                            <i class="fas fa-user-shield me-1"></i>Política de privacidad
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/contacto.php" class="text-white-50">
                            <i class="fas fa-headset me-1"></i>Contacto y soporte
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/includes/faq.php" class="text-white-50">
                            <i class="fas fa-question-circle me-1"></i>Preguntas frecuentes
                        </a>
                    </li>
                </ul>

                <div class="mt-4">
                    <h6 class="mb-2">Síguenos</h6>
                    <div class="d-flex justify-content-center justify-content-md-start gap-3">
                        <a target="_blank" href="https://www.instagram.com" class="text-white fs-5">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a target="_blank" href="https://x.com" class="text-white fs-5">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a target="_blank" href="https://youtube.com" class="text-white fs-5">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a target="_blank" href="https://facebook.com" class="text-white fs-5">
                            <i class="fab fa-facebook"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!--Parte inferior-->
        <div class="text-center pt-4 mt-4 border-top border-white-50">
            <a class="navbar-brand d-inline-flex align-items-center mb-2 text-white" href="<?= BASE_URL ?>/">
                <i class="fas fa-paw me-2"></i>AdoptaWeb
            </a>
            <p class="text-white-50 small mb-0">
                &copy; 2025 AdoptaWeb. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>
<!--Código para la aceptación de las cookies de la página-->
    <?php if (isset($_SESSION['show_cookie_banner'])): ?>
    <div class="cookie-banner fixed-bottom bg-dark text-white p-3" id="cookieBanner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-0">
                        <i class="fas fa-cookie-bite me-2"></i>
                        Utilizamos cookies propias y de terceros para mejorar tu experiencia. 
                        <a href="<?= BASE_URL ?>/privacidad.php" class="text-warning">Política de cookies</a>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <button class="btn btn-success btn-sm me-2" onclick="acceptCookies()">
                        <i class="fas fa-check me-1"></i>Aceptar todas
                    </button>
                    <button class="btn btn-outline-light btn-sm" onclick="rejectCookies()">
                        <i class="fas fa-times me-1"></i>Rechazar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script>
        /*Debida gestión de cookies según lo que decida el usuario*/
    function acceptCookies() {
        fetch('<?= BASE_URL ?>/includes/cookies.php?action=accept')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const banner = document.getElementById('cookieBanner');
                    if (banner) {
                        banner.style.transition = 'opacity 0.5s ease';
                        banner.style.opacity = '0';
                        setTimeout(() => banner.remove(), 500);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
    function rejectCookies() {
        fetch('<?= BASE_URL ?>/includes/cookies.php?action=reject')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const banner = document.getElementById('cookieBanner');
                    if (banner) {
                        banner.style.transition = 'opacity 0.5s ease';
                        banner.style.opacity = '0';
                        setTimeout(() => banner.remove(), 500);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const newsletterForm = document.getElementById('newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = document.getElementById('newsletter-email').value;
                const messageDiv = document.getElementById('newsletter-message');
                /*Si el correo no es válido*/
                if (!validateEmail(email)) {
                    showMessage(messageDiv, 'Email no válido', 'warning');
                    return;
                }
                showMessage(messageDiv, 'Procesando...', 'light');
                
                fetch('<?= BASE_URL ?>/includes/newsletter.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email)
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(messageDiv, '¡Gracias por suscribirte!', 'light');
                        document.getElementById('newsletter-email').value = '';
                        setTimeout(() => messageDiv.innerHTML = '', 3000);
                    } else {
                        showMessage(messageDiv, data.message, 'warning');
                    }
                })
                .catch(error => {
                    showMessage(messageDiv, 'Error de conexión', 'warning');
                });
            });
        }
    });
    /*Función para validad email*/
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    function showMessage(element, text, type) {
        if (!element) return;
        const colors = {
            'warning': 'text-warning',
            'light': 'text-light',
            'danger': 'text-danger'
        };
        element.innerHTML = `<span class="${colors[type] || 'text-light'}">${text}</span>`;
    }
    </script>
</body>
</html>