<?php
require_once 'config.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    $result = $db->query("SELECT id, nombre, email, rol, activo, creado_en FROM usuarios ORDER BY id ASC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $pass = md5($data['password']);
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $data['nombre'], $data['email'], $pass, $data['rol']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $db->insert_id]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'El email ya existe']);
    }
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['password'])) {
        $pass = md5($data['password']);
        $stmt = $db->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, activo=?, password=? WHERE id=?");
        $stmt->bind_param('sssisi', $data['nombre'], $data['email'], $data['rol'], $data['activo'], $pass, $id);
    } else {
        $stmt = $db->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, activo=? WHERE id=?");
        $stmt->bind_param('sssii', $data['nombre'], $data['email'], $data['rol'], $data['activo'], $id);
    }
    $stmt->execute();
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    $stmt = $db->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
}

$db->close();
