<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';

require_method('GET');

try {
	$stmt = $pdo->query('SELECT id, horarios, servicios, telefono, direccion, actualizado_en FROM info_clinica WHERE id = 1');
	$info = $stmt->fetch();
	if ($info && isset($info['servicios'])) {
		$info['servicios_lista'] = array_values(array_filter(array_map('trim', explode(',', (string)$info['servicios']))));
	}
	json_response(['ok' => true, 'info' => $info]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudo obtener la información'], 500);
} 