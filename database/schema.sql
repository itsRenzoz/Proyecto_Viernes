-- Esquema de base de datos para VetAgenda


CREATE DATABASE IF NOT EXISTS vetagenda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vetagenda;

-- Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(100) NOT NULL,
	email VARCHAR(150) NOT NULL UNIQUE,
	password_hash VARCHAR(255) NOT NULL,
	rol ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
	creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Citas
CREATE TABLE IF NOT EXISTS citas (
	id INT AUTO_INCREMENT PRIMARY KEY,
	usuario_id INT NOT NULL,
	nombre_mascota VARCHAR(100) NOT NULL,
	fecha_hora DATETIME NOT NULL,
	estado ENUM('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
	creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_citas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
	INDEX idx_citas_usuario (usuario_id),
	UNIQUE KEY uq_cita_unica (fecha_hora)
) ENGINE=InnoDB;

-- Productos
CREATE TABLE IF NOT EXISTS productos (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(150) NOT NULL,
	descripcion TEXT,
	precio DECIMAL(10,2) NOT NULL,
	stock INT NOT NULL DEFAULT 0,
	imagen_url VARCHAR(255),
	activo TINYINT(1) NOT NULL DEFAULT 1,
	creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Info clínica (una sola fila)
CREATE TABLE IF NOT EXISTS info_clinica (
	id TINYINT PRIMARY KEY DEFAULT 1,
	horarios TEXT,
	servicios TEXT,
	telefono VARCHAR(50),
	direccion VARCHAR(255),
	actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO info_clinica (id, horarios, servicios, telefono, direccion) VALUES
	(1, 'Lunes a Viernes, 8:00 AM - 6:00 PM', 'Consultas generales, Vacunación, Esterilización/Castración, Cirugías de tejidos blandos, Odontología veterinaria, Desparasitación, Laboratorio clínico, Radiografías, Hospitalización, Emergencias', '+506 1234-5678', 'San José, Costa Rica')
ON DUPLICATE KEY UPDATE id=id;

-- Notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
	id INT AUTO_INCREMENT PRIMARY KEY,
	usuario_id INT NOT NULL,
	cita_id INT NULL,
	canal ENUM('email','ui') NOT NULL DEFAULT 'ui',
	mensaje TEXT NOT NULL,
	estado ENUM('pendiente','enviada','fallida') NOT NULL DEFAULT 'pendiente',
	programada_para DATETIME NULL,
	enviada_en DATETIME NULL,
	creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_notif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
	CONSTRAINT fk_notif_cita FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Pedidos (tienda)
CREATE TABLE IF NOT EXISTS pedidos (
	id INT AUTO_INCREMENT PRIMARY KEY,
	usuario_id INT NULL,
	total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
	estado ENUM('pendiente','pagado','cancelado') NOT NULL DEFAULT 'pendiente',
	creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_pedido_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedido_items (
	id INT AUTO_INCREMENT PRIMARY KEY,
	pedido_id INT NOT NULL,
	producto_id INT NOT NULL,
	cantidad INT NOT NULL,
	precio_unitario DECIMAL(10,2) NOT NULL,
	CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
	CONSTRAINT fk_item_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Datos de ejemplo de productos (precios aproximados CRC)
INSERT INTO productos (nombre, descripcion, precio, stock, imagen_url, activo, reservado) VALUES
	('Collar para perro', 'Collar resistente y ajustable.', 6900.00, 25, NULL, 1),
	('Alimento Premium 10kg', 'Alimento balanceado de alta calidad.', 28500.00, 15, NULL, 1)
	('Juguete interactivo', 'Reduce el estrés y promueve el juego.', 5500.00, 40, NULL, 1)
	('Camita acolchada', 'Camita lavable tamaño mediano.', 39900.00, 10, NULL, 1) 
	('Shampoo hipoalergénico', 'Para piel sensible, 500ml.', 7200.00, 30, NULL, 1) 
	('Arena para gato 10kg', 'Control de olores prolongado.', 13500.00, 20, NULL, 1)
ON DUPLICATE KEY UPDATE id=id; 