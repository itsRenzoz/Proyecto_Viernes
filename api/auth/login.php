<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
$input = json_decode(file_get_contents('php://input'), true) ?? [];

$email = strtolower(trim($input['email'] ?? ''));
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
	json_response(['ok' => false, 'error' => 'Email y contraseña requeridos'], 400);
}

try {
	$stmt = $pdo->prepare('SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ? LIMIT 1');
	$stmt->execute([$email]);
	$user = $stmt->fetch();
	if (!$user || !password_verify($password, $user['password_hash'])) {
		json_response(['ok' => false, 'error' => 'Credenciales inválidas'], 401);
	}

	set_user_session($user);
	json_response(['ok' => true, 'user' => ['id' => (int)$user['id'], 'nombre' => $user['nombre'], 'email' => $user['email'], 'rol' => $user['rol']]]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo iniciar sesión'], 500);
} 