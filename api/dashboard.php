<?php
require_once 'config.php';
$db = getDB();

// Total ventas del mes
$r1 = $db->query("SELECT COUNT(*) as total, SUM(total) as ingresos FROM ventas WHERE estado='completada' AND MONTH(creado_en)=MONTH(NOW()) AND YEAR(creado_en)=YEAR(NOW())");
$ventas_mes = $r1->fetch_assoc();

// Total productos y alertas de stock bajo
$r2 = $db->query("SELECT COUNT(*) as total FROM productos");
$total_productos = $r2->fetch_assoc()['total'];

$r3 = $db->query("SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo");
$stock_bajo = $r3->fetch_assoc()['total'];

// Total clientes
$r4 = $db->query("SELECT COUNT(*) as total FROM clientes");
$total_clientes = $r4->fetch_assoc()['total'];

// Ventas por día (últimos 7 días)
$r5 = $db->query("
    SELECT DATE(creado_en) as dia, SUM(total) as total
    FROM ventas
    WHERE estado='completada' AND creado_en >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(creado_en)
    ORDER BY dia ASC
");
$ventas_semana = $r5->fetch_all(MYSQLI_ASSOC);

// Top productos más vendidos
$r6 = $db->query("
    SELECT p.nombre, SUM(vd.cantidad) as unidades
    FROM venta_detalles vd
    JOIN productos p ON vd.producto_id = p.id
    JOIN ventas v ON vd.venta_id = v.id
    WHERE v.estado='completada'
    GROUP BY p.id
    ORDER BY unidades DESC
    LIMIT 5
");
$top_productos = $r6->fetch_all(MYSQLI_ASSOC);

// Últimas 5 ventas
$r7 = $db->query("
    SELECT v.id, c.nombre as cliente, v.total, v.estado, v.creado_en
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    ORDER BY v.creado_en DESC
    LIMIT 5
");
$ultimas_ventas = $r7->fetch_all(MYSQLI_ASSOC);

// Productos con stock bajo
$r8 = $db->query("SELECT nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo ORDER BY stock ASC");
$alertas_stock = $r8->fetch_all(MYSQLI_ASSOC);

$db->close();

echo json_encode([
    'ventas_mes' => (int)($ventas_mes['total'] ?? 0),
    'ingresos_mes' => (float)($ventas_mes['ingresos'] ?? 0),
    'total_productos' => (int)$total_productos,
    'stock_bajo' => (int)$stock_bajo,
    'total_clientes' => (int)$total_clientes,
    'ventas_semana' => $ventas_semana,
    'top_productos' => $top_productos,
    'ultimas_ventas' => $ultimas_ventas,
    'alertas_stock' => $alertas_stock
]);
