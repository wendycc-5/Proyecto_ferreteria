<style>
  .card-respaldo {
    max-width: 550px;
    margin: 30px auto;
    padding: 25px;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    text-align: center;
  }
  .btn-descargar-sql {
    background-color: #1c3a5e;
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    margin-top: 15px;
    transition: background-color 0.2s;
  }
  .btn-descargar-sql:hover {
    background-color: #122842;
  }
</style>

<div class="card-respaldo">
  <h3 style="color: #1c3a5e; margin-bottom: 8px;">💾 Respaldos de Base de Datos</h3>
  <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
    Genera y descarga una copia de seguridad en formato <strong>.SQL</strong> con la estructura y todos los registros guardados en el sistema.
  </p>

  <!-- Enlace que ejecuta el script de descarga PHP -->
  <a href="../menu/configuracion/empresa/respaldo.php" class="btn-descargar-sql">
    📥 Generar y Descargar Respaldo .SQL
  </a>
</div>