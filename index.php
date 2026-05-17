<?php
session_start();
require_once 'conexion.php'; // Usa tu archivo conexion.php que está en la misma carpeta

$error_msg = "";

// Procesar los datos cuando el usuario da clic en "Iniciar sesión"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!empty($correo) && !empty($password)) {
        // Consulta preparada para evitar Inyección SQL
        $stmt = $pdo->prepare('SELECT cedula, nombre, password FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        $user = $stmt->fetch();

        // Verificar contraseña con el hash seguro
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Seguridad contra fijación de sesión
            
            $_SESSION['usuario_cedula'] = $user['cedula'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            
            header("Location: perfil.php"); // ¡Aquí te mandará al inicio del sistema!
            exit;
        } else {
            $error_msg = "El correo o la contraseña son incorrectos.";
        }
    } else {
        $error_msg = "Por favor, llene todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema UTPL</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-dark bg-transparent px-4">
            <span class="navbar-brand mb-0 h1 fs-3 text-white fw-bold">Sistema UTPL</span>
        </nav>
    </header>

    <section class="container flex-grow-1 d-flex justify-content-center align-items-center my-4">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3 rounded-3" role="alert">
                    <strong>¡Atención!</strong> <?php echo htmlspecialchars($error_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card login-card p-4">
                <div class="card-body">
                    <h2 class="text-center card-title mb-3 fw-bold text-dark">Inicio de Sesión</h2>
                    <p class="text-center text-muted small mb-4">Ingresa tus credenciales para acceder</p>
                    <hr class="mb-4">

                    <form method="POST" action="index.php">
                        <div class="mb-3">
                            <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@gmail.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese contraseña" required>
                        </div>

                        <div class="mb-4 text-end">
                            <a href="recuperar.php" class="text-decoration-none small text-primary">¿Olvidaste tu contraseña?</a>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">Iniciar sesión</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted small">¿No tienes cuenta? <a href="registro.php" class="text-decoration-none fw-bold text-primary">Registrarse</a></p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</body>
</html>