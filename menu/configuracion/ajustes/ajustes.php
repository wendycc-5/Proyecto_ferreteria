<style>
  .form-config {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 600px;
    margin: 20px auto;
    text-align: left;
  }
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .form-group label {
    font-size: 0.88rem;
    font-weight: 600;
    color: #334155;
  }
  .form-group input, .form-group textarea {
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
    outline: none;
  }
  .btn-guardar {
    background-color: #1c3a5e;
    color: #ffffff;
    border: none;
    padding: 11px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
  }
  .btn-guardar:hover {
    background-color: #122842;
  }
</style>

<div style="text-align: center; padding: 10px;">
  <h2>Ajustes de Venta</h2>
  <p style="color: #64748b; font-size: 0.9rem;">Configuración de tickets y alerta de stock mínimo</p>

  <form class="form-config" method="POST" action="">
    <div class="form-group">
      <label>Mensaje al Pie del Ticket / Recibo:</label>
      <textarea name="pie_ticket" rows="2" placeholder="Ej. ¡Gracias por su compra en Pinturas Tapir!"></textarea>
    </div>

    <div class="form-group">
      <label>Alerta de Stock Minimo (Unidades):</label>
      <input type="number" name="stock_minimo" value="5" min="1">
      <small style="color: #64748b; font-size: 0.78rem;">El sistema te avisará cuando un producto tenga esta cantidad o menos.</small>
    </div>

    <div class="form-group">
      <label>Porcentaje de IVA por defecto (%):</label>
      <input type="number" name="iva" value="16" min="0" max="100">
    </div>

    <button type="submit" class="btn-guardar">Guardar Ajustes de Venta</button>
  </form>
</div>