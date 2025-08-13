<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$citaId = (int)($input['cita_id'] ?? 0);
if ($citaId <= 0) {
	json_response(['ok' => false, 'error' => 'cita_id requerido'], 400);
}

try {
	$stmt = $pdo->prepare('SELECT c.id, c.usuario_id, u.email, u.nombre FROM citas c JOIN usuarios u ON u.id = c.usuario_id WHERE c.id = ?');
	$stmt->execute([$citaId]);
	$cita = $stmt->fetch();
	if (!$cita) json_response(['ok' => false, 'error' => 'Cita no encontrada'], 404);

	$mensaje = 'Recordatorio: Tiene una cita programada. (ID: ' . $citaId . ')';
	$ins = $pdo->prepare('INSERT INTO notificaciones (usuario_id, cita_id, canal, mensaje, estado) VALUES (?, ?, "ui", ?, "enviada")');
	$ins->execute([(int)$cita['usuario_id'], $citaId, $mensaje]);

	json_response(['ok' => true]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo crear el recordatorio'], 500);
} 