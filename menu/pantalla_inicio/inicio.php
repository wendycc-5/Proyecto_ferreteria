<?php
// Asegurar variables de sesión por seguridad
$rol = isset($rol) ? $rol : (isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Vendedor');
$nombre_usuario = isset($nombre_usuario) ? $nombre_usuario : (isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'WEBE');

// Consulta para obtener 4 productos destacados / en oferta de la base de datos
$query_ofertas = "SELECT id_producto, nom_produc, descripcion, precio FROM producto LIMIT 4";
$res_ofertas = $conexion->query($query_ofertas);
?>

<div class="container-fluid px-0">
    <!-- Banner de Bienvenida con Logo -->
    <div class="card border-0 shadow-sm mb-4 text-white" style="background: linear-gradient(135deg, #1a365d 0%, #0d6efd 100%); border-radius: 12px;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;">
                    <!-- Muestra la imagen del logo o un ícono alternativo si aún no está lista -->
                    <img src="../imagenes/IMG-20261001-WA0010.jpg" alt="Logo Tapir" class="img-fluid rounded-circle" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/2972/2972531.png';">
                </div>
                <div>
                    <h2 class="fw-bold m-0">¡Bienvenido a Pinturas Tapir!</h2>
                    <p class="mb-0 text-light opacity-75">Panel de administración e información de promociones activas.</p>
                </div>
            </div>
            <div>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-bold shadow-sm">
                    Usuario: <?php echo htmlspecialchars($nombre_usuario); ?> (<?php echo htmlspecialchars($rol); ?>)
                </span>
            </div>
        </div>
    </div>

    <!-- Sección de Productos en Promoción -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold text-dark m-0">🔥 Productos en Promoción</h4>
        <a href="menuinicio.php?modulo=ventas" class="btn btn-outline-primary btn-sm fw-bold rounded-pill">Ir a Ventas ➔</a>
    </div>

    <div class="row g-3">
        <?php if ($res_ofertas && $res_ofertas->num_rows > 0): ?>
            <?php 
            $badges = ['20% DESC', 'OFERTA TOP', '15% DESC', 'MEJOR PRECIO'];
            $colores = ['bg-danger', 'bg-warning text-dark', 'bg-success', 'bg-primary'];
            $i = 0;
            while($prod = $res_ofertas->fetch_assoc()): 
                $precioOriginal = $prod['precio'] * 1.20; // Cálculo ilustrativo para precio tachado
            ?>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden hover-card" style="border-radius: 10px;">
                        <span class="position-absolute top-0 end-0 badge <?php echo $colores[$i % 4]; ?> m-2 px-2 py-1 fw-bold">
                            <?php echo $badges[$i % 4]; ?>
                        </span>
                        
                        <div class="p-3 text-center bg-light">
                            <div class="display-6 text-primary my-2">🎨</div>
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <small class="text-muted fw-bold"><?php echo htmlspecialchars($prod['id_producto']); ?></small>
                                <h6 class="fw-bold text-dark my-1"><?php echo htmlspecialchars($prod['nom_produc']); ?></h6>
                                <p class="text-muted small mb-2 text-truncate"><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                            </div>
                            
                            <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-decoration-line-through text-muted">$<?php echo number_format($precioOriginal, 2); ?></small><br>
                                    <span class="fs-5 fw-bold text-success">$<?php echo number_format($prod['precio'], 2); ?></span>
                                </div>
                                <a href="menuinicio.php?modulo=ventas" class="btn btn-sm btn-primary rounded-circle" title="Vender">🛒</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                $i++;
            endwhile; 
            ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">No hay productos promocionales disponibles por el momento.</div>
            </div>
        <?php endif; ?>
    </div>
</div>