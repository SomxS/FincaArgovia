-- Tabla: udn
CREATE TABLE udn (
    idUDN INT AUTO_INCREMENT PRIMARY KEY,
    UDN VARCHAR(255) NOT NULL,
    Abreviatura VARCHAR(50),
    Estado TINYINT(1) DEFAULT 1,
    Antiguedad INT,
    udn_patron VARCHAR(255),
    color VARCHAR(50)
);

-- Tabla: usuarios
CREATE TABLE usuarios (
    idUser INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL,
    user_key VARCHAR(255) NOT NULL,
    key2 VARCHAR(255),
    key3 VARCHAR(255),
    creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    usr_estado TINYINT(1) DEFAULT 1,
    usr_empleado VARCHAR(255),
    usr_udn INT,
    user_intentos INT DEFAULT 0,
    user_photo VARCHAR(255),
    activacion TINYINT(1) DEFAULT 0,
    usr_codigo VARCHAR(255),
    password VARCHAR(255),
    FOREIGN KEY (usr_udn) REFERENCES udn(idUDN)
);

-- Tabla: file
CREATE TABLE file (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udn_id INT,
    user_id INT,
    file_name VARCHAR(255) NOT NULL,
    upload_date DATE NOT NULL,
    size_bytes INT,
    path VARCHAR(255) NOT NULL,
    extension VARCHAR(50),
    operation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN),
    FOREIGN KEY (user_id) REFERENCES usuarios(idUser)
);