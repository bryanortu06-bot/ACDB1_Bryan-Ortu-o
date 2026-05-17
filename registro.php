<?php
require 'conexion.php';
$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = filter_var(trim($_POST['correo']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($cedula) || empty($nombre) || !$correo || empty($password)) {
        $mensaje = "Todos los campos son obligatorios y el correo debe ser válido.";
        $tipo_mensaje = "danger";
    } else {
        // Verificar si el correo ya existe
        $stmt = $pdo->prepare('SELECT cedula FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        
        if ($stmt->fetch()) {
            $mensaje = "El correo electrónico ya se encuentra registrado.";
            $tipo_mensaje = "danger";
        } else {
            // Encriptar contraseña de forma segura (Rúbrica)
            $password_segura = password_hash($password, PASSWORD_BCRYPT);
            
            try {
                $stmt_insert = $pdo->prepare('INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (?, ?, ?, ?)');
                $stmt_insert->execute([$cedula, $nombre, $correo, $password_segura]);
                $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
                $tipo_mensaje = "success";
            } catch (\PDOException $e) {
                $mensaje = "Error al registrar: La cédula ya existe.";
                $tipo_mensaje = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema UTPL</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url("https://img.magnific.com/foto-gratis/hermosos-paisajes-invierno-montanas-nevadas-agua-helada_181624-21843.jpg?semt=ais_hybrid&w=740&q=80");
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 15px; 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); 
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body>
    <section class="container flex-grow-1 d-flex justify-content-center align-items-center my-4">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card glass-card p-4">
                <div class="card-body">
                    <h2 class="text-center card-title mb-3 fw-bold text-dark">Crear Cuenta</h2>
                    <p class="text-center text-muted small mb-4">Regístrate para acceder al sistema académico</p>
                    <hr class="mb-4">

                    <form method="POST" action="registro.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cédula</label>
                            <input type="text" name="cedula" class="form-control" maxlength="10" placeholder="Ej. 1102203304" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" name="correo" class="form-control" placeholder="ejemplo@utpl.edu.ec" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Cree una contraseña segura" required>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-success fw-bold py-2 shadow-sm">Registrarme</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted small">¿Ya tienes cuenta? <a href="index.php" class="text-decoration-none fw-bold text-primary">Inicia Sesión</a></p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="text-white text-decoration-none bg-dark bg-opacity-50 px-3 py-2 rounded-pill small shadow-sm">← Volver al Inicio</a>
            </div>

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>