
<style>
    /* Estilo del botón de impresión */
    .btn-imprimir {
        background-color: #8faec4;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s ease, transform 0.1s ease;
        cursor: pointer;
    }

    .btn-imprimir:hover {
        background-color: #7a9bb2;
        color: #ffffff;
    }

    /* Ocultar elementos innecesarios al momento de mandar a imprimir */
    @media print {
        /* Oculta la barra de navegación lateral, encabezados y el botón */
        .sidebar, 
        .top-navbar, 
        .footer-bar, 
        .btn-imprimir,
        header {
            display: none !important;
        }

        /* Expande el contenedor del reporte al 100% de la hoja */
        body, .content-area, .card {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            width: 100% !important;
        }
    }
</style>

<div class="card p-4 shadow-sm bg-white rounded">
    <!-- CABECERA DEL REPORTE CON BOTÓN ALINEADO -->
    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
        <div>
            <small class="text-muted d-block">Empresa: Pinturas TAPIR S.A.</small>
            <h3 class="fw-bold m-0" style="color: #2b3e50;">Estado de flujo de efectivo</h3>
            <small class="text-muted">Periodo: 09/10/2026 al 22/10/2026 · Moneda: MXN</small>
        </div>

        <!-- Botón de impresión estético -->
        <button onclick="window.print()" class="btn btn-imprimir shadow-sm">
         
            Imprimir Reporte
        </button>
    </div>
</div>

    <!-- 1. Flujos de actividades operativas -->
    <div class="card border rounded-3 overflow-hidden mb-4 mt-3">
        <div class="py-2 px-3 fw-bold text-white" style="background-color: #3c5982;">
            Flujos de actividades operativas
        </div>
        <table class="table table-borderless align-middle mb-0">
            <tbody>
                <tr class="border-bottom">
                    <td class="ps-3">Cobros a clientes</td>
                    <td class="text-end pe-3">27,800</td>
                </tr>
                <tr class="border-bottom">
                    <td class="ps-3">Pagos a proveedores</td>
                    <td class="text-end pe-3">(15,900)</td>
                </tr>
                <tr class="border-bottom">
                    <td class="ps-3">Pagos de gastos operativos</td>
                    <td class="text-end pe-3">(6,100)</td>
                </tr>
                <tr class="fw-bold">
                    <td class="ps-3">Flujo neto operativo</td>
                    <td class="text-end pe-3">5,800</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 2. Flujos de inversión -->
    <div class="card border rounded-3 overflow-hidden mb-4">
        <div class="py-2 px-3 fw-bold text-white" style="background-color: #3c5982;">
            Flujos de inversión
        </div>
        <table class="table table-borderless align-middle mb-0">
            <tbody>
                <tr class="border-bottom">
                    <td class="ps-3">Compra de material de trabajo</td>
                    <td class="text-end pe-3">(2,000)</td>
                </tr>
                <tr class="fw-bold">
                    <td class="ps-3">Flujo neto de inversión</td>
                    <td class="text-end pe-3">(2,000)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 3. Totales / Resumen (Caja Azul Clarita) -->
    <div class="rounded-3 p-3 text-dark" style="background-color: #92b8cd;">
        <table class="table table-borderless align-middle mb-0">
            <tbody>
                <tr class="fw-bold border-bottom border-secondary border-opacity-25">
                    <td>Aumento neto de efectivo del periodo</td>
                    <td class="text-end">4,850</td>
                </tr>
                <tr>
                    <td class="text-secondary fw-semibold">Efectivo inicial</td>
                    <td class="text-end text-secondary fw-semibold">7,650</td>
                </tr>
                <tr class="fw-bold">
                    <td class="fs-6">Efectivo final</td>
                    <td class="text-end fs-6">12,500</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>