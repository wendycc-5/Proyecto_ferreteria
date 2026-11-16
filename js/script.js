let productoActual = null;
let carrito = [];
let tipoComprobanteActual = 'ticket';

function evaluarDescuento() {
    const tipo = document.getElementById("tipo_cliente").value;
    const contenedor = document.getElementById("contenedor_clientes");
    if (tipo === "frecuente") {
        contenedor.classList.remove("d-none");
    } else {
        contenedor.classList.add("d-none");
    }
    actualizarTablaCarrito();
}

// Búsqueda de productos en la base de datos
function buscarProductoBD() {
    const inputBusqueda = document.getElementById("codigo_prod").value.trim();
    if (!inputBusqueda) {
        alert("Ingrese un código o nombre de producto.");
        return;
    }

    fetch(`menuinicio.php?api_buscar_producto=${encodeURIComponent(inputBusqueda)}`)
        .then(response => response.json())
        .then(data => {
            if (data.encontrado) {
                productoActual = { nombre: data.nombre, precio: data.precio, stock: data.stock };
                document.getElementById("lbl_nombre").innerText = data.nombre;
                document.getElementById("lbl_precio").innerText = data.precio.toFixed(2);
                document.getElementById("lbl_stock").innerText = data.stock;
                document.getElementById("info_producto_encontrado").classList.remove("d-none");
            } else {
                alert("Producto no encontrado en la base de datos.");
                document.getElementById("info_producto_encontrado").classList.add("d-none");
                productoActual = null;
            }
        })
        .catch(err => console.error("Error al buscar:", err));
}

function seleccionarRapido(nombre, precio) {
    productoActual = { nombre: nombre, precio: parseFloat(precio) };
    agregarAlCarrito();
}

function agregarAlCarrito() {
    if (!productoActual) {
        alert("Primero busque y seleccione un producto.");
        return;
    }
    const index = carrito.findIndex(p => p.nombre === productoActual.nombre);
    if (index !== -1) {
        carrito[index].cantidad += 1;
    } else {
        carrito.push({
            nombre: productoActual.nombre,
            precioBase: productoActual.precio,
            cantidad: 1
        });
    }
    actualizarTablaCarrito();
    document.getElementById("info_producto_encontrado").classList.add("d-none");
    document.getElementById("codigo_prod").value = "";
    productoActual = null;
}

function actualizarTablaCarrito() {
    const tbody = document.getElementById("tabla_carrito");
    tbody.innerHTML = "";
    let totalGeneral = 0;
    const esFrecuente = document.getElementById("tipo_cliente").value === "frecuente";

    if (carrito.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">No hay productos en el carrito</td></tr>`;
        document.getElementById("total_venta").innerText = "0.00";
        return;
    }

    carrito.forEach((prod) => {
        let precioUnitario = esFrecuente ? (prod.precioBase * 0.90) : prod.precioBase;
        let importe = precioUnitario * prod.cantidad;
        totalGeneral += importe;

        tbody.innerHTML += `
            <tr>
                <td class="text-center fw-bold">${prod.cantidad}</td>
                <td>${prod.nombre} ${esFrecuente ? '<span class="badge bg-success ms-1">10% Desc.</span>' : ''}</td>
                <td>$${precioUnitario.toFixed(2)}</td>
                <td class="fw-bold">$${importe.toFixed(2)}</td>
            </tr>
        `;
    });

    document.getElementById("total_venta").innerText = totalGeneral.toFixed(2);
}

// Ventana de Confirmación de Venta (Modal rojo)
function procesarVenta() {
    if (carrito.length === 0) {
        alert("El carrito está vacío. Agregue productos antes de continuar.");
        return;
    }

    let tipoTarifa = document.getElementById("tipo_cliente").value;
    let nombreCliente = "Público General";
    if (tipoTarifa === "frecuente") {
        let selectC = document.getElementById("lista_clientes");
        if (selectC.selectedIndex >= 0 && selectC.options[selectC.selectedIndex].value !== "") {
            nombreCliente = selectC.options[selectC.selectedIndex].text;
        } else {
            alert("Seleccione un cliente frecuente de la lista.");
            return;
        }
    }

    let totalVenta = document.getElementById("total_venta").innerText;
    let metodoPago = document.getElementById("metodo_pago").value;

    document.getElementById("modal_lbl_tarifa").value = (tipoTarifa === "frecuente") ? `${nombreCliente} (10% Desc.)` : "Público general";
    document.getElementById("modal_lbl_total").innerText = totalVenta;
    document.getElementById("modal_lbl_metodo").innerText = metodoPago;

    document.getElementById("modal_confirmacion_venta").style.display = "block";
}

function cancelarVenta() {
    document.getElementById("modal_confirmacion_venta").style.display = "none";
}

