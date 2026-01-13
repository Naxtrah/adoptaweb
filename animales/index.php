<?php
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animales para Adopción - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <!--filtros-->
            <div class="col-lg-3 col-xl-2">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-filter me-2"></i>
                            Filtros de Búsqueda
                        </h6>
                    </div>
                    <div class="card-body" id="filtros-container">
                        <!-- Los filtros se cargan por AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Cargando filtros...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Contenido principal-->
            <div class="col-lg-9 col-xl-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1">
                                    <i class="fas fa-paw text-success me-2"></i>
                                    Animales para Adopción
                                </h2>
                                <p class="text-muted mb-0">
                                    Encuentra a tu nuevo compañero
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="buscador-animales"
                                           placeholder="Buscar por nombre, especie, raza...">
                                    <button class="btn btn-success" id="btn-buscar">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" id="btn-limpiar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Resultados-->
                <div id="resultados-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Cargando animales...</span>
                        </div>
                    </div>
                </div>
                <!--Paginación-->
                <nav id="paginacion-container" class="d-none">
                    <ul class="pagination justify-content-center" id="paginacion">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let paginaActual = 1;
        let filtros = {};
        //Cargar filtros iniciales
        cargarFiltros();
        //Cargar animales iniciales
        cargarAnimales();
        //Eventos
        document.getElementById('btn-buscar').addEventListener('click', buscar);
        document.getElementById('btn-limpiar').addEventListener('click', limpiarBusqueda);
        document.getElementById('buscador-animales').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') buscar();
        });
        //Delegación de eventos para filtros
        document.getElementById('filtros-container').addEventListener('change', function(e) {
            if (e.target.matches('input[type="checkbox"], select')) {
                aplicarFiltros();
            }
        });
        //Funciones
        function cargarFiltros() {
          fetch('<?= BASE_URL ?>/animales/buscar.php?accion=filtros')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('filtros-container').innerHTML = html;
                });
        }    
        function cargarAnimales(pagina = 1) {
            paginaActual = pagina;
            mostrarCarga();        
            const params = new URLSearchParams(filtros);
            params.append('pagina', pagina);        
            fetch(`<?= BASE_URL ?>/animales/buscar.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    mostrarResultados(data.animales);
                    mostrarPaginacion(data.totalPaginas, pagina);
                });
        }  
        function buscar() {
            const busqueda = document.getElementById('buscador-animales').value.trim();
            filtros.busqueda = busqueda;
            paginaActual = 1;
            cargarAnimales();
        }    
        function limpiarBusqueda() {
            document.getElementById('buscador-animales').value = '';
            filtros.busqueda = '';
            paginaActual = 1;
            cargarAnimales();
        }    
        function aplicarFiltros() {
            const formData = new FormData(document.querySelector('#filtros-form'));
            filtros = Object.fromEntries(formData.entries());
            paginaActual = 1;
            cargarAnimales();
        }    
        function mostrarCarga() {
            document.getElementById('resultados-container').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando animales...</span>
                    </div>
                </div>
            `;
        }    
        function mostrarResultados(animales) {
            if (!animales || animales.length === 0) {
                document.getElementById('resultados-container').innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No se encontraron animales</h4>
                        <p class="text-muted mb-4">Prueba con otros filtros de búsqueda</p>
                        <button class="btn btn-success" onclick="limpiarFiltros()">
                            <i class="fas fa-times me-2"></i>Limpiar todos los filtros
                        </button>
                    </div>
                `;
                document.getElementById('paginacion-container').classList.add('d-none');
                return;
            }         
            let html = '<div class="row g-4">';      
            animales.forEach(animal => {
                const estadoClass = animal.estado === 'Disponible' ? 'success' : 
                                  animal.estado === 'Reservado' ? 'warning' : 'secondary';     
                html += `
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="../${animal.imagen_url || '<?= ASSETS_URL ?>/.img/animales/default.jpg'}" 
                                 class="card-img-top" 
                                 alt="${animal.nombre}"
                                 style="height: 200px; object-fit: cover;">
                            <span class="badge bg-${estadoClass} position-absolute top-0 end-0 m-2">
                                ${animal.estado}
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${animal.nombre}</h5>
                            <p class="card-text small">
                                <i class="fas fa-paw me-1"></i>
                                <strong>${animal.especie}</strong>
                                ${animal.raza ? ' · ' + animal.raza : ''}<br>
                                <i class="fas fa-${animal.sexo === 'Macho' ? 'mars' : 'venus'} me-1"></i>
                                ${animal.sexo} · ${animal.edad} años<br>
                                <i class="fas fa-home me-1"></i>
                                ${animal.centro_nombre}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>/animales/ver${animal.id_animal}" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i>Ver ficha
                                </a>
                                ${animal.estado === 'Disponible' ? `
                                <a href="<?= BASE_URL ?>/animales/adoptar/${animal.id_animal}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-heart me-1"></i>Adoptar
                                </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });        
            html += '</div>';
            document.getElementById('resultados-container').innerHTML = html;
            document.getElementById('paginacion-container').classList.remove('d-none');
        }    
        function mostrarPaginacion(totalPaginas, paginaActual) {
            const container = document.getElementById('paginacion');
            container.innerHTML = '';        
            if (totalPaginas <= 1) {
                document.getElementById('paginacion-container').classList.add('d-none');
                return;
            }     
            //Botón anterior
            if (paginaActual > 1) {
                container.innerHTML += `
                    <li class="page-item">
                        <button class="page-link" onclick="cargarAnimales(${paginaActual - 1})">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </li>
                `;
            } 
            //Números de página
            const inicio = Math.max(1, paginaActual - 2);
            const fin = Math.min(totalPaginas, paginaActual + 2);         
            for (let i = inicio; i <= fin; i++) {
                container.innerHTML += `
                    <li class="page-item ${i === paginaActual ? 'active' : ''}">
                        <button class="page-link" onclick="cargarAnimales(${i})">${i}</button>
                    </li>
                `;
            }          
            //Botón siguiente
            if (paginaActual < totalPaginas) {
                container.innerHTML += `
                    <li class="page-item">
                        <button class="page-link" onclick="cargarAnimales(${paginaActual + 1})">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </li>
                `;
            }
        } 
        window.cargarAnimales = cargarAnimales;
        window.limpiarFiltros = function() {
            document.querySelector('#filtros-form').reset();
            filtros = {};
            paginaActual = 1;
            cargarAnimales();
        };
    });
    </script>
</body>
</html>