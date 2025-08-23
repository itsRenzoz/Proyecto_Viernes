<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Gestión de Citas Veterinarias - Reservar Cita</title>
	<link rel="stylesheet" href="css/estilos.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="reservas">
<?php include 'partials/header.html'; ?>


	<main>
		<section>
			<h2>Reservar una Cita</h2>
			<p>Selecciona una fecha y hora para tu cita con nuestra clínica veterinaria.</p>
			<div class="calendario">
				<h3>Calendario Interactivo</h3>
				<div id="calendar" class="cal-widget"></div>
			</div>
			<div id="time-slots" style="margin-top:10px"></div>
			<form id="reservar-form">
				<label for="fecha">Fecha:</label>
				<input type="text" id="fecha" name="fecha" placeholder="YYYY-MM-DD" readonly>
				<label for="hora">Hora:</label>
				<input type="text" id="hora" name="hora" placeholder="HH:MM" readonly>
				<label for="motivo">Motivo de la cita:</label>
				<select id="motivo" name="motivo" required>
					<option value="consultas">Consultas</option>
					<option value="vacunacion">Vacunación</option>
					<option value="cirugias">Cirugías</option>
				</select>
				<label for="mascota">Nombre de la Mascota:</label>
				<input type="text" id="mascota" name="mascota" required>
				<button type="submit">Reservar</button>
				<div class="form-message" id="reservar-message"></div>
			</form>
		</section>
	</main>
	<footer>
		<p>&copy; 2025 VetAgenda. Todos los derechos reservados.</p>
	</footer>
	<script src="js/app.js"></script>
</body>
</html>