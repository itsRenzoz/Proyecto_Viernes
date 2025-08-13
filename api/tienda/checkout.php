<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

require_method('POST');
require_auth();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$items = $input['items'] ?? [];
if (!is_array($items) || count($items) === 0) {
	json_response(['ok' => false, 'error' => 'No hay items para comprar'], 400);
}

try {
	$pdo->beginTransaction();
	$total = 0;
	$validatedItems = [];
	foreach ($items as $it) {
		$pid = (int)($it['producto_id'] ?? 0);
		$cant = (int)($it['cantidad'] ?? 0);
		if ($pid <= 0 || $cant <= 0) {
			throw new Exception('Item inválido');
		}
		$q = $pdo->prepare('SELECT id, nombre, precio, stock FROM productos WHERE id = ? AND activo = 1 FOR UPDATE');
		$q->execute([$pid]);
		$prod = $q->fetch();
		if (!$prod) throw new Exception('Producto no disponible');
		if ($prod['stock'] < $cant) throw new Exception('Stock insuficiente para ' . $prod['nombre']);
		$total += $prod['precio'] * $cant;
		$validatedItems[] = ['id' => (int)$prod['id'], 'precio' => (float)$prod['precio'], 'cantidad' => $cant];
	}

	$userId = (int)$_SESSION['user']['id'];
	$insPedido = $pdo->prepare('INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, "pagado")');
	$insPedido->execute([$userId, $total]);
	$pedidoId = (int)$pdo->lastInsertId();

	$insItem = $pdo->prepare('INSERT INTO pedido_items (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
	$updStock = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');
	foreach ($validatedItems as $vi) {
		$insItem->execute([$pedidoId, $vi['id'], $vi['cantidad'], $vi['precio']]);
		$updStock->execute([$vi['cantidad'], $vi['id']]);
	}

	$pdo->commit();
	json_response(['ok' => true, 'pedido_id' => $pedidoId, 'total' => $total]);
} catch (Throwable $e) {
	if ($pdo->inTransaction()) $pdo->rollBack();
	json_response(['ok' => false, 'error' => $e->getMessage()], 400);
} 