<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = isset($input['id']) ? (int)$input['id'] : 0;
$nombre = trim($input['nombre'] ?? '');
$descripcion = trim($input['descripcion'] ?? '');
$precio = isset($input['precio']) ? (float)$input['precio'] : null;
$stock = isset($input['stock']) ? (int)$input['stock'] : null;
$activo = isset($input['activo']) ? (int)!!$input['activo'] : 1;

if ($nombre === '' || $precio === null || $stock === null || $precio < 0 || $stock < 0) {
	json_response(['ok' => false, 'error' => 'Datos inválidos'], 400);
}

try {
	if ($id > 0) {
		$stmt = $pdo->prepare('UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, activo = ? WHERE id = ?');
		$stmt->execute([$nombre, $descripcion, $precio, $stock, $activo, $id]);
		json_response(['ok' => true, 'id' => $id]);
	} else {
		$stmt = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, activo) VALUES (?, ?, ?, ?, ?)');
		$stmt->execute([$nombre, $descripcion, $precio, $stock, $activo]);
		json_response(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
	}
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo guardar el producto'], 500);
} 