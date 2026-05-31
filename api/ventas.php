<?php
require_once 'config.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        // Detalle de venta
        $stmt = $db->prepare("SELECT v.*, c.nombre as cliente FROM ventas v LEFT JOIN clientes c ON v.cliente_id = c.id WHERE v.id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $venta = $stmt->get_result()->fetch_assoc();

        $stmt2 = $db->prepare("SELECT vd.*, p.nombre as producto FROM venta_detalles vd JOIN productos p ON vd.producto_id=p.id WHERE vd.venta_id=?");
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $venta['detalles'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($venta);
    } else {
        // Lista de ventas con filtros
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        $estado = $_GET['estado'] ?? '';

        if ($estado) {
            $stmt = $db->prepare("SELECT v.id, c.nombre as cliente, u.nombre as vendedor, v.total, v.estado, v.creado_en FROM ventas v LEFT JOIN clientes c ON v.cliente_id=c.id LEFT JOIN usuarios u ON v.usuario_id=u.id WHERE v.creado_en BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY) AND v.estado=? ORDER BY v.creado_en DESC");
            $stmt->bind_param('sss', $desde, $hasta, $estado);
        } else {
            $stmt = $db->prepare("SELECT v.id, c.nombre as cliente, u.nombre as vendedor, v.total, v.estado, v.creado_en FROM ventas v LEFT JOIN clientes c ON v.cliente_id=c.id LEFT JOIN usuarios u ON v.usuario_id=u.id WHERE v.creado_en BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY) ORDER BY v.creado_en DESC");
            $stmt->bind_param('ss', $desde, $hasta);
        }
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $db->begin_transaction();
    try {
        // Insertar venta
        $stmt = $db->prepare("INSERT INTO ventas (cliente_id, usuario_id, total, estado) VALUES (?,?,?,'completada')");
        $stmt->bind_param('iid', $data['cliente_id'], $data['usuario_id'], $data['total']);
        $stmt->execute();
        $venta_id = $db->insert_id;

        // Insertar detalles y actualizar stock
        foreach ($data['items'] as $item) {
            $stmt2 = $db->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio_unitario) VALUES (?,?,?,?)");
            $stmt2->bind_param('iiid', $venta_id, $item['producto_id'], $item['cantidad'], $item['precio_unitario']);
            $stmt2->execute();

            $stmt3 = $db->prepare("UPDATE productos SET stock = stock - ? WHERE id=?");
            $stmt3->bind_param('ii', $item['cantidad'], $item['producto_id']);
            $stmt3->execute();
        }
        $db->commit();
        echo json_encode(['success' => true, 'id' => $venta_id]);
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

$db->close();
