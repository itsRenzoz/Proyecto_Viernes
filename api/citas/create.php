<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
require_auth();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$date = $input['date'] ?? '';
$time = $input['time'] ?? '';
$mascota = trim($input['mascota'] ?? '');
$motivo = strtolower(trim($input['motivo'] ?? ''));

$validMotivos = ['consultas','vacunacion','cirugias'];
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time) || $mascota === '' || !in_array($motivo, $validMotivos, true)) {
	json_response(['ok' => false, 'error' => 'Datos inválidos'], 400);
}

$fechaHora = $date . ' ' . $time . ':00';
$userId = (int)($_SESSION['user']['id'] ?? 0);

try {
	$pdo->beginTransaction();
	// Verificar si ya está ocupada
	$check = $pdo->prepare('SELECT id FROM citas WHERE fecha_hora = ? AND estado <> "cancelada" LIMIT 1');
	$check->execute([$fechaHora]);
	if ($check->fetch()) {
		$pdo->rollBack();
		json_response(['ok' => false, 'error' => 'Horario no disponible'], 409);
	}
	$insert = $pdo->prepare('INSERT INTO citas (usuario_id, nombre_mascota, motivo, fecha_hora, estado) VALUES (?, ?, ?, ?, "pendiente")');
	$insert->execute([$userId, $mascota, $motivo, $fechaHora]);
	$citaId = (int)$pdo->lastInsertId();

	// Crear notificación interna (UI)
	$notif = $pdo->prepare('INSERT INTO notificaciones (usuario_id, cita_id, canal, mensaje, estado) VALUES (?, ?, "ui", ?, "pendiente")');
	$notif->execute([$userId, $citaId, 'Tu cita ha sido registrada.']);

	$pdo->commit();
	json_response(['ok' => true, 'cita' => ['id' => $citaId, 'fecha_hora' => $fechaHora, 'nombre_mascota' => $mascota, 'motivo' => $motivo, 'estado' => 'pendiente']]);
} catch (Throwable $e) {
	if ($pdo->inTransaction()) $pdo->rollBack();
	json_response(['ok' => false, 'error' => 'No se pudo crear la cita'], 500);
} 