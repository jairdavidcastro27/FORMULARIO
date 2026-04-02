<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webinar Premium • Certificación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Urbanist:wght@700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff00c8;
            --secondary: #00f2ff;
            --accent: #7000ff;
            --bg-dark: #050510;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: white;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* FONDO ANIMADO DE GRADIENTES */
        .aurora-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 0% 0%, rgba(112, 0, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 0%, rgba(255, 0, 200, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 100%, rgba(0, 242, 255, 0.1) 0%, transparent 50%);
        }

        /* CONTENEDOR PRINCIPAL */
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.8);
            overflow: hidden;
            max-width: 1100px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
        }

        /* SECCIÓN IZQUIERDA (VISUAL) */
        .visual-side {
            background: linear-gradient(135deg, rgba(112, 0, 255, 0.8), rgba(255, 0, 200, 0.6));
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .visual-side::after {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            opacity: 0.1;
            top: 0; left: 0;
        }

        .title-huge {
            font-family: 'Urbanist', sans-serif;
            font-size: 3.5rem;
            line-height: 1;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 20px;
            text-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .floating-img {
            width: 100%;
            max-width: 320px;
            border-radius: 30px;
            transform: perspective(800px) rotateY(-15deg) rotateX(10deg);
            box-shadow: -20px 20px 50px rgba(0,0,0,0.5);
            margin-top: 30px;
            border: 4px solid rgba(255,255,255,0.2);
            transition: 0.5s;
        }

        .floating-img:hover {
            transform: perspective(800px) rotateY(0deg) scale(1.05);
        }

        /* SECCIÓN DERECHA (FORMULARIO) */
        .form-side {
            padding: 60px;
            background: rgba(10, 10, 25, 0.4);
        }

        .form-label-fancy {
            color: var(--secondary);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
            display: block;
        }

        .input-group-fancy {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            transition: 0.3s;
        }

        .input-group-fancy:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(255, 0, 200, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }

        .input-group-fancy i {
            color: var(--secondary);
            font-size: 1.2rem;
            margin-right: 15px;
        }

        .input-group-fancy input, .input-group-fancy select, .input-group-fancy textarea {
            background: transparent;
            border: none;
            color: white;
            width: 100%;
            outline: none;
            font-weight: 500;
        }

        .upload-zone {
            border: 3px dashed rgba(0, 242, 255, 0.3);
            border-radius: 25px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: rgba(0, 242, 255, 0.02);
        }

        .upload-zone:hover {
            border-color: var(--secondary);
            background: rgba(0, 242, 255, 0.08);
        }

        .btn-action {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border: none;
            border-radius: 20px;
            color: white;
            width: 100%;
            padding: 18px;
            font-weight: 800;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 15px 30px rgba(112, 0, 255, 0.4);
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(112, 0, 255, 0.6);
            filter: brightness(1.1);
        }

        /* RESPONSIVO */
        @media (max-width: 992px) {
            .glass-card { grid-template-columns: 1fr; }
            .visual-side { padding: 40px; text-align: center; align-items: center; }
            .title-huge { font-size: 2.5rem; }
            .form-side { padding: 30px; }
        }
    </style>
</head>
<body>

<div class="aurora-bg"></div>

<div class="main-container">
    <div class="glass-card">
        <div class="visual-side">
            <div class="mb-3">
                <span style="background: rgba(0,0,0,0.3); padding: 8px 15px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(255,255,255,0.2);">
                    🚀 WEBINAR EXCLUSIVO 2026
                </span>
            </div>
            <h1 class="title-huge">Consigue tu <br><span style="color: var(--secondary);">Certificado</span></h1>
            <p class="opacity-75">Valida tus conocimientos y destaca en el mercado laboral con nuestra certificación profesional oficial.</p>
            
            <img src="imagen/metodosPago.jpeg" alt="Certificación" class="floating-img">
            
            <div class="mt-auto pt-4 d-flex gap-3 justify-content-center">
                <div class="text-center">
                    <h4 class="mb-0 fw-bold">100%</h4>
                    <small class="opacity-50">Digital</small>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.2);"></div>
                <div class="text-center">
                    <h4 class="mb-0 fw-bold">PRO</h4>
                    <small class="opacity-50">Calidad</small>
                </div>
            </div>
        </div>

        <div class="form-side">
            <h3 class="fw-800 mb-4">Registro de Pago</h3>
            
            <form action="sistema.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="guardar_inscripcion">
                <input type="hidden" name="catalogo_pago_id" value="<?= $certificado['id'] ?>">

                <div>
                    <label class="form-label-fancy">Identificación</label>
                    <div class="input-group-fancy">
                        <i class="fas fa-fingerprint"></i>
                            <input type="text" name="dni" placeholder="Ingresa tu DNI" maxlength="8" required>
                    </div>
                </div>

                <div>
                    <label class="form-label-fancy">Nombre Completo</label>
                    <div class="input-group-fancy">
                        <i class="fas fa-user-astronaut"></i>
                        <input type="text" name="nombre_completo" placeholder="Escribe tu nombre aquí" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-fancy">WhatsApp</label>
                        <div class="input-group-fancy">
                            <i class="fab fa-whatsapp"></i>
                            <input type="tel" name="celular" placeholder="Celular" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-fancy">Sucursal</label>
                        <div class="input-group-fancy">
                            <i class="fas fa-map-marker-alt"></i>
                            <select name="sucursal" required style="background: transparent; color: white; border: none; width: 100%;">
                                <option value="" disabled selected style="color: black;">Seleccionar</option>
                                <option value="Ica" style="color: black;">Ica</option>
                                <option value="Arequipa" style="color: black;">Arequipa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <label class="form-label-fancy">Comprobante de Pago</label>
                <div class="upload-zone mb-4" id="dropzone" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color: var(--secondary);"></i>
                    <p class="mb-0 fw-bold">Sube tu captura aquí</p>
                    <small class="opacity-50">Click para seleccionar (JPG o PNG)</small>
                    <input type="file" id="fileInput" name="captura_pago" hidden required accept="image/*">
                </div>
                <div id="file-name" class="text-center mb-3 text-info fw-bold"></div>

                <button type="submit" class="btn-action">
                    <i class="fas fa-rocket me-2"></i> Confirmar Inscripción
                </button>
            </form>

            <div class="mt-4 text-center">
                    <a href="index.php?login=1" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-user-lock me-1"></i> Acceso Staff
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('file-name');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${this.files[0].name}`;
            document.getElementById('dropzone').style.borderColor = "#00f2ff";
        }
    });
</script>

</body>
</html>