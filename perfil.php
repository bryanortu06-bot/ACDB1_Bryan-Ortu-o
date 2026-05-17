<?php
session_start();
require_once 'conexion.php';

// Si no hay sesión activa, expulsamos al usuario al login
if (!isset($_SESSION['usuario_cedula'])) {
    header("Location: index.php");
    exit;
}

$cedula = $_SESSION['usuario_cedula'];
$mensaje = "";
$tipo_alerta = "";

// 1. Obtener datos actualizados del usuario desde la Base de Datos
$stmt = $pdo->prepare('SELECT nombre, correo FROM usuarios WHERE cedula = ?');
$stmt->execute([$cedula]);
$usuario = $stmt->fetch();

// 2. Procesar la actualización de datos cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_correo = filter_var(trim($_POST['correo']), FILTER_VALIDATE_EMAIL);

    if (!empty($nuevo_nombre) && $nuevo_correo) {
        try {
            $stmt_update = $pdo->prepare('UPDATE usuarios SET nombre = ?, correo = ? WHERE cedula = ?');
            $stmt_update->execute([$nuevo_nombre, $nuevo_correo, $cedula]);
            
            // Actualizamos las variables de sesión para que cambie el nombre en la pantalla arriba
            $_SESSION['usuario_nombre'] = $nuevo_nombre;
            $usuario['nombre'] = $nuevo_nombre;
            $usuario['correo'] = $nuevo_correo;
            
            $mensaje = "Datos actualizados correctamente.";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar los datos o el correo ya existe.";
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "Por favor, ingrese un nombre y un correo válido.";
        $tipo_alerta = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Sistema UTPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .profile-card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show mb-3" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card profile-card p-4">
                    <div class="card-body">
                        <h2 class="fw-bold text-center text-dark mb-2">Mi Perfil Privado</h2>
                        <p class="text-center text-muted small">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
                        <p class="text-center text-secondary small">Cédula: <?php echo htmlspecialchars($cedula); ?></p>
                        <hr class="my-4">

                        <form method="POST" action="perfil.php">
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success fw-bold py-2">Actualizar Mis Datos</button>
                                <a href="logout.php" class="btn btn-outline-danger fw-bold py-2">Cerrar Sesión</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>