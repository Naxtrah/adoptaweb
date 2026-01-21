<?php
require_once '../includes/config.php';
//Verificar autenticación y permisos de administrador
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

//Procesar formulario de guardar vacuna
if (isset($_POST['guardar'])) {
    //Sanitizar datos del formulario
    $nombre = sanitizar($_POST['nombre']);
    $desc = sanitizar($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    
    //Actualizar si existe ID, insertar si es nuevo
    if (!empty($_POST['id'])) {
        $pdo->prepare("UPDATE vacunas SET nombre=?, descripcion=?, precio=? WHERE id_vacuna=?")
            ->execute([$nombre,$desc,$precio,$_POST['id']]);
    } else {
        $pdo->prepare("INSERT INTO vacunas (nombre,descripcion,precio) VALUES (?,?,?)")
            ->execute([$nombre,$desc,$precio]);
    }
    header("Location: vacunas.php");
    exit;
}

//Procesar eliminación de vacuna
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM vacunas WHERE id_vacuna=?")->execute([$_GET['del']]);
    header("Location: vacunas.php");
    exit;
}

//Consultar todas las vacunas ordenadas por nombre
$vacunas = $pdo->query("SELECT * FROM vacunas ORDER BY nombre")->fetchAll();

//Cargar datos de vacuna para editar si se solicita
$vacuna_editar = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM vacunas WHERE id_vacuna = ?");
    $stmt->execute([$_GET['edit']]);
    $vacuna_editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vacunas - Admin</title>
    <!--Enlaces a librerías externas-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-syringe me-2"></i>Vacunas</h2>
            <!--Botón para abrir modal de nueva vacuna-->
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalVacuna" data-action="nuevo">
                Nueva vacuna
            </button>
            
            <!--Tabla de vacunas existentes-->
            <table class="table table-bordered">
                <thead class="table-light"><tr><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($vacunas as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['nombre']) ?></td>
                        <td><?= htmlspecialchars($v['descripcion']) ?></td>
                        <td><?= number_format($v['precio'],2) ?> €</td>
                        <td>
                            <!--Botón para editar vacuna con atributos de datos-->
                            <button class="btn btn-sm btn-warning btn-editar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalVacuna"
                                    data-id="<?= $v['id_vacuna'] ?>"
                                    data-nombre="<?= htmlspecialchars($v['nombre']) ?>"
                                    data-descripcion="<?= htmlspecialchars($v['descripcion']) ?>"
                                    data-precio="<?= $v['precio'] ?>">
                                Editar
                            </button>
                            <!--Enlace para eliminar vacuna con confirmación-->
                            <a href="?del=<?= $v['id_vacuna'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--Modal para crear/editar vacuna-->
<div class="modal fade" id="modalVacuna" tabindex="-1" aria-labelledby="modalVacunaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formVacuna">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalVacunaLabel">Nueva Vacuna</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="vacunaId" value="">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio *</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--Scripts de Bootstrap y funcionalidad personalizada-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
//Configurar interacción con modal de vacunas
document.addEventListener('DOMContentLoaded', function() {
    const modalVacuna = document.getElementById('modalVacuna');
    const btnEditar = document.querySelectorAll('.btn-editar');
    const modalTitle = document.getElementById('modalVacunaLabel');
    const form = document.getElementById('formVacuna');
    const vacunaId = document.getElementById('vacunaId');
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');
    const precioInput = document.getElementById('precio');
    
    //Configurar eventos para botones de edición
    btnEditar.forEach(btn => {
        btn.addEventListener('click', function() {
            vacunaId.value = this.getAttribute('data-id');
            nombreInput.value = this.getAttribute('data-nombre');
            descripcionInput.value = this.getAttribute('data-descripcion');
            precioInput.value = this.getAttribute('data-precio');
            modalTitle.textContent = 'Editar Vacuna';
        });
    });
    
    //Restablecer formulario cuando se cierra el modal
    modalVacuna.addEventListener('hidden.bs.modal', function() {
        form.reset();
        vacunaId.value = '';
        modalTitle.textContent = 'Nueva Vacuna';
    });
});
</script>
</body>
</html>