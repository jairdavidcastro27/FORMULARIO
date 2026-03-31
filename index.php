<?php
session_start();

/* =========================
   CONFIG DB - RAILWAY POSTGRESQL
========================= */
$host = "hopper.proxy.rlwy.net";
$port = "32772";
$dbname = "railway";
$user = "postgres";
$password = "xmHueKPmenUNrMQktpRFQlnQLJqxFfzc";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

/* =========================
   VER CAPTURA
========================= */
if (isset($_GET['ver_captura'])) {
    $id = (int) $_GET['ver_captura'];

    $stmt = $pdo->prepare("SELECT captura_pago_nombre, captura_pago_tipo, captura_pago_archivo FROM inscripciones WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        header("Content-Type: " . $row['captura_pago_tipo']);
        header("Content-Disposition: inline; filename=\"" . $row['captura_pago_nombre'] . "\"");

        if (is_resource($row['captura_pago_archivo'])) {
            fpassthru($row['captura_pago_archivo']);
        } else {
            echo $row['captura_pago_archivo'];
        }
        exit;
    } else {
        die("Archivo no encontrado.");
    }
}

/* =========================
   LOGOUT
========================= */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: sistema.php");
    exit;
}

/* =========================
   LOGIN ADMIN
========================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'login') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email AND estado = 'activo' LIMIT 1");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $hash = $admin['password_hash'];
        // Permitir login si el hash es válido o si coincide en texto plano
        if (password_verify($pass, $hash) || $pass === $hash) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            header("Location: sistema.php?admin=1");
            exit;
        }
    }
    $error_login = "❌ Credenciales incorrectas";
}

/* =========================
   GUARDAR INSCRIPCIÓN
========================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_inscripcion') {
    $nombre_completo = trim($_POST['nombre_completo']);
    $celular = trim($_POST['celular']);
    $ciudad = trim($_POST['ciudad']);
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $sucursal = trim($_POST['sucursal']);
    $catalogo_pago_id = (int) $_POST['catalogo_pago_id'];
    $comentario = !empty($_POST['comentario']) ? trim($_POST['comentario']) : null;

    if (!isset($_FILES["captura_pago"]) || $_FILES["captura_pago"]["error"] !== 0) {
        $mensaje = "❌ Error al subir la captura.";
    } else {
        $archivo = $_FILES["captura_pago"];
        $nombreArchivo = $archivo["name"];
        $tipoArchivo = $archivo["type"];
        $tmpPath = $archivo["tmp_name"];
        $tamano = $archivo["size"];

        if ($tamano > 5 * 1024 * 1024) {
            $mensaje = "❌ El archivo no debe superar los 5MB.";
        } else {
            $permitidos = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!in_array($tipoArchivo, $permitidos)) {
                $mensaje = "❌ Solo se permiten JPG, PNG o PDF.";
            } else {
                $contenidoArchivo = file_get_contents($tmpPath);

                $sql = "INSERT INTO inscripciones (
                            catalogo_pago_id,
                            nombre_completo, celular, ciudad, email,
                            sucursal, comentario,
                            captura_pago_nombre, captura_pago_tipo, captura_pago_archivo
                        ) VALUES (
                            :catalogo_pago_id,
                            :nombre_completo, :celular, :ciudad, :email,
                            :sucursal, :comentario,
                            :captura_pago_nombre, :captura_pago_tipo, :captura_pago_archivo
                        )";

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':catalogo_pago_id', $catalogo_pago_id, PDO::PARAM_INT);
                $stmt->bindValue(':nombre_completo', $nombre_completo);
                $stmt->bindValue(':celular', $celular);
                $stmt->bindValue(':ciudad', $ciudad);
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':sucursal', $sucursal);
                $stmt->bindValue(':comentario', $comentario);
                $stmt->bindValue(':captura_pago_nombre', $nombreArchivo);
                $stmt->bindValue(':captura_pago_tipo', $tipoArchivo);
                $stmt->bindValue(':captura_pago_archivo', $contenidoArchivo, PDO::PARAM_LOB);

                $stmt->execute();
                $mensaje = "✅ Inscripción enviada correctamente.";
            }
        }
    }
}

/* =========================
   CAMBIAR ESTADO
========================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'cambiar_estado' && isset($_SESSION['admin_id'])) {
    $id = (int) $_POST['id'];
    $estado = $_POST['estado'];

    if (in_array($estado, ['pendiente', 'validado', 'rechazado'])) {
        $stmt = $pdo->prepare("UPDATE inscripciones SET estado = :estado WHERE id = :id");
        $stmt->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }

    header("Location: sistema.php?admin=1");
    exit;
}

/* =========================
   ELIMINAR
========================= */
if (isset($_GET['eliminar']) && isset($_SESSION['admin_id'])) {
    $id = (int) $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: sistema.php?admin=1");
    exit;
}

