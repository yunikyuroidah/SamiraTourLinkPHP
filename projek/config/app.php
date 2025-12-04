<?php
// Compute a base URL relative to the webroot so assets work whether the project
// is served from the domain root or from a subfolder (e.g. http://localhost/projek).
// Usage: echo asset('src/assets/logo.png');

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($base === '.') $base = '';

define('BASE_URL', $base);

function asset($path) {
    $p = ltrim($path, '/');
    if (defined('BASE_URL') && BASE_URL !== '') {
        return BASE_URL . '/' . $p;
    }
    return '/' . $p;
}

?>