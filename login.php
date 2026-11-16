<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['rol'] = $_POST['usuario'];
    $_SESSION['usuario_nombre'] = $_POST['usuario'] == 'Vendedor' ? 'Wendy Araceli' : $_POST['usuario'];
    
    // llamado al inicio 
    header("Location: menu/menuinicio.php?modulo=inicio");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - Pinturas Tapir</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, Helvetica, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        /* Cuadro de login */
        .card-login {
            width: 480px; /* Ajuste del contenedor */
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            padding: 35px;
        }

        .logo-login {
            max-height: 85px;
            width: auto;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .btn-custom-blue {
            background-color: #0d6efd;
            color: #ffffff;
            font-weight: bold;
            padding: 10px;
            font-size: 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .btn-custom-blue:hover {
            background-color: #0b5ed7;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="card-login text-center">
        <!-- LOGO DE PINTURAS TAPIR -->
        <img src="imagenes/logo.jpg" alt="Pinturas Tapir Logo" class="logo-login" onerror="this.onerror=null; this.src='imagenes/logo.jpg';">

        <h3 class="text-danger fw-bold mb-1" style="letter-spacing: 0.5px;">PINTURAS TAPIR</h3>
        <h5 class="text-secondary mb-4 fs-6">Iniciar sesión</h5>

        <form method="POST" class="text-start">
            <div class="mb-3">
                <label for="usuario" class="form-label fw-bold text-dark small">Usuario</label>
                <select id="usuario" name="usuario" class="form-select form-select-lg fs-6" required>
                    <option value="Vendedor" selected>Vendedor</option>
                    <option value="Vendedor Pro">Vendedor Pro</option>
                    <option value="Contabilidad">Contabilidad</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-bold text-dark small">Contrasena</label>
                <input type="password" id="password" name="password" class="form-control form-control-lg fs-6" placeholder="••••••••" required minlength="8" maxlength="8">
            </div>

            <button type="submit" class="btn btn-custom-blue w-100 border-0">Entrar</button>
        </form>
    </div>

</body>
</html>