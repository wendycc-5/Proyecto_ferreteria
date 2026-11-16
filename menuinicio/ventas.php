<div class="card p-4 shadow-sm pos-card bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
        <h5 class="fw-bold text-dark m-0">Pinturas TAPIR - Sucursal Centro</h5>
        <span class="badge badge-cajero text-white fw-bold">Cajero: B20 (Wendy Araceli Chi Cupul)</span>
    </div>

    <!-- Controles de Tarifa y Buscador -->
    <div class="row g-2 mb-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted m-0">Tipo de Tarifa:</label>
            <select id="tipo_cliente" class="form-select form-select-sm" onchange="evaluarDescuento()">
                <option value="general">Público general</option>
                <option value="frecuente">Cliente Frecuente (10% Desc.)</option>
            </select>
            
            <div id="contenedor_clientes" class="mt-2 d-none">
                <label class="form-label small fw-bold text-primary m-0">Seleccionar Cliente Frecuente:</label>
                <select id="lista_clientes" class="form-select form-select-sm" size="4" style="max-height: 95px; overflow-y: auto;">
                    <?php
                    $q_clientes = $conexion->query("SELECT * FROM cliente");
                    if ($q_clientes && $q_clientes->num_rows > 0) {
                        while ($c = $q_clientes->fetch_assoc()) {
                            echo "<option value='" . $c['id_cliente'] . "'>" . htmlspecialchars($c['nombre']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

       <div class="col-md-5">
    <label class="form-label small fw-bold text-muted m-0">Buscar Código o Nombre de Producto:</label>
    <div class="input-group input-group-sm">
        <input type="text" id="codigo_prod" class="form-control" placeholder="Ej. PIN-01, CAL, Thiner" onkeydown="if(event.key === 'Enter'){ buscarProductoBD(); }">
        <button class="btn btn-primary" type="button" onclick="buscarProductoBD()">🔍 Buscar</button>
    </div>
</div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-success btn-sm fw-bold w-100 py-2" onclick="agregarAlCarrito()">🛒 Agregar Al Carrito</button>
        </div>
    </div>

    <div id="info_producto_encontrado" class="alert alert-info border small py-2 mb-3 d-none">
        <strong>Producto Encontrado:</strong> <span id="lbl_nombre">-</span> | <strong>Precio:</strong> $<span id="lbl_precio">0.00</span> | <strong>Stock:</strong> <span id="lbl_stock">0</span>
    </div>

    <!-- Catálogo Rápido con scroll vertical -->
    <div class="card bg-light border-0 p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small fw-bold text-secondary">📦 Productos en Existencia (Catálogo Rápido):</span>
            <span class="badge bg-secondary">Deslice para ver más / Clic en + Añadir</span>
        </div>
        <div class="table-responsive" style="max-height: 165px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
            <table class="table table-sm table-bordered bg-white align-middle small mb-0">
                <thead class="table-secondary sticky-top">
                    <tr>
                        <th>Código</th>
                        <th>Descripción del Producto</th>
                        <th>Precio Unitario</th>
                        <th>Stock</th>
                        <th class="text-center" style="width: 100px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $top = $conexion->query("SELECT * FROM producto");
                    if ($top && $top->num_rows > 0) {
                        while ($p = $top->fetch_assoc()) {
                            $cod = htmlspecialchars($p['id_producto']);
                            $nom = htmlspecialchars($p['nom_produc']);
                            $precio = floatval($p['precio']);
                            $stock = isset($p['stock']) ? $p['stock'] : 0;
                            echo "<tr>";
                            echo "<td><code class='text-primary fw-bold'>$cod</code></td>";
                            echo "<td>$nom</td>";
                            echo "<td>$" . number_format($precio, 2) . "</td>";
                            echo "<td><span class='badge bg-info text-dark'>$stock unids</span></td>";
                            echo "<td class='text-center'><button class='btn btn-outline-primary btn-sm py-0 px-2 fw-bold' onclick=\"seleccionarRapido('$nom', $precio)\">+ Añadir</button></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabla del Carrito -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;" class="text-center">Cant.</th>
                    <th>Concepto / Descripción</th>
                    <th style="width: 130px;">P. Unitario</th>
                    <th style="width: 130px;">Importe</th>
                </tr>
            </thead>
            <tbody id="tabla_carrito">
                <tr><td colspan="4" class="text-center text-muted py-4">No hay productos en el carrito</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pie de Cobro -->
    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
        <div class="d-flex align-items-center gap-2">
            <label class="small fw-bold text-dark m-0">Método de pago:</label>
            <select id="metodo_pago" class="form-select form-select-sm w-auto">
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjetas bancarias">Tarjetas bancarias</option>
            </select>
        </div>
        <h3 class="fw-bold text-danger m-0">Total: $<span id="total_venta">0.00</span></h3>
        <button class="btn btn-success btn-lg fw-bold px-4" onclick="procesarVenta()">Continuar venta</button>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN DE VENTA (Idéntico a tu diseño) -->
<div id="modal_confirmacion_venta" class="modal" tabindex="-1" style="background: rgba(0,0,0,0.6); display: none;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content shadow-lg border-0 rounded-0">
            <!-- Barra superior roja -->
            <div class="modal-header text-white py-2" style="background-color: #dc3545; border-radius: 0;">
                <h5 class="modal-title fw-bold fs-6 m-0">Confirmación de venta</h5>
                <button type="button" class="btn-close btn-close-white" onclick="cancelarVenta()"></button>
            </div>
            <div class="modal-body bg-white p-4">
                <!-- Tarjeta de Usuario/Cajero -->
                <div class="border p-2 mb-3 bg-light d-flex align-items-center gap-2">
                    <span class="badge bg-secondary">👤 B09</span>
                    <strong class="text-dark">Wendy Araceli Chi Cupul</strong>
                </div>

                <div class="mb-2 row align-items-center">
                    <div class="col-4 small fw-bold text-muted">Precio lista:</div>
                    <div class="col-8">
                        <input type="text" id="modal_lbl_tarifa" class="form-control form-control-sm bg-white" readonly value="Público general">
                    </div>
                </div>

                <div class="mb-2 row align-items-center">
                    <div class="col-4 small fw-bold text-muted">Monto total:</div>
                    <div class="col-8 fw-bold fs-5 text-dark">$<span id="modal_lbl_total">0.00</span></div>
                </div>

                <div class="mb-3 row align-items-center">
                    <div class="col-4 small fw-bold text-muted">Pago con:</div>
                    <div class="col-8 fw-bold text-secondary" id="modal_lbl_metodo">Efectivo</div>
                </div>

                <div class="text-center mb-3">
                    <span class="small fw-bold text-dark d-block mb-2">Comprobante de transacción</span>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" id="btn_modo_ticket" class="btn btn-success btn-sm px-4 rounded-pill fw-bold" onclick="seleccionarComprobante('ticket')">Ticket</button>
                        <button type="button" id="btn_modo_factura" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-bold" onclick="seleccionarComprobante('factura')">Factura</button>
                    </div>
                </div>

                <button type="button" class="btn btn-success w-100 fw-bold py-2 shadow-sm" onclick="confirmarYDescontarStock()">Confirmar venta</button>
            </div>
        </div>
    </div>
</div>