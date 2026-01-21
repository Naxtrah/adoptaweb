//Ejecutar cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    //Referencias a elementos de filtro
    const busqueda = document.getElementById('busqueda');
    const especie = document.getElementById('especie');
    const estado = document.getElementById('estado');
    const centro = document.getElementById('centro');

    //Variable para controlar página actual
    let currentPage = 1;

    //Función principal para cargar animales con filtros
    function cargarAnimales(pagina = 1) {
        currentPage = pagina;
        //Construir parámetros de búsqueda
        const params = new URLSearchParams();
        params.append('accion', 'buscar'); 
        params.append('pagina', currentPage);
        if (busqueda.value.trim()) params.append('busqueda', busqueda.value.trim());
        if (especie.value) params.append('especie', especie.value);
        if (estado.value) params.append('estado', estado.value);
        if (centro.value) params.append('centro', centro.value);

        //Realizar petición AJAX
        fetch(`animales.php?${params.toString()}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return res.json();
            })
            .then(data => {
                //Actualizar interfaz con resultados
                mostrarTabla(data.animales);
                mostrarPaginacion(data.totalPaginas, currentPage);
                document.getElementById('resultado-count').textContent = `Mostrando ${data.animales.length} de ${data.total}`;
            })
            .catch(err => {
                //Manejar errores
                console.error('Error:', err);
                document.getElementById('resultados-tabla').innerHTML = 
                    '<div class="alert alert-danger">Error al cargar animales. Por favor, recarga la página.</div>';
            });
    }

    //Función para mostrar resultados en tabla
    function mostrarTabla(animales) {
        let html = '';
        
        //Mensaje si no hay resultados
        if (animales.length === 0) {
            html = '<div class="alert alert-info">No se encontraron animales con los filtros seleccionados.</div>';
        } else {
            //Construir estructura de tabla
            html = '<table class="table table-hover"><thead class="table-light"><tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Especie/Raza</th><th>Edad/Sexo</th><th>Estado</th><th>Centro</th><th>Ingreso</th><th class="text-center">Acciones</th></tr></thead><tbody>';
            
            //Generar fila para cada animal
            animales.forEach(a => {
                //Procesar URL de imagen
                const imagen = a.imagen_url ? '../' + a.imagen_url.replace('./', '') : '../img/animales/default.jpg';
                
                //Determinar clase CSS según estado
                const badgeClass = {
                    'Disponible': 'badge-disponible',
                    'Reservado': 'badge-reservado',
                    'Adoptado': 'badge-adoptado',
                    'En tratamiento': 'badge-tratamiento'
                }[a.estado] || 'badge-secondary';
                
                //Plantilla de fila
                html += `
                    <tr>
                        <td>${a.id_animal}</td>
                        <td><img src="${imagen}" class="img-thumbnail" style="width:60px;height:60px;object-fit:cover;" onerror="this.src='../img/animales/default.jpg'" alt="${a.nombre}"></td>
                        <td><strong>${a.nombre}</strong></td>
                        <td><small class="text-muted">${a.especie}</small><br><small>${a.raza || 'Mestizo'}</small></td>
                        <td>${a.edad} años<br><small>${a.sexo}</small></td>
                        <td><span class="badge ${badgeClass}">${a.estado}</span></td>
                        <td><small>${a.centro_nombre}</small></td>
                        <td><small>${new Date(a.fecha_ingreso).toLocaleDateString('es-ES')}</small></td>
                        <td class="text-center">
                            <!--Botones de acción-->
                            <a href="../animales/ver.php?id=${a.id_animal}" class="btn btn-sm btn-info me-1" target="_blank" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="animales.php?accion=editar&id=${a.id_animal}" class="btn btn-sm btn-warning me-1" title="Editar"><i class="fas fa-edit"></i></a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este animal?')">
                                <input type="hidden" name="id_animal" value="${a.id_animal}">
                                <button type="submit" name="eliminar_animal" class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
        }
        
        //Insertar HTML en contenedor
        document.getElementById('resultados-tabla').innerHTML = html;
    }

    //Función para mostrar controles de paginación
    function mostrarPaginacion(totalPaginas, paginaActual) {
        const container = document.getElementById('paginacion-container');
        const paginacion = document.getElementById('paginacion');
        
        //Ocultar si solo hay una página
        if (totalPaginas <= 1) {
            container.classList.add('d-none');
            return;
        }
        
        //Mostrar contenedor
        container.classList.remove('d-none');
        paginacion.innerHTML = '';
        
        //Botón página anterior
        if (paginaActual > 1) {
            const li = document.createElement('li');
            li.className = 'page-item';
            const btn = document.createElement('button');
            btn.className = 'page-link';
            btn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            btn.addEventListener('click', () => {
                cargarAnimales(paginaActual - 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            li.appendChild(btn);
            paginacion.appendChild(li);
        }
        
        //Calcular rango de páginas a mostrar
        const inicio = Math.max(1, paginaActual - 2);
        const fin = Math.min(totalPaginas, inicio + 4);
        
        //Generar números de página
        for (let i = inicio; i <= fin; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
            const btn = document.createElement('button');
            btn.className = 'page-link';
            btn.textContent = i;
            btn.addEventListener('click', () => {
                cargarAnimales(i);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            li.appendChild(btn);
            paginacion.appendChild(li);
        }
        
        //Botón página siguiente
        if (paginaActual < totalPaginas) {
            const li = document.createElement('li');
            li.className = 'page-item';
            const btn = document.createElement('button');
            btn.className = 'page-link';
            btn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            btn.addEventListener('click', () => {
                cargarAnimales(paginaActual + 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            li.appendChild(btn);
            paginacion.appendChild(li);
        }
    }

    //Configurar eventos si existen los elementos de filtro
    if (busqueda && especie && estado && centro) {
        //Eventos para filtros en tiempo real
        busqueda.addEventListener('input', () => cargarAnimales(1));
        especie.addEventListener('change', () => cargarAnimales(1));
        estado.addEventListener('change', () => cargarAnimales(1));
        centro.addEventListener('change', () => cargarAnimales(1));

        //Cargar datos iniciales
        cargarAnimales(1);
    }
});

//Exponer función al scope global para uso externo
window.cargarAnimales = function(pagina = 1) {
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
};