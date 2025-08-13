<?php
function json_response(array $data, int $status = 200): void {
	http_response_code($status);
	header('Content-Type: application/json');
	echo json_encode($data);
	exit;
}

function require_method(string $method): void {
	if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
		json_response(['ok' => false, 'error' => 'Método no permitido'], 405);
	}
} 