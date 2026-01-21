document.addEventListener('DOMContentLoaded', function() {
    //Preseleccionar centro si viene en parámetro URL
    const urlParams = new URLSearchParams(window.location.search);
    const centroId = urlParams.get('centro');
    
    if (centroId && (window.location.pathname.includes('/animales/'))) {
        setTimeout(() => {
            const centroSelect = document.getElementById('centro');
            if (centroSelect) {
                centroSelect.value = centroId;
                aplicarFiltros();
            }
        }, 500);
    }
    
    //Configurar eventos solo en páginas de animales
    if (window.location.pathname.includes('/animales/') || window.location.pathname.endsWith('/animales')) {
        cargarFiltros();
        cargarAnimales();
        
        //Configurar búsqueda con debounce
        const buscador = document.getElementById('buscador-animales');
        if (buscador) {
            let timeoutBusqueda;
            buscador.addEventListener('input', function(e) {
                clearTimeout(timeoutBusqueda);
                timeoutBusqueda = setTimeout(() => {
                    aplicarFiltros();
                }, 300);
            });
        }
        
        //Configurar botón limpiar filtros
        const btnLimpiar = document.getElementById('btn-limpiar');
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', limpiarFiltros);
        }
    }
});

let filters = {};
let currentPage = 1;
let debounceTimer;

//Cargar filtros dinámicamente desde servidor
function cargarFiltros() {
    fetch('buscar.php?accion=filtros')
        .then(response => response.text())
        .then(html => {
            document.getElementById('filtros-container').innerHTML = html;
            
            const formContainer = document.getElementById('filtros-form');
            if (formContainer) {
                //Configurar eventos para checkboxes
                const checkboxes = formContainer.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', aplicarFiltros);
                });
                
                //Configurar eventos para selects
                const selects = formContainer.querySelectorAll('select');
                selects.forEach(select => {
                    select.addEventListener('change', aplicarFiltros);
                });
            }
        })
        .catch(error => console.error('Error cargando filtros:', error));
}

//Aplicar filtros con debounce para evitar peticiones excesivas
function aplicarFiltros() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const formContainer = document.getElementById('filtros-form');
        const busqueda = document.getElementById('buscador-animales');
        
        filters = {};
        
        if (formContainer) {
            //Recoger filtros de especies
            const especiesCheckboxes = formContainer.querySelectorAll('input[name="especie[]"]:checked');
            if (especiesCheckboxes.length > 0) {
                filters.especie = [];
                especiesCheckboxes.forEach(cb => filters.especie.push(cb.value));
            }
            
            //Recoger filtros de sexo
            const sexosCheckboxes = formContainer.querySelectorAll('input[name="sexo[]"]:checked');
            if (sexosCheckboxes.length > 0) {
                filters.sexo = [];
                sexosCheckboxes.forEach(cb => filters.sexo.push(cb.value));
            }
            
            //Recoger filtros de estado
            const estadosCheckboxes = formContainer.querySelectorAll('input[name="estado[]"]:checked');
            if (estadosCheckboxes.length > 0) {
                filters.estado = [];
                estadosCheckboxes.forEach(cb => filters.estado.push(cb.value));
            }
            
            //Recoger filtro de edad máxima
            const edadMax = document.getElementById('edad_max');
            if (edadMax && edadMax.value) {
                filters.edad_max = edadMax.value;
            }
            
            //Recoger filtro de centro
            const centro = document.getElementById('centro');
            if (centro && centro.value) {
                filters.centro = centro.value;
            }
        }
        
        //Recoger término de búsqueda
        if (busqueda && busqueda.value.trim()) {
            filters.busqueda = busqueda.value.trim();
        }
        
        currentPage = 1;
        cargarAnimales();
    }, 200);
}

//Limpiar todos los filtros aplicados
function limpiarFiltros() {
    const formContainer = document.getElementById('filtros-form');
    if (formContainer) {
        const checkboxes = formContainer.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
        
        //Seleccionar disponible por defecto
        const disponibleCheckbox = document.getElementById('estado-disponible');
        if (disponibleCheckbox) disponibleCheckbox.checked = true;
        
        const selects = formContainer.querySelectorAll('select');
        selects.forEach(select => select.value = '');
    }
    
    const busqueda = document.getElementById('buscador-animales');
    if (busqueda) busqueda.value = '';
    
    filters = {};
    currentPage = 1;
    cargarAnimales();
}

