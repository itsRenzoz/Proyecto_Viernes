<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Gestión de Citas Veterinarias - Iniciar Sesión</title>
	<link rel="stylesheet" href="css/estilos.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Roboto:wght@400;700&display=swap"
		rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	
<script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
</head>

<body class="auth-page">
	<div id="header"></div>

	<main>
		<section class="auth-hero">
			<div class="form-container one-col">
				<div class="auth-card">
					<div class="auth-icon">👤</div>
					<div class="auth-title">INICIAR SESIÓN</div>

					<form id="login-form" class="glass-panel">

						<label for="email" class="sr-only">Correo Electrónico</label>
						<div class="input-group underline">
							<span class="icon" aria-hidden="true">✉</span>
							<input type="email" id="email" name="email" placeholder="Correo electrónico" required>
						</div>
						<label for="password" class="sr-only">Contraseña</label>
						<div class="input-group underline">
							<span class="icon" aria-hidden="true">🔒</span>
							<input type="password" id="password" name="password" placeholder="Contraseña" required>
							<button type="button" class="toggle-password" data-target="password" aria-label="Mostrar u ocultar contraseña">
								<i class="fas fa-eye"></i>
							</button>
						</div>

						<div class="auth-row">
							<label class="remember"><input type="checkbox" id="remember"> Recordarme</label>
							<a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
						</div>

						<button type="submit" class="primary">LOGIN</button>
						<div class="form-message" id="login-message"></div>
						<div class="auth-switch">
							¿No tienes cuenta? <a href="#" id="link-show-register">Crear cuenta</a>
						</div>
					</form>
				</div>
				<div class="auth-card" id="register-panel" style="display:none">
					<div class="auth-icon">👤</div>
					<div class="auth-title">REGISTRARSE</div>

					<form id="register-form" class="glass-panel">
						<label for="nombre" class="sr-only">Nombre</label>
						<div class="input-group underline">
							<span class="icon" aria-hidden="true">👤</span>
							<input type="text" id="nombre" name="nombre" placeholder="Nombre completo" required>
						</div>

						<label for="email-registro" class="sr-only">Correo Electrónico</label>
						<div class="input-group underline">
							<span class="icon" aria-hidden="true">✉</span>
							<input type="email" id="email-registro" name="email-registro" placeholder="Correo electrónico" required>
						</div>

						<label for="password-registro" class="sr-only">Contraseña</label>
						<div class="input-group underline">
							<span class="icon" aria-hidden="true">🔒</span>
							<input type="password" id="password-registro" name="password-registro" placeholder="Contraseña" required>
							<button type="button" class="toggle-password" data-target="password-registro" aria-label="Mostrar u ocultar contraseña">
								<i class="fas fa-eye"></i></button>
						</div>

						<button type="submit" class="primary">Crear cuenta</button>
						<div class="form-message" id="register-message"></div>
						<div class="auth-switch">
							¿Ya tienes cuenta? <a href="#" id="link-show-login">Iniciar sesión</a>
						</div>
					</form>
				</div>
			</div>
		</section>
	</main>
	<footer>
		<p>&copy; 2025 VetAgenda. Todos los derechos reservados.</p>
	</footer>
	<script src="js/app.js"></script>
</body>
</html>