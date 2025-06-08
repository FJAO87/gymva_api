<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Truco para InfinityFree
if (!isset($_COOKIE['__test'])) {
    $a = bin2hex(random_bytes(16));
    $b = bin2hex(random_bytes(16));
    $c = bin2hex(random_bytes(16));
    echo "<html><body><script>
        document.cookie='__test=' + $c + '; path=/';
        location.href='?i=1';
    </script></body></html>";
    exit;
}

$host = "sql7.freesqldatabase.com";
$user = "sql7780578";
$password = "keRF8Iadnc";
$db = "sql7780578";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Conexión fallida"]);
    exit;
}

// Recoger datos JSON desde Flutter
$input = json_decode(file_get_contents("php://input"), true);
$email = $input['email'] ?? '';
$contrasena = $input['contrasena'] ?? '';

if (!$email || !$contrasena) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit;
}

$stmt = $conn->prepare("SELECT id_cliente, nombre_cliente, contrasena_cliente FROM cliente WHERE email_cliente = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($contrasena, $row['contrasena_cliente'])) {

        // ✅ Enviar respuesta JSON a Flutter
        echo json_encode([
            "success" => true,
            "message" => "Login correcto",
            "id_cliente" => $row["id_cliente"],
            "nombre" => $row["nombre_cliente"]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
}

$stmt->close();
$conn->close();
?>