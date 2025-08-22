<?php
// Configuración de entorno y constantes
// Se pueden sobrescribir con variables de entorno en XAMPP si se desea
// Se debe de cambiar la información de la configuración en caso de que no sea la misma en el localhost especifico

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3307');
define('DB_NAME', getenv('DB_NAME') ?: 'vetagenda');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '12345');

// Ruta base si se despliega en subcarpeta (ajustar según corresponda)
define('BASE_URL', getenv('BASE_URL') ?: 'C:\xampp\htdocs\proyecto\Proyecto_Viernes');

// Zona horaria por defecto
if (!ini_get('date.timezone')) {
	date_default_timezone_set('America/Costa_Rica');
} 