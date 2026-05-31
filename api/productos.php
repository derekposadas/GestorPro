<?php
require_once 'config.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $db->prepare("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_assoc());
    } else {
        $buscar = isset($_GET['buscar']) ? '%' . $_GET['buscar'] . '%' : '%';
        $cat = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

        if ($cat > 0) {
            $stmt = $db->prepare("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.nombre LIKE ? AND p.categoria_id = ? ORDER BY p.id DESC");
            $stmt->bind_param('si', $buscar, $cat);
        } else {
            $stmt = $db->prepare("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.nombre LIKE ? ORDER BY p.id DESC");
            $stmt->bind_param('s', $buscar);
        }
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param('ssdiiii', $data['nombre'], $data['descripcion'], $data['precio'], $data['stock'], $data['stock_minimo'], $data['categoria_id']);
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $db->insert_id]);
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, stock_minimo=?, categoria_id=? WHERE id=?");
    $stmt->bind_param('ssdiiiii', $data['nombre'], $data['descripcion'], $data['precio'], $data['stock'], $data['stock_minimo'], $data['categoria_id'], $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    $stmt = $db->prepare("DELETE FROM productos WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
}

$db->close();
