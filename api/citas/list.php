<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('GET');
start_session_if_needed();

$isAdmin = (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? 'cliente') === 'admin');
$all = isset($_GET['all']) && $isAdmin;
$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : '';
$allowedStatus = ['pendiente','confirmada','cancelada'];
if ($status !== '' && !in_array($status, $allowedStatus, true)) {
	json_response(['ok' => false, 'error' => 'Estado inválido'], 400);
}

try {
	if ($all) {
		if ($status !== '') {
			$stmt = $pdo->prepare('SELECT c.id, c.fecha_hora, c.estado, c.nombre_mascota, c.motivo, u.nombre AS usuario, u.email AS email FROM citas c JOIN usuarios u ON u.id = c.usuario_id WHERE c.estado = ? ORDER BY c.fecha_hora DESC');
			$stmt->execute([$status]);
		} else {
			$stmt = $pdo->query('SELECT c.id, c.fecha_hora, c.estado, c.nombre_mascota, c.motivo, u.nombre AS usuario, u.email AS email FROM citas c JOIN usuarios u ON u.id = c.usuario_id ORDER BY c.fecha_hora DESC');
		}
	} else {
		require_auth();
		$userId = (int)$_SESSION['user']['id'];
		if ($status !== '') {
			$stmt = $pdo->prepare('SELECT id, fecha_hora, estado, nombre_mascota, motivo FROM citas WHERE usuario_id = ? AND estado = ? ORDER BY fecha_hora DESC');
			$stmt->execute([$userId, $status]);
		} else {
			$stmt = $pdo->prepare('SELECT id, fecha_hora, estado, nombre_mascota, motivo FROM citas WHERE usuario_id = ? ORDER BY fecha_hora DESC');
			$stmt->execute([$userId]);
		}
	}
	$citas = $stmt->fetchAll();
	json_response(['ok' => true, 'citas' => $citas]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudieron obtener las citas'], 500);
} 