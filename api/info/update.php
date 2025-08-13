<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$horarios = $input['horarios'] ?? null;
$servicios = $input['servicios'] ?? null;
$telefono = $input['telefono'] ?? null;
$direccion = $input['direccion'] ?? null;

try {
	$upd = $pdo->prepare('UPDATE info_clinica SET horarios = ?, servicios = ?, telefono = ?, direccion = ? WHERE id = 1');
	$upd->execute([$horarios, $servicios, $telefono, $direccion]);
	json_response(['ok' => true]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo actualizar la información'], 500);
} 