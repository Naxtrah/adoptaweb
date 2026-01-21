<?php
require_once 'includes/config.php';

//Consulta para obtener todos los centros
$stmt = $pdo->query("
    SELECT c.*, 
           COUNT(a.id_animal) as total_animales,
           SUM(CASE WHEN a.estado = 'Disponible' THEN 1 ELSE 0 END) as disponibles
    FROM centros c
    LEFT JOIN animales a ON c.id_centro = a.id_centro
    GROUP BY c.id_centro
    ORDER BY c.nombre
");
$centros = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Centros - AdoptaWeb</title>
    <?php include 'includes/header.php'; ?>
    <!--Librería Leaflet para mapas interactivos-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #mapa {
            height: 600px;
            border-radius: 10px;
            z-index: 1;
        }
        .centro-item {
            cursor: pointer;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            padding: 15px;
        }
        .centro-item:hover {
            background-color: #f8f9fa;
            border-left-color: #28a745;
        }
        .centro-item.active {
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        .leaflet-popup-content {
            min-width: 250px; 
        }
        .centro-marker {
            background: #28a745;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            border: 3px solid white; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <!--Lista de centros-->
            <div class="col-lg-4 col-xl-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Centros de Adopción
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="lista-centros">
                            <?php if (count($centros) > 0): ?>
                                <?php foreach ($centros as $centro): 
                                    if ($centro['latitud'] && $centro['longitud']) {
                                        $lat = $centro['latitud'];
                                        $lng = $centro['longitud'];
                                    } else {
                                        //Coordenadas aleatorias cerca de Madrid como fallback
                                        $lat = 40.4168 + (rand(-50, 50) / 1000);
                                        $lng = -3.7038 + (rand(-50, 50) / 1000);
                                    }
                                ?>
                                <div class="list-group-item centro-item" 
                                     data-id="<?= $centro['id_centro'] ?>"
                                     data-lat="<?= $lat ?>"
                                     data-lng="<?= $lng ?>"
                                     data-nombre="<?= htmlspecialchars($centro['nombre']) ?>"
                                     data-direccion="<?= htmlspecialchars($centro['direccion']) ?>"
                                     data-telefono="<?= htmlspecialchars($centro['telefono']) ?>"
                                     data-email="<?= htmlspecialchars($centro['email']) ?>"
                                     data-disponibles="<?= $centro['disponibles'] ?>"
                                     data-total="<?= $centro['total_animales'] ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="me-2">
                                            <h6 class="mb-1"><?= htmlspecialchars($centro['nombre']) ?></h6>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?= htmlspecialchars($centro['direccion']) ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>
                                                <?= htmlspecialchars($centro['telefono']) ?>
                                            </small>
                                        </div>
                                        <!--Badge con animales disponibles-->
                                        <span class="badge bg-success">
                                            <?= $centro['disponibles'] ?> disp.
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!--Mensaje si no hay centros-->
                                <div class="text-center py-4">
                                    <i class="fas fa-map-marker-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No hay centros registrados</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!--Panel informativo-->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Haz clic en un centro para ver su ubicación
                        </p>
                        <p class="small mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Los números indican animales disponibles
                        </p>
                        <p class="small mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Visita los centros para conocer a los animales
                        </p>
                        <p class="small mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Horario: Lunes-Viernes 10:00-18:00 Sábados 10:00-14:00
                        </p>
                    </div>
                </div>
            </div>
            
            <!--Mapa interactivo-->
            <div class="col-lg-8 col-xl-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-map me-2"></i>
                            Ubicación de Centros
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <!--Contenedor del mapa Leaflet-->
                        <div id="mapa"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar mapa centrado en Madrid
        const mapa = L.map('mapa').setView([40.4168, -3.7038], 12);
        
        // Capa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(mapa);
        
        // Icono personalizado para los marcadores
        const iconoCentro = L.divIcon({
            className: 'custom-div-icon',
            html: '<div class="centro-marker"><i class="fas fa-home"></i></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 40], // Punto del icono que corresponde a la ubicación
            popupAnchor: [0, -40] // Posición del popup relativa al icono
        });
        
        // Objeto para almacenar todos los marcadores
        const marcadores = {};
        
        // Crear marcadores para cada centro
        document.querySelectorAll('.centro-item').forEach(item => {
            const id = item.dataset.id;
            const lat = parseFloat(item.dataset.lat);
            const lng = parseFloat(item.dataset.lng);
            const nombre = item.dataset.nombre;
            const direccion = item.dataset.direccion;
            const telefono = item.dataset.telefono;
            const email = item.dataset.email;
            const disponibles = item.dataset.disponibles;
            const total = item.dataset.total;
            
            // Crear marcador en el mapa
            const marcador = L.marker([lat, lng], { icon: iconoCentro })
                .addTo(mapa)
                .bindPopup(`
                    <div class="p-2">
                        <h6 class="mb-2">${nombre}</h6>
                        <p class="mb-1 small">
                            <i class="fas fa-map-marker-alt text-success me-1"></i>
                            ${direccion}
                        </p>
                        <p class="mb-1 small">
                            <i class="fas fa-phone text-success me-1"></i>
                            ${telefono}
                        </p>
                        <p class="mb-1 small">
                            <i class="fas fa-envelope text-success me-1"></i>
                            ${email}
                        </p>
                        <p class="mb-2 small">
                            <i class="fas fa-paw text-success me-1"></i>
                            <strong>${disponibles}</strong> disponibles de ${total} animales
                        </p>
                        <div class="d-grid gap-2">
                            <a style="color:white"; href="<?= BASE_URL ?>/centros/ver.php?id=${id}" 
                               class="btn btn-sm btn-success">
                                Ver centro
                            </a>
                            <a class="mapa" style="color:#28a745"; href="<?= BASE_URL ?>/animales/index.php?centro=${id}" 
                               class="btn btn-sm btn-outline-success">
                                Ver animales
                            </a>
                        </div>
                    </div>
                `);
            
            // Almacenar marcador por ID
            marcadores[id] = marcador;
            
            // Evento click en el item de la lista
            item.addEventListener('click', function() {
                // Centrar mapa en el marcador
                mapa.setView([lat, lng], 15);
                marcador.openPopup();
                // Resaltar item seleccionado
                document.querySelectorAll('.centro-item').forEach(el => {
                    el.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Ajustar vista del mapa para mostrar todos los marcadores
        if (Object.keys(marcadores).length > 0) {
            const grupo = L.featureGroup(Object.values(marcadores));
            mapa.fitBounds(grupo.getBounds().pad(0.1)); // Margen del 10%
        }
        
        // Mensaje si no hay centros
        if (Object.keys(marcadores).length === 0) {
            const control = L.control({position: 'topright'});
            control.onAdd = function() {
                const div = L.DomUtil.create('div', 'info alert alert-warning');
                div.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No hay centros para mostrar';
                return div;
            };
            control.addTo(mapa);
        }
        
        // Centrar en un marcador específico si viene en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const centroId = urlParams.get('centro');
        if (centroId && marcadores[centroId]) {
            const marcador = marcadores[centroId];
            mapa.setView(marcador.getLatLng(), 15);
            marcador.openPopup();
            const item = document.querySelector(`.centro-item[data-id="${centroId}"]`);
            if (item) {
                item.classList.add('active');
            }
        }
    });
    </script>
</body>
</html>