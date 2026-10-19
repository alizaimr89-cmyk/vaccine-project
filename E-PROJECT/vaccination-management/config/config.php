<?php
// Application settings. The base URL is detected so the project works whether
// installed directly in htdocs or inside a parent folder such as E-prjoect.
define('APP_NAME', 'NurtureDose');
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/vaccination-management/index.php');
$marker = '/vaccination-management/';
$markerPosition = strpos($scriptPath, $marker);
if ($markerPosition !== false) {
    define('BASE_URL', substr($scriptPath, 0, $markerPosition) . '/vaccination-management');
} else {
    define('BASE_URL', '/vaccination_management');
}
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'vaccination_management');
define('DB_USER', 'root');
define('DB_PASS', '');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
