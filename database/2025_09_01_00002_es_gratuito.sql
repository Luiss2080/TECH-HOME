ALTER TABLE `cursos`
ADD COLUMN `es_gratuito` TINYINT (1) DEFAULT 1 COMMENT '1 = Gratuito, 0 = De pago';

CREATE TABLE
  `inscripciones` (
    `id` INT (11) NOT NULL AUTO_INCREMENT,
    `estudiante_id` INT (11) NOT NULL,
    `curso_id` INT (11) NOT NULL,
    `fecha_inscripcion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
  `notas` (
    `id` INT (11) NOT NULL AUTO_INCREMENT,
    `estudiante_id` INT (11) NOT NULL,
    `curso_id` INT (11) NOT NULL,
    `nota` DECIMAL(5, 2) NOT NULL,
    `fecha_calificacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;