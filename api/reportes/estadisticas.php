<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('GET');
require_admin();

try {
	$totales = $pdo->query('SELECT COUNT(*) AS total, SUM(estado = "pendiente") AS pendientes, SUM(estado = "confirmada") AS confirmadas, SUM(estado = "cancelada") AS canceladas FROM citas')->fetch();
	$ventas = $pdo->query('SELECT COUNT(*) AS pedidos, COALESCE(SUM(total),0) AS total_ventas FROM pedidos WHERE estado = "pagado"')->fetch();
	$topProductos = $pdo->query('SELECT p.nombre, SUM(i.cantidad) AS vendidos FROM pedido_items i JOIN productos p ON p.id = i.producto_id GROUP BY p.id ORDER BY vendidos DESC LIMIT 5')->fetchAll();
	json_response(['ok' => true, 'citas' => $totales, 'ventas' => $ventas, 'top_productos' => $topProductos]);
} catch (Throwable $e) {
	json_response(['ok' => false, 'error' => 'No se pudieron generar estadísticas'], 500);
} 