/* =========================
   CARGAR CATÁLOGO
========================= */
$catalogo = $pdo->query("SELECT * FROM catalogo_pagos WHERE estado = 'activo' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   PANEL ADMIN
========================= */
if (isset($_GET['admin']) && isset($_SESSION['admin_id'])) {
    $inscripciones = $pdo->query("
        SELECT i.*, c.nombre AS concepto_pago
        FROM inscripciones i
        INNER JOIN catalogo_pagos c ON i.catalogo_pago_id = c.id
        ORDER BY i.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Panel Admin</title>
        <style>
            body { font-family: Arial; background:#f4f6f9; padding:20px; }
            .box { max-width: 1200px; margin:auto; background:white; padding:20px; border-radius:12px; }
            table { width:100%; border-collapse: collapse; margin-top:20px; }
            th, td { border:1px solid #ddd; padding:10px; text-align:left; font-size:14px; }
            th { background:#6c63ff; color:white; }
            .top { display:flex; justify-content:space-between; align-items:center; }
            a.btn, button, select {
                padding:8px 12px; border:none; border-radius:8px; text-decoration:none;
            }
            a.btn { background:#111827; color:white; }
            .ok { color:green; font-weight:bold; }
            .pen { color:orange; font-weight:bold; }
            .rej { color:red; font-weight:bold; }
            form.inline { display:inline-block; }
        </style>
    </head>
    <body>
    <div class="box">
        <div class="top">
            <h2>Panel Admin - Pagos Recibidos</h2>
            <div>
                <strong><?= htmlspecialchars($_SESSION['admin_nombre']) ?></strong>
                <a class="btn" href="sistema.php?logout=1">Cerrar sesión</a>
            </div>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Alumno</th>
                <th>Celular</th>
                <th>Ciudad</th>
                <th>Email</th>
                <th>Sucursal</th>
                <th>Pago</th>
                <th>Captura</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($inscripciones as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($row['celular']) ?></td>
                    <td><?= htmlspecialchars($row['ciudad']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['sucursal']) ?></td>
                    <td><?= htmlspecialchars($row['concepto_pago']) ?></td>
                    <td><a target="_blank" href="sistema.php?ver_captura=<?= $row['id'] ?>">Ver</a></td>
                    <td>
                        <?php if ($row['estado'] === 'pendiente'): ?>
                            <span class="pen">Pendiente</span>
                        <?php elseif ($row['estado'] === 'validado'): ?>
                            <span class="ok">Validado</span>
                        <?php else: ?>
                            <span class="rej">Rechazado</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <form class="inline" method="POST">
                            <input type="hidden" name="accion" value="cambiar_estado">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="estado" onchange="this.form.submit()">
                                <option value="pendiente" <?= $row['estado']=='pendiente'?'selected':'' ?>>Pendiente</option>
                                <option value="validado" <?= $row['estado']=='validado'?'selected':'' ?>>Validado</option>
                                <option value="rechazado" <?= $row['estado']=='rechazado'?'selected':'' ?>>Rechazado</option>
                            </select>
                        </form>
                        <br><br>
                        <a onclick="return confirm('¿Eliminar este registro?')" href="sistema.php?admin=1&eliminar=<?= $row['id'] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    </body>
    </html>
    <?php
    exit;
}

/* =========================
   LOGIN ADMIN VIEW
========================= */
if (isset($_GET['login'])) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login Admin</title>
        <style>
            body { font-family: Arial; background:#f4f6f9; padding:30px; }
            .box { max-width:400px; margin:auto; background:white; padding:30px; border-radius:12px; }
            input, button {
                width:100%; padding:12px; margin-top:10px; margin-bottom:15px;
                border:1px solid #ccc; border-radius:8px;
            }
            button { background:#6c63ff; color:white; border:none; }
            a { text-decoration:none; }
            .error { color:red; margin-bottom:10px; }
        </style>
    </head>
    <body>
    <div class="box">
        <h2>Login Admin</h2>
        <?php if (!empty($error_login)): ?>
            <div class="error"><?= $error_login ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="accion" value="login">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Contraseña</label>
            <input type="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
        <a href="sistema.php">← Volver al formulario</a>
    </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Pago</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; padding:30px; }
        .box { max-width:700px; margin:auto; background:white; padding:30px; border-radius:15px; }
        h2 { text-align:center; margin-bottom:20px; }
        input, select, textarea, button {
            width:100%; padding:12px; margin-top:8px; margin-bottom:16px;
            border:1px solid #ccc; border-radius:8px;
        }
        button { background:#6c63ff; color:white; border:none; cursor:pointer; }
        .top-link { text-align:right; margin-bottom:10px; }
        .msg { background:#ecfdf5; color:#065f46; padding:12px; border-radius:8px; margin-bottom:15px; }
        .info { background:#f9fafb; padding:15px; border-radius:10px; border:1px solid #e5e7eb; margin-bottom:20px; }
    </style>
</head>
<body>
<div class="box">
    <div class="top-link">
        <a href="sistema.php?login=1">🔐 Ingresar como Admin</a>
    </div>

    <h2>Formulario de Pago</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="msg"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="info">
        <strong>📌 Importante:</strong><br>
        Realiza tu pago por <strong>Yape / Plin / Transferencia</strong> y sube tu captura aquí.
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="guardar_inscripcion">

        <label>Nombre completo</label>
        <input type="text" name="nombre_completo" required>

        <label>Celular</label>
        <input type="text" name="celular" required>

        <label>Ciudad</label>
        <input type="text" name="ciudad" required>

        <label>Email</label>
        <input type="email" name="email">

        <label>Sucursal</label>
        <select name="sucursal" required>
            <option value="">-- Seleccionar --</option>
            <option value="Ica">Ica</option>
            <option value="Arequipa">Arequipa</option>
        </select>

        <label>¿Qué vas a pagar?</label>
        <select name="catalogo_pago_id" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($catalogo as $item): ?>
                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Comentario adicional (opcional)</label>
        <textarea name="comentario"></textarea>

        <label>Subir captura de pago</label>
        <input type="file" name="captura_pago" accept=".jpg,.jpeg,.png,.pdf" required>

        <button type="submit">Enviar</button>
    </form>
</div>
</body>
</html>
