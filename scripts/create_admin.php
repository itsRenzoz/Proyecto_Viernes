<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: text/plain; charset=utf-8');

// Configuración del admin por defecto
$adminName = 'Administrador';
$adminEmail = 'admin@vetagenda.com';
$adminPassword = 'Admin123!';

try {
	// Verificar si existe
	$sel = $pdo->prepare('SELECT id, rol FROM usuarios WHERE email = ? LIMIT 1');
	$sel->execute([$adminEmail]);
	$existing = $sel->fetch();

	if ($existing) {
		// Asegurar rol admin (no cambiamos contraseña por seguridad, salvo ?reset=1)
		$pdo->prepare('UPDATE usuarios SET rol = "admin" WHERE id = ?')->execute([(int)$existing['id']]);
		if (isset($_GET['reset']) && $_GET['reset'] === '1') {
			$hash = password_hash($adminPassword, PASSWORD_DEFAULT);
			$pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')->execute([$hash, (int)$existing['id']]);
			echo "Admin actualizado y contraseña reseteada.\n";
		} else {
			echo "Admin existente actualizado (rol asegurado).\n";
		}
		echo "Email: {$adminEmail}\nContraseña: {$adminPassword}\n";
		exit;
	}

	// Crear nuevo admin
	$hash = password_hash($adminPassword, PASSWORD_DEFAULT);
	$ins = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, "admin")');
	$ins->execute([$adminName, $adminEmail, $hash]);
	echo "Admin creado correctamente.\nEmail: {$adminEmail}\nContraseña: {$adminPassword}\n";
	echo "Sugerencia: borre este archivo (scripts/create_admin.php) después de usarlo.\n";
} catch (Throwable $e) {
	http_response_code(500);
	echo 'Error: ' . $e->getMessage();
} 