<?php
require __DIR__ . '/config/db.php';
header('Content-Type: text/plain; charset=utf-8');
echo 'Conexión OK a ' . DB_NAME . ' en puerto ' . DB_PORT . "\n";
try {
	$stmt = $pdo->query('SELECT NOW() AS now');
	$row = $stmt->fetch();
	echo 'Hora del servidor MySQL: ' . ($row['now'] ?? '') . "\n";
} catch (Throwable $e) {
	echo 'Consulta falló: ' . $e->getMessage();
} 