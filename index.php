<?php
// Encaminador básico para Render
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/login.php':
        require __DIR__ . '/login.php';
        break;
    default:
        echo json_encode(["status" => "API Gymva funcionando"]);
        break;
}