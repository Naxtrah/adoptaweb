<<<<<<< HEAD
//Escuchar cuando el documento HTML está completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    //Verificar si estamos en la sección de animales
    if(window.location.pathname.includes('/animales/') || window.location.pathname.endsWith('/animales')) {
        //Cargar filtros y animales al iniciar
        cargarFiltros();
        cargarAnimales();
        
        //Configurar eventos para los botones de búsqueda
        document.getElementById('btn-buscar').addEventListener('click', buscarAnimales);
        document.getElementById('btn-limpiar').addEventListener('click', limpiarBusqueda);
        
        //Permitir búsqueda con tecla Enter en el input
        document.getElementById('buscador-animales').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') buscarAnimales();
        });
    }
});

//Objeto para almacenar los filtros activos
let filters = {};
let currentPage = 1;

/* Carga los filtros disponibles desde el servidor*/
function cargarFiltros() {
    //Realizar petición para obtener los filtros
    fetch('buscar.php?accion=filtros')
        .then(response => response.text())
        .then(html => {
            //Insertar el HTML de los filtros en el contenedor
            document.getElementById('filtros-container').innerHTML = html;
        })
        .catch(error => console.error('Error cargando filtros:', error));
}

/* Aplica los filtros seleccionados por el usuario*/
function aplicarFiltros() {
    const form = document.getElementById('filtros-form');
    if(!form) return;
    
    //Crear objeto FormData para capturar todos los datos del formulario
    const formData = new FormData(form);
    filters = {}; // Reiniciar filtros
    
    //Procesamiento de cada campo del formulario
    for(let [key, value] of formData.entries()) {
        if(!filters[key]) filters[key] = [];
        if(!filters[key].includes(value)) filters[key].push(value);
    }
    
    currentPage = 1;
    cargarAnimales();
}

/* Limpia todos los filtros aplicados*/
function limpiarFiltros() {
    const form = document.getElementById('filtros-form');
    if(form) form.reset();
    filters = {};
    currentPage = 1;
    cargarAnimales();
}
/*Realiza una búsqueda por texto*/
function buscarAnimales() {
    const busqueda = document.getElementById('buscador-animales').value.trim();    
    // Agregar o eliminar filtro de búsqueda según si hay texto
    if(busqueda) {
        filters.busqueda = busqueda;
    }else{
        delete filters.busqueda;
    }
    currentPage = 1;
    cargarAnimales();
}
/* Limpia la búsqueda por texto*/
function limpiarBusqueda() {
    const input = document.getElementById('buscador-animales');
    if(input) input.value = '';
    delete filters.busqueda;
    currentPage = 1;
    cargarAnimales();
}
/**
 * Carga la lista de animales desde el servidor
 * @param {number} pagina 
 */
