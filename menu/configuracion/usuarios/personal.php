<?php
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_empleado'])) {
    $id_empleado  = $conexion->real_escape_string($_POST['id_empleado'] ?? '');
    $nombre       = $conexion->real_escape_string($_POST['nombre'] ?? '');
    $telefono     = $conexion->real_escape_string($_POST['telefono'] ?? '');
    $correo       = $conexion->real_escape_string($_POST['correo'] ?? '');
    $contrasena   = password_hash($_POST['contrasena'] ?? '', PASSWORD_DEFAULT);
    $rol          = $conexion->real_escape_string($_POST['rol'] ?? '');
    $genero       = $conexion->real_escape_string($_POST['genero'] ?? '');
    $permisos     = isset($_POST['permisos']) ? $conexion->real_escape_string(implode(', ', $_POST['permisos'])) : '';
    $fecha_inicio = $conexion->real_escape_string($_POST['fecha_inicio'] ?? '');
    $hora_comida  = $conexion->real_escape_string($_POST['hora_comida'] ?? '');
    $direccion    = $conexion->real_escape_string($_POST['direccion'] ?? '');
    $edad    = $conexion->real_escape_string($_POST['edad'] ?? '');
    
    $foto_empleado = '';
    if (isset($_FILES['foto_empleado']) && $_FILES['foto_empleado']['error'] === UPLOAD_ERR_OK) {
        $nombreFoto = time() . '_' . basename($_FILES['foto_empleado']['name']);
        $rutaDestino = '../uploads/' . $nombreFoto;
        if (!is_dir('../uploads')) {
            mkdir('../uploads', 0777, true);
        }
        if (move_uploaded_file($_FILES['foto_empleado']['tmp_name'], $rutaDestino)) {
            $foto_empleado = $nombreFoto;
        }
    }

    $sql_insert = "INSERT INTO empleado (id_empleado, nombre, telefono, correo, contrasena, rol, genero, permisos, fecha_inicio, hora_comida, direccion, foto_empleado, edad) 
                   VALUES ('$id_empleado', '$nombre', '$telefono', '$correo', '$contrasena', '$rol', '$genero', '$permisos', '$fecha_inicio', '$hora_comida', '$direccion', '$foto_empleado', '$edad')";

    if ($conexion->query($sql_insert)) {
        $mensaje = "<div class='alert alert-success alert-dismissible fade show w-100' role='alert'>
                        Empleado registrado correctamente.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $mensaje = "<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
                        Error al guardar: " . $conexion->error . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    }
}
?>

<div class="w-100 bg-white p-2 rounded text-start">
    <?php echo $mensaje; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
        <div>
            <h5 class="fw-bold mb-0">Registro de Personal</h5>
            <small class="text-muted">Complete los datos correspondientes</small>
        </div>
        <a href="menuinicio.php?modulo=configuracion&tab=usuarios&submodulo=menu" class="btn btn-outline-secondary btn-sm">← Volver</a>
    </div>

    <form action="menuinicio.php?modulo=configuracion&tab=usuarios&submodulo=personal" method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="guardar_empleado" value="1">

        <div class="col-md-4">
            <label class="form-label small fw-bold">ID Empleado: *</label>
            <input type="text" name="id_empleado" class="form-control form-control-sm" placeholder="Ej. X01" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Nombre Completo: *</label>
            <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Ej. Bethsi Jazmin" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Teléfono: *</label>
            <input type="tel" name="telefono" class="form-control form-control-sm" placeholder="Ej. 9991234567" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Correo Electrónico: *</label>
            <input type="email" name="correo" class="form-control form-control-sm" placeholder="usuario@dominio.com" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Contraseña: *</label>
            <input type="password" name="contrasena" class="form-control form-control-sm" placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Rol: *</label>
            <select name="rol" class="form-select form-select-sm" required>
                <option value="" disabled selected>-- Seleccione --</option>
                <option value="Admin">Admin</option>
                <option value="Vendedor">Vendedor</option>
                <option value="Vendedor">Vendedor Pro</option>
                <option value="Vendedor">Bodeguero</option>
                <option value="Vendedor">Repartidor</option>
                <option value="Vendedor">Contador</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold d-block">Género: *</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="genero" value="Masculino" required>
                <label class="form-check-label small">M</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="genero" value="Femenino">
                <label class="form-check-label small">F</label>
            </div>
        </div>
        <div class="col-md-8">
            <label class="form-label small fw-bold d-block">Permisos Especiales:</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="permisos[]" value="Caja">
                <label class="form-check-label small">Acceso a Caja</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="permisos[]" value="Inventario">
                <label class="form-check-label small">Modificar Inventario</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="permisos[]" value="Reportes">
                <label class="form-check-label small">Exportar Reportes</label>
            </div>
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha Nacimiento: *</label>
            <input 
                type="date" 
                id="fecha_inicio" 
                name="fecha_inicio" 
                onchange="calcularEdadAutomatica()" 
                required
                >
            </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Hora de Comida: *</label>
            <input type="time" name="hora_comida" class="form-control form-control-sm" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Foto / Documento: *</label>
            <input type="file" name="foto_empleado" class="form-control form-control-sm" required>
        </div>
        <div class="col-md-12">
            <label class="form-label small fw-bold">Dirección Domiciliaria: *</label>
            <textarea name="direccion" class="form-control form-control-sm" rows="2" placeholder="Ingrese la dirección..." required></textarea>
        </div>
        <div class="form-group">
            <label for="edad">Edad:</label>
            <input 
            type="text" 
            id="edad" 
            name="edad" 
            placeholder="--" 
            readonly
                >
                </div>
        <div class="col-12 d-flex gap-2 pt-2">
            <button type="submit" class="btn btn-success btn-sm px-4">Registrar Empleado</button>
            <button type="reset" class="btn btn-warning btn-sm text-white px-3">Limpiar Formulario</button>
            <a href="menuinicio.php?modulo=configuracion&tab=usuarios&submodulo=menu" class="btn btn-secondary btn-sm px-3">Cancelar</a>
        </div>
    </form>
</div>

<script>
function calcularEdadAutomatica() {         
  const fechaNacimientoInput = document.getElementById('fecha_inicio');
  const edadInput = document.getElementById('edad');

  // Validar si hay una fecha seleccionada
  if (!fechaNacimientoInput.value) {
    edadInput.value = '';
    return;
  }

  const hoy = new Date();
  const fechaNac = new Date(fechaNacimientoInput.value);

  // Para Calcular la diferencia de edad
  let edad = hoy.getFullYear() - fechaNac.getFullYear();
  const diferenciaMeses = hoy.getMonth() - fechaNac.getMonth();

  // Ajuste si aun no ha cumplido anos en el ano actual
  if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < fechaNac.getDate())) {
    edad--;
  }

  // Evitar valores negativos
  if (edad < 0 || isNaN(edad)) {
    edadInput.value = 'Fecha inválida';
  } else {
    // Asignar el resultado al campo edad
    edadInput.value = edad;
  }
}
</script>