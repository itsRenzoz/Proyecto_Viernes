<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
$input = json_decode(file_get_contents('php://input'), true) ?? [];

$nombre = trim($input['nombre'] ?? '');
$email = strtolower(trim($input['email'] ?? ''));
$password = $input['password'] ?? '';

if ($nombre === '' || $email === '' || $password === '') {
	json_response(['ok' => false, 'error' => 'Campos incompletos'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	json_response(['ok' => false, 'error' => 'Email inválido'], 400);
}
if (strlen($password) < 6) {
	json_response(['ok' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
}

try {
	$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
	$stmt->execute([$email]);
	if ($stmt->fetch()) {
		json_response(['ok' => false, 'error' => 'El email ya está registrado'], 409);
	}

	$hash = password_hash($password, PASSWORD_DEFAULT);
	$insert = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, "cliente")');
	$insert->execute([$nombre, $email, $hash]);

	$id = (int)$pdo->lastInsertId();
	set_user_session(['id' => $id, 'nombre' => $nombre, 'email' => $email, 'rol' => 'cliente']);
	json_response(['ok' => true, 'user' => ['id' => $id, 'nombre' => $nombre, 'email' => $email, 'rol' => 'cliente']]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo registrar'], 500);
} 