function cargarAnimales(pagina = 1) {
    currentPage = pagina;
    const container = document.getElementById('resultados-container');
    if(!container) return; // Salir si no hay contenedor
    
    //Mostrar spinner de carga
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div></div>';
    
    //Crear parámetros para la petición
    const params = new URLSearchParams();
    params.append('pagina', currentPage);
    
    //Agregar todos los filtros activos a los parámetros
    Object.entries(filters).forEach(([key, value]) => {
        if(Array.isArray(value)) {
            value.forEach(v => params.append(`${key}[]`, v));
        } else if (value) {
            params.append(key, value);
        }
    });    
    // Realizar petición al servidor
    fetch(`buscar.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            //Mostrar animales y paginación
            mostrarAnimales(data.animales);
            mostrarPaginacion(data.totalPaginas, currentPage);
        })
        .catch(error => {
            console.error('Error cargando animales:', error);
            container.innerHTML = '<div class="alert alert-danger">Error al cargar animales</div>';
        });
}

/**
 * Muestra los animales en el contenedor de resultados
 * @param {Array} animales
 */
function mostrarAnimales(animales) {
    const container = document.getElementById('resultados-container');
    if (!container) return;
    
    if (!animales || animales.length === 0) {
        container.innerHTML = '<div class="text-center py-5"><i class="fas fa-search fa-3x text-muted mb-3"></i><h4 class="text-muted">No se encontraron animales</h4></div>';
        return;
    }
    
    let html = '<div class="row g-4">';
    
    animales.forEach(animal => {
        const estadoClass = animal.estado === 'Disponible' ? 'success' : 'warning';
        const imagen = animal.imagen_url && animal.imagen_url.startsWith('./img/') 
            ? animal.imagen_url.replace('./', '../') 
            : (animal.imagen_url || '../img/animales/default.jpg');
        
        // IMAGEN CON LAZY LOADING
        html += `
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="position-relative" style="min-height: 200px;">
                    <!-- Placeholder mientras carga -->
                    <div class="image-placeholder" style="width: 100%; height: 200px; background: #f8f9fa; border-radius: 8px;"></div>
                    
                    <!-- Imagen con lazy loading -->
                    <img 
                        data-src="${imagen}"
                        alt="${animal.nombre}"
                        class="card-img-top lazy-image"
                        style="height: 200px; object-fit: cover; position: absolute; top: 0; left: 0; width: 100%; opacity: 0; transition: opacity 0.3s ease;"
                        onerror="this.src='../img/animales/default.jpg'; this.onerror=null;"
                    >
                    
                    <span class="badge bg-${estadoClass} position-absolute top-0 end-0 m-2">${animal.estado}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${animal.nombre}</h5>
                    <p class="card-text small">
                        <i class="fas fa-paw me-1"></i><strong>${animal.especie}</strong>
                        ${animal.raza ? ' · ' + animal.raza : ''}<br>
                        <i class="fas fa-${animal.sexo === 'Macho' ? 'mars' : 'venus'} me-1"></i>
                        ${animal.sexo} · ${animal.edad} años<br>
                        <i class="fas fa-home me-1"></i>${animal.centro_nombre}
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="d-grid gap-2">
                        <a href="ver.php?id=${animal.id_animal}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-eye me-1"></i>Ver ficha
                        </a>
                        ${animal.estado === 'Disponible' ? `<a href="adoptar.php?id=${animal.id_animal}" class="btn btn-sm btn-success"><i class="fas fa-heart me-1"></i>Adoptar</a>` : ''}
                    </div>
                </div>
            </div>
        </div>`;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Iniciar lazy loading después de insertar HTML
    iniciarLazyLoading();
}

/**
 * Muestra la paginación según el total de páginas
 * @param {number} totalPaginas - Total de páginas disponibles
 * @param {number} paginaActual - Página actual mostrada
 */
function mostrarPaginacion(totalPaginas, paginaActual) {
    const container = document.getElementById('paginacion-container');
    const paginacion = document.getElementById('paginacion');
    
    if (!container || !paginacion) return;
    
    //Ocultar paginación si solo hay una página
    if (totalPaginas <= 1) {
        container.classList.add('d-none');
        return;
    }
    
    //Mostrar contenedor y limpiar paginación anterior
    container.classList.remove('d-none');
    paginacion.innerHTML = '';
    
    //Botón "Anterior" (si no estamos en la primera página)
    if (paginaActual > 1) {
        paginacion.innerHTML += `<li class="page-item"><button class="page-link" onclick="cargarAnimales(${paginaActual - 1})"><i class="fas fa-chevron-left"></i></button></li>`;
    }
    
    //Calcular rango de páginas a mostrar (5 páginas máximo)
    const inicio = Math.max(1, paginaActual - 2);
    const fin = Math.min(totalPaginas, paginaActual + 2);
    
    //Generar botones de páginas numeradas
    for (let i = inicio; i <= fin; i++) {
        paginacion.innerHTML += `<li class="page-item ${i === paginaActual ? 'active' : ''}"><button class="page-link" onclick="cargarAnimales(${i})">${i}</button></li>`;
    }
    
    //Botón de siguiente
    if (paginaActual < totalPaginas) {
        paginacion.innerHTML += `<li class="page-item"><button class="page-link" onclick="cargarAnimales(${paginaActual + 1})"><i class="fas fa-chevron-right"></i></button></li>`;
    }
}
function iniciarLazyLoading() {
    const lazyImages = document.querySelectorAll('.lazy-image');
    
    if ('IntersectionObserver' in window) {
        // Usar IntersectionObserver (moderno)
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    cargarImagen(img);
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px', // Carga imágenes 50px antes de que entren en vista
            threshold: 0.01
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback para navegadores antiguos
        cargarImagenesVisibles();
        window.addEventListener('scroll', debounce(cargarImagenesVisibles, 200));
    }
}
function cargarImagen(img) {
    const src = img.getAttribute('data-src');
    if (!src) return;
    
    // Crear imagen fantasma para precarga
    const tempImg = new Image();
    tempImg.onload = function() {
        img.src = src;
        img.style.opacity = '1';
        img.previousElementSibling?.remove(); // Eliminar placeholder
    };
    tempImg.onerror = function() {
        img.src = '../img/animales/default.jpg';
        img.style.opacity = '1';
        img.previousElementSibling?.remove();
    };
    tempImg.src = src;
}

function cargarImagenesVisibles() {
    const lazyImages = document.querySelectorAll('.lazy-image');
    const windowHeight = window.innerHeight;
    
    lazyImages.forEach(img => {
        const rect = img.getBoundingClientRect();
        if (rect.top <= windowHeight + 100) { // 100px antes de entrar en vista
            cargarImagen(img);
        }
    });
}

// Debounce para optimizar eventos scroll
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Precarga inteligente para primeras imágenes
function precargarPrimerasImagenes() {
    // Precargar las primeras 4 imágenes inmediatamente
    const primerasImagenes = document.querySelectorAll('.lazy-image:nth-child(-n+4)');
    primerasImagenes.forEach(cargarImagen);
}
//Hacer las funciones globales para los eventos onclik
window.cargarAnimales = cargarAnimales;
window.aplicarFiltros = aplicarFiltros;
window.limpiarFiltros = limpiarFiltros;
window.buscarAnimales = buscarAnimales;
=======
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/animales/') || window.location.pathname.endsWith('/animales')) {
        cargarFiltros();
        cargarAnimales();
        
        document.getElementById('btn-buscar').addEventListener('click', buscarAnimales);
        document.getElementById('btn-limpiar').addEventListener('click', limpiarBusqueda);
        document.getElementById('buscador-animales').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') buscarAnimales();
        });
    }
});

let filters = {};
let currentPage = 1;

function cargarFiltros() {
    fetch('buscar.php?accion=filtros')
        .then(response => response.text())
        .then(html => {
            document.getElementById('filtros-container').innerHTML = html;
        })
        .catch(error => console.error('Error cargando filtros:', error));
}

function aplicarFiltros() {
    const form = document.getElementById('filtros-form');
    if (!form) return;
    
    const formData = new FormData(form);
    filters = {};
    for (let [key, value] of formData.entries()) {
        if (!filters[key]) filters[key] = [];
        if (!filters[key].includes(value)) filters[key].push(value);
    }
    currentPage = 1;
    cargarAnimales();
}

function limpiarFiltros() {
    const form = document.getElementById('filtros-form');
    if (form) form.reset();
    filters = {};
    currentPage = 1;
    cargarAnimales();
}

function buscarAnimales() {
    const busqueda = document.getElementById('buscador-animales').value.trim();
    if (busqueda) {
        filters.busqueda = busqueda;
    } else {
        delete filters.busqueda;
    }
    currentPage = 1;
    cargarAnimales();
}

function limpiarBusqueda() {
    const input = document.getElementById('buscador-animales');
    if (input) input.value = '';
    delete filters.busqueda;
    currentPage = 1;
    cargarAnimales();
}

function cargarAnimales(pagina = 1) {
    currentPage = pagina;
    const container = document.getElementById('resultados-container');
    if (!container) return;
    
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div></div>';
    
    const params = new URLSearchParams();
    params.append('pagina', currentPage);
    
    Object.entries(filters).forEach(([key, value]) => {
        if (Array.isArray(value)) {
            value.forEach(v => params.append(`${key}[]`, v));
        } else if (value) {
            params.append(key, value);
        }
    });
    
    fetch(`buscar.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            mostrarAnimales(data.animales);
            mostrarPaginacion(data.totalPaginas, currentPage);
        })
        .catch(error => {
            console.error('Error cargando animales:', error);
            container.innerHTML = '<div class="alert alert-danger">Error al cargar animales</div>';
        });
}

