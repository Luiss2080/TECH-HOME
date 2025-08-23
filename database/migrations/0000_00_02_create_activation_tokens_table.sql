-- ==========================================
-- TABLA: activation_tokens
-- Sistema de activación de cuentas de usuario
-- ==========================================

USE tech_home;

CREATE TABLE activation_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    usado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_usado (usado)
) ENGINE = InnoDB;
