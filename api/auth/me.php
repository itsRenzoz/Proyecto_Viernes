<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../_utils/response.php';
require_once __DIR__ . '/../_utils/auth.php';

// GET /api/auth/me.php
start_session_if_needed();
$user = $_SESSION['user'] ?? null;
if (!$user) {
	json_response(['ok' => true, 'user' => null]);
}
json_response(['ok' => true, 'user' => $user]); 