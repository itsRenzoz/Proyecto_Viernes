<?php
require_once __DIR__ . '/response.php';

function start_session_if_needed(): void {
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
}

function set_user_session(array $user): void {
	start_session_if_needed();
	$_SESSION['user'] = [
		'id' => (int)$user['id'],
		'name' => $user['nombre'] ?? $user['name'] ?? '',
		'email' => $user['email'] ?? '',
		'role' => $user['rol'] ?? $user['role'] ?? 'cliente',
	];
}

function require_auth(): void {
	start_session_if_needed();
	if (empty($_SESSION['user'])) {
		json_response(['ok' => false, 'error' => 'No autorizado'], 401);
	}
}

function require_admin(): void {
	require_auth();
	if (($_SESSION['user']['role'] ?? 'cliente') !== 'admin') {
		json_response(['ok' => false, 'error' => 'Prohibido'], 403);
	}
} 