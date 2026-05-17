<?php
require 'conexion.php';
$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var(trim($_POST['correo']), FILTER_VALIDATE_EMAIL);

    if (!$correo) {
        $mensaje = "Por favor, introduce una dirección de correo válida.";
        $tipo_mensaje = "danger";
    } else {
        // Verificar si el usuario realmente existe en la base de datos (Requisito de seguridad)
        $stmt = $pdo->prepare('SELECT nombre FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Simulación de envío exitoso conforme a requerimientos del sistema
            $mensaje = "Estimado(a) <strong>" . htmlspecialchars($usuario['nombre']) . "</strong>, se han enviado las instrucciones de recuperación a su correo institucional.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "El correo electrónico no se encuentra registrado en nuestro sistema.";
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
    <title>Recuperar Contraseña - Sistema UTPL</title>
    
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
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show shadow-sm mb-3 rounded-3" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card glass-card p-4">
                <div class="card-body">
                    <h3 class="text-center card-title mb-3 fw-bold text-dark">¿Olvidaste tu contraseña?</h3>
                    <p class="text-center text-muted small mb-4">Introduce tu correo electrónico y te enviaremos un enlace para restablecerla.</p>
                    <hr class="mb-4">

                    <form method="POST" action="recuperar.php">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" placeholder="ejemplo@utpl.edu.ec" required>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">Enviar Instrucciones</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted small">¿Recordaste tus datos? <a href="index.php" class="text-decoration-none fw-bold text-primary">Inicia Sesión</a></p>
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