//Cargar animales con filtros aplicados
function cargarAnimales(pagina = 1) {
    currentPage = pagina;
    const container = document.getElementById('resultados-container');
    if (!container) return;
    
    //Mostrar spinner de carga
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div></div>';
    
    //Construir parámetros para la petición
    const params = new URLSearchParams();
    params.append('pagina', currentPage);
    
    Object.entries(filters).forEach(([key, value]) => {
        if (Array.isArray(value)) {
            value.forEach(v => params.append(`${key}[]`, v));
        } else if (value) {
            params.append(key, value);
        }
    });
    
    //Realizar petición AJAX
    fetch(`buscar.php?${params.toString()}`)
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.error) {
                    throw new Error(data.error);
                }
                mostrarAnimales(data.animales);
                mostrarPaginacion(data.totalPaginas, currentPage);
            } catch (e) {
                console.error('Error parseando JSON:', e, 'Respuesta:', text);
                container.innerHTML = '<div class="alert alert-danger">Error al cargar los animales. Por favor, recarga la página.</div>';
            }
        })
        .catch(error => {
            console.error('Error cargando animales:', error);
            container.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
        });
}

//Mostrar lista de animales
function mostrarAnimales(animales) {
    const container = document.getElementById('resultados-container');
    if (!container) return;
    
    //Mensaje si no hay resultados
    if (!animales || animales.length === 0) {
        container.innerHTML = '<div class="text-center py-5"><i class="fas fa-search fa-3x text-muted mb-3"></i><h4 class="text-muted">No se encontraron animales</h4><p class="text-muted">Intenta cambiar los filtros de búsqueda</p></div>';
        return;
    }
    
    let html = '<div class="row g-4">';
    animales.forEach(animal => {
        //Determinar clase CSS según estado
        const estadoClass = animal.estado === 'Disponible' ? 'success' : 
                          animal.estado === 'Reservado' ? 'warning' : 'secondary';
        
        //Procesar imagen con fallback por defecto
        let imagen = animal.imagen_url || '../img/animales/default.jpg';
        if (imagen.startsWith('./img/')) {
            imagen = imagen.replace('./', '../');
        }
        
        html += `
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="position-relative">
                    <img src="${imagen}" 
                         class="card-img-top" 
                         alt="${animal.nombre}" 
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='../img/animales/default.jpg'">
                    <span class="badge bg-${estadoClass} position-absolute top-0 end-0 m-2">${animal.estado}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${animal.nombre}</h5>
                    <p class="card-text small">
                        <i class="fas fa-paw me-1"></i><strong>${animal.especie}</strong>
                        ${animal.raza ? ' · ' + animal.raza : ''}<br>
                        <i class="fas fa-${animal.sexo === 'Macho' ? 'mars' : 'venus'} me-1"></i>
                        ${animal.sexo} · ${animal.edad} años<br>
                        <i class="fas fa-home me-1"></i>${animal.centro_nombre || 'Sin centro'}
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

//Mostrar controles de paginación
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
    
    //Botón anterior
    if (paginaActual > 1) {
        paginacion.innerHTML += `<li class="page-item"><button class="page-link" onclick="cargarAnimales(${paginaActual - 1})"><i class="fas fa-chevron-left"></i></button></li>`;
    }
    
    //Números de página
    const inicio = Math.max(1, paginaActual - 2);
    const fin = Math.min(totalPaginas, paginaActual + 2);
    
    for (let i = inicio; i <= fin; i++) {
        paginacion.innerHTML += `<li class="page-item ${i === paginaActual ? 'active' : ''}"><button class="page-link" onclick="cargarAnimales(${i})">${i}</button></li>`;
    }
    
    //Botón siguiente
    if (paginaActual < totalPaginas) {
        paginacion.innerHTML += `<li class="page-item"><button class="page-link" onclick="cargarAnimales(${paginaActual + 1})"><i class="fas fa-chevron-right"></i></button></li>`;
    }
}

//Exponer funciones al scope global
window.cargarAnimales = cargarAnimales;
window.aplicarFiltros = aplicarFiltros;
window.limpiarFiltros = limpiarFiltros;