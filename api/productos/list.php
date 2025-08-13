<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('GET');

$all = isset($_GET['all']) && $_GET['all'] == '1';

try {
	if ($all) {
		start_session_if_needed();
		$isAdmin = (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? 'cliente') === 'admin');
		if (!$isAdmin) {
			json_response(['ok' => false, 'error' => 'No autorizado'], 401);
		}
		$stmt = $pdo->query('SELECT id, nombre, descripcion, precio, stock, imagen_url, activo, creado_en FROM productos ORDER BY creado_en DESC');
	} else {
		$stmt = $pdo->query('SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos WHERE activo = 1 ORDER BY creado_en DESC');
	}
	$productos = $stmt->fetchAll();
	json_response(['ok' => true, 'productos' => $productos]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudieron obtener los productos'], 500);
} 