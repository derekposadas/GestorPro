<?php
require_once 'config.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$endpoint = $_GET['tipo'] ?? 'clientes';

if ($endpoint === 'categorias') {
    $result = $db->query("SELECT * FROM categorias ORDER BY nombre ASC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $db->close(); exit;
}

// CLIENTES
if ($method === 'GET') {
    $result = $db->query("SELECT * FROM clientes ORDER BY nombre ASC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare("INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $data['nombre'], $data['email'], $data['telefono'], $data['direccion']);
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $db->insert_id]);
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare("UPDATE clientes SET nombre=?, email=?, telefono=?, direccion=? WHERE id=?");
    $stmt->bind_param('ssssi', $data['nombre'], $data['email'], $data['telefono'], $data['direccion'], $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    $stmt = $db->prepare("DELETE FROM clientes WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
}

$db->close();
