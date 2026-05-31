-- ============================================
-- SISTEMA DE INVENTARIO Y VENTAS - DAM Portfolio
-- ============================================

CREATE DATABASE IF NOT EXISTS inventario_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_db;

-- Tabla de usuarios y roles
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'vendedor', 'almacen') NOT NULL DEFAULT 'vendedor',
    activo TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
);

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    categoria_id INT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de ventas
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    usuario_id INT,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('completada', 'pendiente', 'cancelada') DEFAULT 'completada',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de líneas de venta
CREATE TABLE venta_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- ============================================
-- DATOS DE EJEMPLO
-- ============================================

-- Usuarios (password: "1234" hasheado con MD5 para simplicidad)
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin Principal', 'admin@tienda.com', MD5('1234'), 'admin'),
('Carlos Vendedor', 'carlos@tienda.com', MD5('1234'), 'vendedor'),
('Laura Almacén', 'laura@tienda.com', MD5('1234'), 'almacen');

-- Categorías
INSERT INTO categorias (nombre, descripcion) VALUES
('Electrónica', 'Dispositivos y gadgets electrónicos'),
('Ropa', 'Prendas de vestir y accesorios'),
('Hogar', 'Artículos para el hogar y decoración'),
('Alimentación', 'Productos alimenticios y bebidas');

-- Productos
INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id) VALUES
('Auriculares Bluetooth', 'Auriculares inalámbricos con cancelación de ruido', 49.99, 25, 5, 1),
('Teclado USB', 'Teclado mecánico compacto', 34.99, 15, 3, 1),
('Ratón Óptico', 'Ratón inalámbrico ergonómico', 19.99, 30, 5, 1),
('Camiseta Básica', 'Camiseta de algodón 100%', 12.99, 50, 10, 2),
('Vaqueros Slim', 'Pantalón vaquero ajustado', 39.99, 20, 5, 2),
('Lámpara de Escritorio', 'Lámpara LED regulable', 24.99, 12, 3, 3),
('Almohada Viscoelástica', 'Almohada ergonómica memoria de forma', 29.99, 8, 2, 3),
('Café Molido 500g', 'Café arábica de tueste medio', 8.99, 40, 10, 4),
('Aceite de Oliva 1L', 'Aceite virgen extra', 6.99, 35, 10, 4),
('Cable HDMI 2m', 'Cable HDMI 4K ultra HD', 9.99, 4, 5, 1);

-- Clientes
INSERT INTO clientes (nombre, email, telefono, direccion) VALUES
('Ana García', 'ana@gmail.com', '612345678', 'Calle Mayor 1, Barcelona'),
('Pedro López', 'pedro@gmail.com', '623456789', 'Av. Diagonal 45, Barcelona'),
('María Martínez', 'maria@gmail.com', '634567890', 'Passeig de Gràcia 10, Barcelona'),
('Juan Sánchez', 'juan@gmail.com', '645678901', 'Calle Aragó 200, Barcelona'),
('Isabel Fernández', 'isabel@gmail.com', '656789012', 'Gran Via 300, Barcelona');

-- Ventas (últimos 30 días)
INSERT INTO ventas (cliente_id, usuario_id, total, estado, creado_en) VALUES
(1, 2, 69.98, 'completada', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 2, 34.99, 'completada', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 2, 149.97, 'completada', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 2, 52.98, 'completada', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(5, 2, 24.99, 'completada', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(1, 2, 39.99, 'completada', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(2, 2, 29.97, 'completada', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(3, 2, 89.97, 'completada', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(4, 2, 19.99, 'pendiente', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 2, 15.98, 'cancelada', DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Detalles de ventas
INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio_unitario) VALUES
(1, 1, 1, 49.99), (1, 4, 1, 12.99), (1, 9, 1, 6.99),  -- venta 1: auriculares + camiseta + aceite
(2, 2, 1, 34.99),                                        -- venta 2: teclado
(3, 1, 1, 49.99), (3, 2, 1, 34.99), (3, 5, 1, 39.99), (3, 8, 1, 8.99), (3, 9, 1, 6.99), (3, 3, 1, 19.99), -- venta 3
(4, 5, 1, 39.99), (4, 8, 1, 8.99), (4, 9, 1, 6.99),    -- venta 4
(5, 6, 1, 24.99),                                        -- venta 5: lámpara
(6, 5, 1, 39.99),                                        -- venta 6
(7, 8, 1, 8.99), (7, 9, 1, 6.99), (7, 4, 1, 12.99),    -- venta 7
(8, 1, 1, 49.99), (8, 6, 1, 24.99), (8, 3, 1, 19.99),  -- venta 8
(9, 3, 1, 19.99),                                        -- venta 9 (pendiente)
(10, 8, 1, 8.99), (10, 4, 1, 6.99);                     -- venta 10 (cancelada)
