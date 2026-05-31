<?php
require_once 'config.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = md5($data['password'] ?? '');

    $db = getDB();
    $stmt = $db->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE email = ? AND password = ? AND activo = 1");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $db->close();

    if ($user) {
        $_SESSION['usuario'] = $user;
        echo json_encode(['success' => true, 'usuario' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Email o contraseña incorrectos']);
    }
}

if ($method === 'DELETE') {
    session_destroy();
    echo json_encode(['success' => true]);
}

// GET - verificar sesión activa
if ($method === 'GET') {
    if (isset($_SESSION['usuario'])) {
        echo json_encode(['loggedIn' => true, 'usuario' => $_SESSION['usuario']]);
    } else {
        echo json_encode(['loggedIn' => false]);
    }
}
