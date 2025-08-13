<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) json_response(['ok' => false, 'error' => 'ID inválido'], 400);

try {
	$pdo->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]);
	json_response(['ok' => true]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo eliminar el producto'], 500);
} 