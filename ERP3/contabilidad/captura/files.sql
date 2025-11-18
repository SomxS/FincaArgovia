-- Enhanced database schema for files module with module support and permission levels

-- Tabla: file_modules (to support different modules: Ventas, Compras, Almacén, Tesorería)
CREATE TABLE file_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_name VARCHAR(50) NOT NULL UNIQUE,
    module_description VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default modules
INSERT INTO file_modules (module_name, module_description) VALUES
('Ventas', 'Archivos de ventas y facturación'),
('Compras', 'Archivos de compras y proveedores'),
('Almacén', 'Archivos de inventario y almacén'),
('Tesorería', 'Archivos de tesorería y finanzas');

-- Enhanced file table with module support
CREATE TABLE file (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udn_id INT,
    user_id INT,
    module_id INT,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    upload_date DATE NOT NULL,
    upload_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    size_bytes INT,
    path VARCHAR(500) NOT NULL,
    extension VARCHAR(50),
    mime_type VARCHAR(100),
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_by INT NULL,
    deleted_at DATETIME NULL,
    download_token VARCHAR(255) UNIQUE,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN),
    FOREIGN KEY (user_id) REFERENCES usuarios(idUser),
    FOREIGN KEY (module_id) REFERENCES file_modules(id),
    FOREIGN KEY (deleted_by) REFERENCES usuarios(idUser),
    INDEX idx_upload_date (upload_date),
    INDEX idx_module (module_id),
    INDEX idx_user (user_id),
    INDEX idx_deleted (is_deleted)
);

-- Tabla: file_access_log (for tracking downloads and deletions)
CREATE TABLE file_access_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_id INT,
    user_id INT,
    action VARCHAR(50) NOT NULL, -- 'download', 'delete', 'view'
    action_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (file_id) REFERENCES file(id),
    FOREIGN KEY (user_id) REFERENCES usuarios(idUser),
    INDEX idx_file_action (file_id, action),
    INDEX idx_user_action (user_id, action),
    INDEX idx_action_date (action_datetime)
);

-- Tabla: user_permissions (to define user access levels)
CREATE TABLE user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    module_id INT,
    permission_level ENUM('Captura', 'Gerencia', 'Contabilidad', 'Direccion') DEFAULT 'Captura',
    can_view TINYINT(1) DEFAULT 1,
    can_download TINYINT(1) DEFAULT 1,
    can_delete TINYINT(1) DEFAULT 0,
    can_view_all_units TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(idUser),
    FOREIGN KEY (module_id) REFERENCES file_modules(id),
    UNIQUE KEY unique_user_module (user_id, module_id),
    INDEX idx_permission_level (permission_level)
);

-- Vista: file_view (for easy querying with all related data)
CREATE VIEW file_view AS
SELECT 
    f.id,
    f.file_name,
    f.original_name,
    f.upload_date,
    f.upload_datetime,
    f.size_bytes,
    f.extension,
    f.path,
    f.is_deleted,
    f.download_token,
    u.user AS uploaded_by,
    u.user_perfil AS user_profile,
    u.usr_empleado AS employee_name,
    udn.UDN AS business_unit,
    fm.module_name,
    fm.module_description,
    CASE 
        WHEN f.size_bytes < 1024 THEN CONCAT(f.size_bytes, ' B')
        WHEN f.size_bytes < 1048576 THEN CONCAT(ROUND(f.size_bytes/1024, 2), ' KB')
        WHEN f.size_bytes < 1073741824 THEN CONCAT(ROUND(f.size_bytes/1048576, 2), ' MB')
        ELSE CONCAT(ROUND(f.size_bytes/1073741824, 2), ' GB')
    END AS formatted_size
FROM file f
LEFT JOIN usuarios u ON f.user_id = u.idUser
LEFT JOIN udn ON f.udn_id = udn.idUDN
LEFT JOIN file_modules fm ON f.module_id = fm.id
WHERE f.is_deleted = 0;

-- Procedimiento almacenado: Get files by permission level
DELIMITER //
CREATE PROCEDURE GetFilesByPermission(
    IN p_user_id INT,
    IN p_module_id INT,
    IN p_date_from DATE,
    IN p_date_to DATE,
    IN p_permission_level VARCHAR(20)
)
BEGIN
    SELECT 
        fv.*,
        up.permission_level,
        up.can_view,
        up.can_download,
        up.can_delete
    FROM file_view fv
    LEFT JOIN user_permissions up ON fv.id = (
        SELECT f.id FROM file f 
        WHERE f.id = fv.id 
        AND f.user_id = p_user_id
    ) AND up.user_id = p_user_id AND up.module_id = fv.module_id
    WHERE 
        (p_permission_level = 'Direccion' OR fv.business_unit = (
            SELECT udn.UDN FROM usuarios u 
            LEFT JOIN udn ON u.usr_udn = udn.idUDN 
            WHERE u.idUser = p_user_id
        ))
        AND (p_module_id IS NULL OR fv.module_id = p_module_id)
        AND (p_permission_level IN ('Gerencia', 'Direccion', 'Contabilidad') OR fv.upload_date BETWEEN p_date_from AND p_date_to)
        AND fv.is_deleted = 0
    ORDER BY fv.upload_datetime DESC;
END //
DELIMITER ;

-- Índices adicionales para optimización
CREATE INDEX idx_file_composite ON file(upload_date, module_id, udn_id, is_deleted);
CREATE INDEX idx_file_token ON file(download_token);