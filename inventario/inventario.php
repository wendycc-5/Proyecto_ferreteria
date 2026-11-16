<?php
// Lógica para guardar o actualizar inventario cuando se envía por AJAX
if (isset($_POST['accion_guardar_inventario'])) {
    header('Content-Type: application/json');
    
    $id_inv = intval($_POST['id_inv']);
    $id_producto = trim($conexion->real_escape_string($_POST['id_producto']));
    $cantidad_dispo = intval($_POST['cantidad_dispo']);
    $cantidad_min = intval($_POST['cantidad_min']);
    $ubicacion = trim($conexion->real_escape_string($_POST['ubicacion']));
    $id_empleado = trim($conexion->real_escape_string($_POST['id_empleado']));

    if ($id_inv <= 0 || empty($id_producto)) {
        echo json_encode(["exito" => false, "mensaje" => "Faltan campos obligatorios."]);
        exit();
    }

    $sql = "INSERT INTO inventario (id_inv, id_producto, cantidad_dispo, cantidad_min, ubicacion, id_empleado) 
            VALUES ($id_inv, '$id_producto', $cantidad_dispo, $cantidad_min, '$ubicacion', '$id_empleado')
            ON DUPLICATE KEY UPDATE 
            id_producto='$id_producto', cantidad_dispo=$cantidad_dispo, cantidad_min=$cantidad_min, ubicacion='$ubicacion', id_empleado='$id_empleado'";
    
    if ($conexion->query($sql)) {
        echo json_encode(["exito" => true]);
    } else {
        echo json_encode(["exito" => false, "mensaje" => $conexion->error]);
    }
    exit();
}

// Lógica para eliminar registro de inventario
if (isset($_GET['eliminar_inv'])) {
    $id_eliminar = intval($_GET['eliminar_inv']);
    $conexion->query("DELETE FROM inventario WHERE id_inv = $id_eliminar");
    header("Location: menuinicio.php?modulo=inventario");
    exit();
}

// Obtener registros de inventario y calcular el siguiente ID sugerido
$resultado_inv = $conexion->query("SELECT * FROM inventario ORDER BY id_inv ASC");
$res_max = $conexion->query("SELECT MAX(id_inv) as max_id FROM inventario");
$row_max = $res_max->fetch_assoc();
$siguiente_id = ($row_max['max_id']) ? intval($row_max['max_id']) + 1 : 1;
?>

<div class="card shadow-sm border-0 p-4 bg-white">
    <h3 class="fw-bold text-dark mb-3">Control de Inventario - Pinturas Tapir</h3>
    <p class="text-muted small">Gestión y control de existencias en sucursales y pasillos.</p>
    <hr>

    <!-- Tabla de Inventario con scroll interno fijo -->
    <div class="table-responsive mb-4 shadow-sm border rounded bg-white" style="max-height: 280px; overflow-y: auto; position: relative;">
        <table class="table table-bordered table-striped align-middle text-center mb-0">
            <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th>id_inv</th>
                    <th>id_producto</th>
                    <th>cantidad_dispo</th>
                    <th>cantidad_min</th>
                    <th>ubicacion</th>
                    <th>id_empleado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_inv && $resultado_inv->num_rows > 0): ?>
                    <?php while($row = $resultado_inv->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?php echo $row['id_inv']; ?></td>
                            <td><?php echo htmlspecialchars($row['id_producto']); ?></td>
                            <td><?php echo $row['cantidad_dispo']; ?></td>
                            <td><?php echo $row['cantidad_min']; ?></td>
                            <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                            <td><?php echo htmlspecialchars($row['id_empleado']); ?></td>
                            <td>
                                <a href="menuinicio.php?modulo=inventario&eliminar_inv=<?php echo $row['id_inv']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Desea eliminar este registro?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-muted py-3">No hay registros en el inventario.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Formulario Registrar / Actualizar Stock -->
    <div class="card p-4 bg-light border">
        <h5 class="fw-bold text-dark mb-1">Registrar / Actualizar Stock</h5>
        <p class="text-muted small mb-3">Complete los datos correspondientes para agregar un nuevo producto o actualizar existente</p>

        <form id="formInventario" onsubmit="guardarInventario(event)">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">ID Inv: *</label>
                    <input type="number" id="inv_id" name="id_inv" class="form-control" value="<?php echo $siguiente_id; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">ID Producto: *</label>
                    <input type="text" id="inv_producto" name="id_producto" class="form-control" placeholder="Ej. PIN-01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Cantidad Disponible: *</label>
                    <input type="number" id="inv_dispo" name="cantidad_dispo" class="form-control" placeholder="Ej. 30" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Cantidad Mínima: *</label>
                    <input type="number" id="inv_min" name="cantidad_min" class="form-control" placeholder="Ej. 5" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Ubicación: *</label>
                    <input type="text" id="inv_ubicacion" name="ubicacion" class="form-control" placeholder="Ej. Pasillo A - Estante 1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">ID Empleado: *</label>
                    <input type="text" id="inv_empleado" name="id_empleado" class="form-control" placeholder="Ej. EA2" required>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success fw-bold px-4">Guardar en Inventario</button>
                <button type="button" onclick="restablecerFormulario()" class="btn btn-warning fw-bold px-4 text-dark">Restablecer</button>
            </div>
        </form>
    </div>
</div>

<script>
function guardarInventario(e) {
    e.preventDefault();
    let formData = new FormData(document.getElementById('formInventario'));
    formData.append('accion_guardar_inventario', true);

    // Enviar petición POST directamente al archivo actual de inventario o controlador
    fetch('inventario/inventario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.exito) {
            alert("¡Registro guardado en Inventario con éxito!");
            location.reload();
        } else {
            alert("Error al guardar: " + data.mensaje);
        }
    })
    .catch(err => {
        console.error("Error de conexión:", err);
        // Respaldo por si la ruta relativa varía, intenta enviar a menuinicio
        fetch('menuinicio.php?modulo=inventario', {
            method: 'POST',
            body: formData
        }).then(() => {
            alert("Registro procesado.");
            location.reload();
        });
    });
}

function restablecerFormulario() {
    document.getElementById('formInventario').reset();
    document.getElementById('inv_id').value = "<?php echo $siguiente_id; ?>";
}
</script>