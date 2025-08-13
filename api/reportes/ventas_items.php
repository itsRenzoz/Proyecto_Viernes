<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('GET');
require_admin();

try {
	$sql = 'SELECT i.pedido_id, p.nombre AS producto, i.cantidad, i.precio_unitario, pe.creado_en
		FROM pedido_items i
		JOIN productos p ON p.id = i.producto_id
		JOIN pedidos pe ON pe.id = i.pedido_id
		ORDER BY pe.creado_en DESC, i.pedido_id DESC';
	$rows = $pdo->query($sql)->fetchAll();
	json_response(['ok' => true, 'items' => $rows]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudieron obtener las ventas'], 500);
} 