<?php
// Configuración de entorno y constantes
// Se pueden sobrescribir con variables de entorno en XAMPP si se desea

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'vetagenda');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '123456');

// Ruta base si se despliega en subcarpeta (ajustar según corresponda)
define('BASE_URL', getenv('BASE_URL') ?: '/Proyecto_Viernes/');

// Zona horaria por defecto
if (!ini_get('date.timezone')) {
	date_default_timezone_set('America/Costa_Rica');
} 