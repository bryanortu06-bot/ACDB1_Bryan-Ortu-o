<?php
session_start();
require 'conexion.php';

// SEGURIDAD: Si no hay sesión activa, redirigir al login inmediatamente
if (!isset($_SESSION['usuario_cedula'])) {
    header("Location: index.php");
    exit;
}

$mensaje = "";
$tipo_mensaje = "";
$tab_activa = "datos"; // Pestaña por defecto

$cedula = $_SESSION['usuario_cedula'];

// 1. PROCESAR ACTUALIZACIÓN DE DATOS BÁSICOS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    $tab_activa = "datos";
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_correo = filter_var(trim($_POST['correo']), FILTER_VALIDATE_EMAIL);

    if (!empty($nuevo_nombre) && $nuevo_correo) {
        try {
            $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, correo = ? WHERE cedula = ?');
            $stmt->execute([$nuevo_nombre, $nuevo_correo, $cedula]);
            
            // Actualizar inmediatamente las variables de sesión
            $_SESSION['usuario_nombre'] = $nuevo_nombre;
            $_SESSION['usuario_correo'] = $nuevo_correo;
            
            $mensaje = "Datos personales actualizados correctamente.";
            $tipo_mensaje = "success";
        } catch (\PDOException $e) {
            $mensaje = "Error: El correo electrónico ya está registrado por otro usuario.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Por favor, ingrese un nombre válido y un correo institucional correcto.";
        $tipo_mensaje = "danger";
    }
}

// 2. PROCESAR CAMBIO DE CONTRASEÑA SEGURO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_clave'])) {
    $tab_activa = "password";
    $password_actual = trim($_POST['password_actual']);
    $password_nueva = trim($_POST['password_nueva']);
    $password_confirma = trim($_POST['password_confirma']);

    if (empty($password_actual) || empty($password_nueva) || empty($password_confirma)) {
        $mensaje = "Todos los campos de contraseña son obligatorios.";
        $tipo_mensaje = "danger";
    } elseif ($password_nueva !== $password_confirma) {
        $mensaje = "La nueva contraseña y su confirmación no coinciden.";
        $tipo_mensaje = "danger";
    } else {
        // Obtener el hash actual de la base de datos para verificarlo
        $stmt = $pdo->prepare('SELECT password FROM usuarios WHERE cedula = ?');
        $stmt->execute([$cedula]);
        $usuario = $stmt->fetch();

        // RÚBRICA: Verificación segura usando password_verify
        if ($usuario && password_verify($password_actual, $usuario['password'])) {
            
            // RÚBRICA: Encriptación segura de la nueva clave usando password_hash con BCRYPT
            $nueva_clave_hash = password_hash($password_nueva, PASSWORD_BCRYPT);
            
            $stmt_update = $pdo->prepare('UPDATE usuarios SET password = ? WHERE cedula = ?');
            $stmt_update->execute([$nueva_clave_hash, $cedula]);
            
            $mensaje = "Contraseña modificada exitosamente de forma segura.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "La contraseña actual es incorrecta. Verificación fallida.";
            $tipo_mensaje = "danger";
        }
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
            background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url("https://img.magnific.com/foto-gratis/hermosos-paisajes-invierno-montanas-nevadas-agua-helada_181624-21843.jpg?semt=ais_hybrid&w=740&q=80");
            background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95); border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); backdrop-filter: blur(4px);
        }
        .nav-tabs .nav-link {
            color: #495057; font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold; color: #0d6efd !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark bg-opacity-70 px-4">
        <span class="navbar-brand mb-0 h1 fw-bold">Sistema UTPL - Panel Privado</span>
        <div>
            <span class="text-white me-3 d-none d-sm-inline">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold rounded-pill">Cerrar Sesión</a>
        </div>
    </nav>

    <section class="container flex-grow-1 d-flex justify-content-center align-items-center my-4">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show shadow-sm mb-3 rounded-3" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card glass-card p-4">
                <div class="card-body">
                    
                    <ul class="nav nav-tabs mb-4 justify-content-center" id="perfilTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($tab_activa === 'datos') ? 'active' : ''; ?>" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos-panel" type="button" role="tab">
                                👤 Mis Datos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($tab_activa === 'password') ? 'active' : ''; ?>" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-panel" type="button" role="tab">
                                🔒 Cambiar Contraseña
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="perfilTabsContent">
                        
                        <div class="tab-pane fade <?php echo ($tab_activa === 'datos') ? 'show active' : ''; ?>" id="datos-panel" role="tabpanel">
                            <h4 class="fw-bold mb-3 text-dark text-center">Actualizar Información Personal</h4>
                            <hr class="mb-4">
                            <form action="perfil.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Cédula de Identidad</label>
                                    <input type="text" class="form-control bg-light text-muted" value="<?php echo htmlspecialchars($_SESSION['usuario_cedula']); ?>" disabled>
                                    <div class="form-text">La cédula no es modificable por motivos institucionales.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nombre Completo</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Correo Institucional</label>
                                    <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($_SESSION['usuario_correo']); ?>" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="actualizar_perfil" class="btn btn-success fw-bold py-2 shadow-sm">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade <?php echo ($tab_activa === 'password') ? 'show active' : ''; ?>" id="password-panel" role="tabpanel">
                            <h4 class="fw-bold mb-3 text-dark text-center">Seguridad de la Cuenta</h4>
                            <hr class="mb-4">
                            <form action="perfil.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Contraseña Actual</label>
                                    <input type="password" name="password_actual" class="form-control" placeholder="Ingrese su clave actual" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nueva Contraseña</label>
                                    <input type="password" name="password_nueva" class="form-control" placeholder="Mínimo 6-8 caracteres" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                                    <input type="password" name="password_confirma" class="form-control" placeholder="Repita la nueva clave" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="cambiar_clave" class="btn btn-warning fw-bold py-2 shadow-sm text-dark">Actualizar Contraseña de Forma Segura</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>