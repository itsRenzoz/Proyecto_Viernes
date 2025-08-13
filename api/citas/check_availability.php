<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';

$date = $_GET['date'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
	json_response(['ok' => false, 'error' => 'Fecha inválida, formato YYYY-MM-DD'], 400);
}

$opening = new DateTime($date . ' 09:00:00');
$closing = new DateTime($date . ' 17:00:00');
$interval = new DateInterval('PT30M');

try {
	$stmt = $pdo->prepare('SELECT DATE_FORMAT(fecha_hora, "%H:%i") AS hora FROM citas WHERE DATE(fecha_hora) = ? AND estado <> "cancelada"');
	$stmt->execute([$date]);
	$booked = array_column($stmt->fetchAll(), 'hora');

	$slots = [];
	for ($time = clone $opening; $time <= $closing; $time->add($interval)) {
		$h = $time->format('H:i');
		if (in_array($h, $booked, true)) continue;
		$slots[] = $h;
	}
	json_response(['ok' => true, 'date' => $date, 'slots' => $slots]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo obtener disponibilidad'], 500);
} 