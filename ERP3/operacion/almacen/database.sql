-- Database Schema for Módulo de Materiales
-- Sistema de Almacén ERP

-- Table: mtto_almacen_area (Areas)
CREATE TABLE IF NOT EXISTS mtto_almacen_area (
    idArea INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Area VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mtto_categoria (Categories)
CREATE TABLE IF NOT EXISTS mtto_categoria (
    idcategoria INT PRIMARY KEY AUTO_INCREMENT,
    nombreCategoria VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mtto_almacen_equipos (Equipment)
CREATE TABLE IF NOT EXISTS mtto_almacen_equipos (
    idEquipo INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Equipo VARCHAR(255) NOT NULL,
    min_stock INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mtto_proveedores (Suppliers)
CREATE TABLE IF NOT EXISTS mtto_proveedores (
    idProveedor INT PRIMARY KEY AUTO_INCREMENT,
    nombreProveedor TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mtto_marca (Brands)
CREATE TABLE IF NOT EXISTS mtto_marca (
    idmarca INT PRIMARY KEY AUTO_INCREMENT,
    Marca_producto VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mtto_almacen (Main Materials Table)
CREATE TABLE IF NOT EXISTS mtto_almacen (
    idAlmacen INT PRIMARY KEY AUTO_INCREMENT,
    UDN_Almacen INT,
    CodigoEquipo VARCHAR(50) UNIQUE NOT NULL,
    Equipo VARCHAR(255) NOT NULL,
    Area INT,
    Estado INT DEFAULT 1,
    id_categoria INT,
    cantidad INT DEFAULT 0,
    Costo DOUBLE DEFAULT 0.00,
    PrecioVenta DOUBLE,
    TiempoDeVida TIMESTAMP NULL,
    FechaIngreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    inventario_min INT,
    Resazon TEXT,
    Ruta VARCHAR(255),
    Archivo TEXT,
    Size DOUBLE,
    Type_File VARCHAR(10),
    Descripcion TEXT,
    Hora TIME,
    Fecha DATE,
    EstadoProducto INT,
    Observaciones TEXT,
    id_zona INT,
    rutaImagen TEXT,
    id_Proveedor INT,
    id_marca INT,
    
    FOREIGN KEY (id_zona) REFERENCES mtto_almacen_area(idArea) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES mtto_categoria(idcategoria) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_Proveedor) REFERENCES mtto_proveedores(idProveedor) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_marca) REFERENCES mtto_marca(idmarca) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_codigo (CodigoEquipo),
    INDEX idx_zona (id_zona),
    INDEX idx_categoria (id_categoria),
    INDEX idx_estado (Estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for testing (optional)
INSERT INTO mtto_almacen_area (Nombre_Area) VALUES 
('PAPELERIA'),
('LIMPIEZA'),
('MANTENIMIENTO'),
('COCINA'),
('RECEPCION');

INSERT INTO mtto_categoria (nombreCategoria) VALUES 
('PIEZAS'),
('HERRAMIENTAS'),
('CONSUMIBLES'),
('EQUIPOS'),
('MOBILIARIO');