function seleccionarComprobante(tipo) {
    tipoComprobanteActual = tipo;
    if (tipo === 'ticket') {
        document.getElementById("btn_modo_ticket").className = "btn btn-success btn-sm px-4 rounded-pill fw-bold";
        document.getElementById("btn_modo_factura").className = "btn btn-outline-secondary btn-sm px-4 rounded-pill fw-bold";
    } else {
        document.getElementById("btn_modo_factura").className = "btn btn-success btn-sm px-4 rounded-pill fw-bold";
        document.getElementById("btn_modo_ticket").className = "btn btn-outline-secondary btn-sm px-4 rounded-pill fw-bold";
    }
}


// Al hacer clic en "Confirmar venta": descuenta stock y genera el ticket
function confirmarYDescontarStock() {
    let totalVenta = document.getElementById("modal_lbl_total").innerText;
    let metodoPago = document.getElementById("modal_lbl_metodo").innerText;
    let nombreCliente = document.getElementById("modal_lbl_tarifa").value;

    // Petición AJAX dirigida al archivo principal que procesa la base de datos
    fetch("menuinicio.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion_procesar_venta: true, carrito: carrito })
    })
    .then(res => res.json())
    .then(data => {
        if (data.exito) {
            document.getElementById("modal_confirmacion_venta").style.display = "none";
            generarComprobanteImpresion(nombreCliente, totalVenta, metodoPago, tipoComprobanteActual);
        } else {
            alert("Error al actualizar inventario: " + data.mensaje);
        }
    })
    .catch(err => {
        console.error("Error:", err);
        // Respaldo de seguridad para abrir el ticket aunque falle la respuesta de red
        document.getElementById("modal_confirmacion_venta").style.display = "none";
        generarComprobanteImpresion(nombreCliente, totalVenta, metodoPago, tipoComprobanteActual);
    });
}

// Impresión del Ticket con formato estructurado
function generarComprobanteImpresion(cliente, total, metodo, tipo) {
    let detalleProductos = "";
    let esFrecuente = document.getElementById("tipo_cliente").value === "frecuente";

    carrito.forEach(prod => {
        let precioUnitario = esFrecuente ? (prod.precioBase * 0.90) : prod.precioBase;
        let subtotal = (precioUnitario * prod.cantidad).toFixed(2);
        detalleProductos += `${prod.cantidad} x ${prod.nombre} ---- $${subtotal}<br>`;
    });

    let ventanaImp = window.open("", "_blank", "width=400,height=650");
    if (!ventanaImp || ventanaImp.closed || typeof ventanaImp.closed == 'undefined') {
        alert("Por favor permita las ventanas emergentes (pop-ups) en su navegador para imprimir el ticket.");
    } else {
        ventanaImp.document.write(`
            <html>
            <head>
                <title>Ticket de Venta - Pinturas Tapir</title>
                <style>
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        font-size: 13px;
                        width: 300px;
                        margin: 0 auto;
                        padding: 10px;
                        background: #ffffff;
                        color: #000000;
                    }
                    .center { text-align: center; }
                    .bold { font-weight: bold; }
                    .right { text-align: right; }
                    .line {
                        border-bottom: 1px dashed #000000;
                        margin: 8px 0;
                    }
                    table { width: 100%; font-size: 13px; font-family: 'Courier New', Courier, monospace; }
                </style>
            </head>
            <body>
                <div class="center bold" style="font-size: 15px;">PINTURAS TAPIR</div>
                <div class="center" style="font-size: 11px;">
                    Dirección: C.57 #404, Centro, 97700,<br>Tizimín, Yuc.<br>
                    Contacto: 986 107 8065<br>
                    Sucursal: Centro
                </div>
                
                <div class="line"></div>
                
                <div class="center bold">
                    COMPROBANTE DE ${tipo.toUpperCase()}<br>VENTA N° 11
                </div>
                
                <div class="line"></div>
                
                <div>
                    <strong>Fecha:</strong> 08/05/2026<br>
                    <strong>Cliente:</strong> ${cliente}<br>
                    <strong>Vendedor:</strong> WEBE
                </div>
                
                <div class="line"></div>
                
                <table>
                    <tr>
                        <td class="bold">Descripción</td>
                        <td class="bold right">Costo</td>
                    </tr>
                </table>
                
                <div class="line"></div>
                
                <div style="font-size: 12px; line-height: 1.4;">
                    ${detalleProductos}
                </div>
                
                <div class="line"></div>
                
                <div class="bold right" style="font-size: 14px; margin-bottom: 5px;">
                    Total: $${total}
                </div>
                
                <div style="font-size: 12px;">
                    Método: ${metodo} &nbsp;&nbsp;&nbsp;&nbsp; Pagado:
                </div>
                <div class="bold" style="font-size: 12px; margin-top: 2px;">
                    $${total}
                </div>
                
                <div class="line"></div>
                
                <div class="center bold" style="margin-top: 10px;">
                    ¡Gracias por su compra!
                </div>

                <script>
                    window.print();
                </script>
            </body>
            </html>
        `);
        ventanaImp.document.close();
    }

    carrito = [];
    actualizarTablaCarrito();
    document.getElementById("tipo_cliente").value = "general";
    evaluarDescuento();
    setTimeout(() => { location.reload(); }, 1000);
}