function mostrarAnimales(animales) {
    const container = document.getElementById('resultados-container');
    if (!container) return;
    
    if (!animales || animales.length === 0) {
        container.innerHTML = '<div class="text-center py-5"><i class="fas fa-search fa-3x text-muted mb-3"></i><h4 class="text-muted">No se encontraron animales</h4></div>';
        return;
    }
    
    let html = '<div class="row g-4">';
    animales.forEach(animal => {
        const estadoClass = animal.estado === 'Disponible' ? 'success' : 'warning';
        
        const imagen = animal.imagen_url && animal.imagen_url.startsWith('./img/') 
            ? animal.imagen_url.replace('./', '../') 
            : (animal.imagen_url || '../img/animales/default.jpg');
        
        html += `
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="position-relative">
                    <img src="${imagen}" class="card-img-top" alt="${animal.nombre}" style="height: 200px; object-fit: cover;" onerror="this.src='../img/animales/default.jpg'">
                    <span class="badge bg-${estadoClass} position-absolute top-0 end-0 m-2">${animal.estado}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${animal.nombre}</h5>
                    <p class="card-text small">
                        <i class="fas fa-paw me-1"></i><strong>${animal.especie}</strong>
                        ${animal.raza ? ' · ' + animal.raza : ''}<br>
                        <i class="fas fa-${animal.sexo === 'Macho' ? 'mars' : 'venus'} me-1"></i>
                        ${animal.sexo} · ${animal.edad} años<br>
                        <i class="fas fa-home me-1"></i>${animal.centro_nombre}
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="d-grid gap-2">
                        <a href="ver.php?id=${animal.id_animal}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-eye me-1"></i>Ver ficha
                        </a>
                        ${animal.estado === 'Disponible' ? `<a href="adoptar.php?id=${animal.id_animal}" class="btn btn-sm btn-success"><i class="fas fa-heart me-1"></i>Adoptar</a>` : ''}
                    </div>
                </div>
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

function mostrarPaginacion(totalPaginas, paginaActual) {
    const container = document.getElementById('paginacion-container');
    const paginacion = document.getElementById('paginacion');
    
    if (!container || !paginacion) return;
    
    if (totalPaginas <= 1) {
        container.classList.add('d-none');
        return;
    }
    
    container.classList.remove('d-none');
    paginacion.innerHTML = '';
    
    if (paginaActual > 1) {
        paginacion.innerHTML += `<li class="page-item"><button class="page-link" onclick="cargarAnimales(${paginaActual - 1})"><i class="fas fa-chevron-left"></i></button></li>`;
    }
    
    const inicio = Math.max(1, paginaActual - 2);
    const fin = Math.min(totalPaginas, paginaActual + 2);
    
    for (let i = inicio; i <= fin; i++) {
        paginacion.innerHTML += `<li class="page-item ${i === paginaActual ? 'active' : ''}"><button class="page-link" onclick="cargarAnimales(${i})">${i}</button></li>`;
    }
    
    if (paginaActual < totalPaginas) {
        paginacion.innerHTML += `<li class="page-item"><button class="page-link" onclick="cargarAnimales(${paginaActual + 1})"><i class="fas fa-chevron-right"></i></button></li>`;
    }
}


window.cargarAnimales = cargarAnimales;
window.aplicarFiltros = aplicarFiltros;
window.limpiarFiltros = limpiarFiltros;
window.buscarAnimales = buscarAnimales;
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
window.limpiarBusqueda = limpiarBusqueda;