--------------------------------------------COMPONENTES --------------------------------------------------------------
INSERT INTO `componentes`
(`nombre`,`descripcion`,`categoria_id`,`codigo_producto`,`marca`,`modelo`,
 `especificaciones`,`imagen_principal`,`imagenes_adicionales`,
 `precio`,`stock`,`stock_minimo`,`proveedor`,`estado`,
 `stock_reservado`,`alerta_stock_bajo`,`permite_venta_sin_stock`)
VALUES
-- 1
('Arduino Uno R3','Placa microcontrolador ATmega328P compatible.',11,'CMP-1001','Arduino','UNO R3',
 '{"mcu":"ATmega328P","voltaje":"5V","io_pines":14,"pwm":6,"memoria_flash":"32KB"}',
 '/img/componentes/arduino_uno.jpg','["/img/componentes/arduino_uno_1.jpg","/img/componentes/arduino_uno_2.jpg"]',
 89.90,50,5,'TechParts SRL','Disponible',0,1,0),

-- 2
('Raspberry Pi Pico','Microcontrolador RP2040 con USB.',11,'CMP-1002','Raspberry Pi','Pico',
 '{"mcu":"RP2040","velocidad_mhz":133,"ram_kb":264,"usb":"Micro-USB"}',
 '/img/componentes/rp_pico.jpg','["/img/componentes/rp_pico_1.jpg"]',
 49.50,70,10,'ElectroStore','Disponible',0,1,0),

-- 3
('ESP32 DevKit v1','WiFi+BLE SoC dual-core Xtensa.',11,'CMP-1003','Espressif','DevKit v1',
 '{"wifi":"802.11 b/g/n","bluetooth":"BLE 4.2","gpio":30}',
 '/img/componentes/esp32.jpg','[]',
 64.00,80,10,'ElectroStore','Disponible',0,1,0),

-- 4
('Sensor DHT22','Sensor digital de temperatura y humedad.',12,'CMP-1004','Aosong','DHT22',
 '{"rango_temp":"-40–80°C","rango_hum":"0–100%","precision":"±0.5°C"}',
 '/img/componentes/dht22.jpg','["/img/componentes/dht22_1.jpg"]',
 28.90,120,15,'SensTech','Disponible',5,1,0),

-- 5
('Sensor Ultrasonido HC-SR04','Medición de distancia por ultrasonido.',12,'CMP-1005','Elecfreaks','HC-SR04',
 '{"distancia_max_cm":400,"angulo":"15°","tension":"5V"}',
 '/img/componentes/hcsr04.jpg','[]',
 19.90,150,20,'SensTech','Disponible',0,1,0),

-- 6
('Módulo MPU-6050','Acelerómetro+Giroscopio 6 ejes.',12,'CMP-1006','InvenSense','MPU-6050',
 '{"interfaz":"I2C","rango_g":"±2/±4/±8/±16"}',
 '/img/componentes/mpu6050.jpg','["/img/componentes/mpu6050_1.jpg"]',
 34.50,90,10,'SensTech','Disponible',3,1,0),

-- 7
('Servo SG90 9g','Servo micro 180°.',13,'CMP-1007','TowerPro','SG90',
 '{"angulo":"180°","torque_kgcm":1.8,"voltaje":"4.8-6V"}',
 '/img/componentes/sg90.jpg','[]',
 16.00,200,30,'ActuaParts','Disponible',10,1,0),

-- 8
('Motor Paso a Paso NEMA17','Motor bipolar 1.8°.',13,'CMP-1008','Wantai','NEMA17',
 '{"corriente":"1.5A","paso":"1.8°","tension":"12V"}',
 '/img/componentes/nema17.jpg','["/img/componentes/nema17_1.jpg"]',
 129.00,40,5,'ActuaParts','Disponible',0,1,0),

-- 9
('Driver L298N','Driver puente H dual para motores DC.',13,'CMP-1009','ST','L298N',
 '{"corriente_max":"2A","voltaje":"5-35V"}',
 '/img/componentes/l298n.jpg','[]',
 29.00,110,15,'ActuaParts','Disponible',4,1,0),

-- 10
('Kit Resistencias 1/4W','Surtido 1% 600 piezas.',14,'CMP-1010','UniOhm','Surtido 600',
 '{"tolerancia":"±1%","potencia":"0.25W","rangos_ohm":"10Ω–1MΩ"}',
 '/img/componentes/res_kit.jpg','[]',
 24.50,85,10,'ElectroBits','Disponible',0,1,0),

-- 11
('Kit Capacitores Electrolíticos','Surtido 100 piezas.',14,'CMP-1011','Nichicon','Assorted',
 '{"voltajes":"10V–50V","capacitancias":"1µF–1000µF"}',
 '/img/componentes/cap_kit.jpg','["/img/componentes/cap_kit_1.jpg"]',
 22.00,70,10,'ElectroBits','Disponible',0,1,0),

-- 12
('LED 5mm Surtido','Paquete 500 LEDs varios colores.',14,'CMP-1012','LiteOn','5mm Mix',
 '{"colores":["rojo","verde","azul","amarillo","blanco"]}',
 '/img/componentes/led_pack.jpg','[]',
 21.00,150,20,'ElectroBits','Disponible',0,1,0),

-- 13
('Protoboard 830 puntos','Breadboard tamaño estándar.',15,'CMP-1013','Elegoo','MB-102',
 '{"puntos":830,"lineas_alimentacion":2}',
 '/img/componentes/protoboard.jpg','[]',
 18.50,120,20,'ToolTech','Disponible',0,1,0),

-- 14
('Multímetro Digital','Básico para electrónica.',15,'CMP-1014','Mastech','DT-830B',
 '{"funciones":["V","A","Ω","diodo","continuidad"]}',
 '/img/componentes/multimetro.jpg','[]',
 55.00,60,10,'ToolTech','Disponible',0,1,0),

-- 15
('Soldador 60W con estaño','Incluye soporte y estaño 1mm.',15,'CMP-1015','YIHUA','60W Kit',
 '{"potencia":"60W","temp_max":"450°C"}',
 '/img/componentes/soldador.jpg','["/img/componentes/soldador_1.jpg"]',
 69.00,45,8,'ToolTech','Disponible',2,1,0),

-- 16
('Cables Jumper 120pcs','M/M, M/H, H/H surtidos.',14,'CMP-1016','Elegoo','Jump-120',
 '{"cantidad":120,"tipos":["MM","MH","HH"]}',
 '/img/componentes/jumper.jpg','[]',
 14.90,180,25,'ElectroBits','Disponible',0,1,0),

-- 17
('Fuente 5V 2A','Fuente con conector 5.5x2.1mm.',14,'CMP-1017','Mean Well','GST25A05',
 '{"salida":"5V 2A","eficiencia":"Level VI"}',
 '/img/componentes/psu5v.jpg','[]',
 39.00,75,10,'ElectroBits','Disponible',0,1,0),

-- 18
('Módulo Relevador 1 Canal','Relay 10A con optoacoplador.',14,'CMP-1018','Songle','SRD-05VDC-SL-C',
 '{"contacto":"10A 250VAC","control":"5V"}',
 '/img/componentes/relay1c.jpg','[]',
 17.50,130,20,'ElectroBits','Disponible',6,1,0),

-- 19
('Pantalla OLED 0.96\" I2C','Resolución 128x64.',14,'CMP-1019','Waveshare','SSD1306',
 '{"interfaz":"I2C","tamano":"0.96\\""}',
 '/img/componentes/oled096.jpg','["/img/componentes/oled096_1.jpg"]',
 48.00,65,10,'ElectroBits','Disponible',0,1,0),

-- 20
('LCD 16x2 I2C','Módulo con backpack I2C.',14,'CMP-1020','Hitachi','HD44780+I2C',
 '{"caracteres":"16x2","interfaz":"I2C"}',
 '/img/componentes/lcd16x2.jpg','[]',
 29.90,90,12,'ElectroBits','Disponible',0,1,0),

-- 21
('Kit Educativo Arduino Starter','Incluye sensores y módulos.',16,'CMP-1021','Elegoo','Starter Kit',
 '{"incluye":["UNO","sensores","cables","protoboard"]}',
 '/img/componentes/kit_arduino.jpg','[]',
 289.00,35,5,'EduKits','Disponible',0,1,0),

-- 22
('Kit Educativo Robótica Básica','Brazo robótico y guías.',16,'CMP-1022','MakeBlock','mBot Basic',
 '{"material":"Aluminio","edad_min":8}',
 '/img/componentes/kit_robotica.jpg','[]',
 359.00,20,3,'EduKits','Disponible',1,1,0),

-- 23
('ESP8266 NodeMCU','WiFi SoC con Lua/Arduino.',11,'CMP-1023','Espressif','NodeMCU',
 '{"wifi":"802.11 b/g/n","flash_mb":4}',
 '/img/componentes/esp8266.jpg','[]',
 44.00,95,10,'ElectroStore','Disponible',0,1,0),

-- 24
('Sensor Magnetómetro HMC5883L','Brújula digital 3 ejes.',12,'CMP-1024','Honeywell','HMC5883L',
 '{"interfaz":"I2C","rango_gauss":"±8"}',
 '/img/componentes/hmc5883l.jpg','[]',
 33.00,85,10,'SensTech','Disponible',0,1,0),

-- 25
('Driver DRV8825','Driver para motores paso a paso.',13,'CMP-1025','Texas Instruments','DRV8825',
 '{"corriente":"2.2A","microstepping":"1/32"}',
 '/img/componentes/drv8825.jpg','["/img/componentes/drv8825_1.jpg"]',
 42.00,70,10,'ActuaParts','Disponible',0,1,0);


----------------------------------------------LIBROS---------------------------------------------------------------

INSERT INTO `libros`
(`id`,`titulo`,`autor`,`descripcion`,`categoria_id`,`isbn`,`paginas`,`editorial`,
 `año_publicacion`,`imagen_portada`,`archivo_pdf`,`enlace_externo`,
 `tamaño_archivo`,`stock`,`stock_minimo`,`precio`,`es_gratuito`,`estado`,`fecha_creacion`)
VALUES
-- CAT 6: Robótica Educativa (5)
(1, 'Robótica con Arduino para Aula', 'Sofía Ramos', 'Proyectos didácticos con Arduino para primaria y secundaria', 6, '978-6000000001', 240, 'EduTech', '2022', 'robotica_aula.jpg', '/libros/robotica_aula.pdf', NULL, 0, 20, 5, 120.00, 0, 1, NOW()),
(2, 'Iniciación a la Robótica', 'Héctor Aguilar', 'Conceptos básicos de robótica y sensores', 6, '978-6000000002', 200, 'Aprende+','2021', 'iniciacion_robotica.jpg', '/libros/iniciacion_robotica.pdf', NULL, 0, 18, 5, 95.00, 0, 1, NOW()),
(3, 'Talleres STEAM con Robots', 'Daniela Peña', 'Secuencias didácticas STEAM con kits educativos', 6, '978-6000000003', 260, 'STEAM House','2023','steam_robots.jpg','/libros/steam_robots.pdf',NULL,0,15,5,135.00,0,1,NOW()),
(4, 'Robótica Móvil para Jóvenes', 'Luis Cabrera', 'Line follower, evasión de obstáculos y bluetooth', 6, '978-6000000004', 280, 'RoboPress','2020','robotica_movil.jpg','/libros/robotica_movil.pdf',NULL,0,22,5,145.00,0,1,NOW()),
(5, 'Didáctica de la Robótica', 'Paula Calderón', 'Metodologías activas y evaluación por competencias', 6, '978-6000000005', 220, 'EduLab','2022','didactica_robotica.jpg','/libros/didactica_robotica.pdf',NULL,0,16,5,110.00,0,1,NOW()),

-- CAT 7: Programación Avanzada (5)
(6, 'Patrones de Diseño en la Práctica', 'Carolina Ibáñez', 'Aplicación de GoF y patrones modernos', 7, '978-7000000001', 320, 'WebDev Press','2023','patrones_practica.jpg','/libros/patrones_practica.pdf',NULL,0,20,5,165.00,0,1,NOW()),
(7, 'Programación Concurrente', 'Julián Vera', 'Hilos, sincronización y concurrencia avanzada', 7, '978-7000000002', 340, 'Code Press','2021','prog_concurrente.jpg','/libros/prog_concurrente.pdf',NULL,0,14,5,155.00,0,1,NOW()),
(8, 'Diseño Orientado a Objetos', 'Natalia Ríos', 'Principios SOLID y arquitectura limpia', 7, '978-7000000003', 300, 'Clean Arch','2022','doo.jpg','/libros/doo.pdf',NULL,0,25,5,150.00,0,1,NOW()),
(9, 'Estructuras y Algoritmos Avanzados', 'Sergio Maldonado', 'Grafos, DP, flujos y heurísticas', 7, '978-7000000004', 410, 'Algoritmia Ed.','2020','eda_avanzados.jpg','/libros/eda_avanzados.pdf',NULL,0,12,5,175.00,0,1,NOW()),
(10, 'Metaprogramación en Python', 'Lucía Navarro', 'Decoradores, introspección y generación de código', 7, '978-7000000005', 260, 'PyBooks','2023','metaprogramacion_py.jpg','/libros/metaprogramacion_py.pdf',NULL,0,18,5,140.00,0,1,NOW()),

-- CAT 8: Electrónica Práctica (5)
(11, 'Electrónica desde Cero', 'Carlos Sánchez', 'Fundamentos, mediciones y prácticas guiadas', 8, '978-8000000001', 350, 'Electro Books','2020','electronica_cero.jpg','/libros/electronica_cero.pdf',NULL,0,28,5,130.00,0,1,NOW()),
(12, 'Proyectos con Sensores', 'María Valdez', 'Aplicaciones con DHT22, MPU6050, HC-SR04', 8, '978-8000000002', 280, 'Maker Ed.','2021','proyectos_sensores.jpg','/libros/proyectos_sensores.pdf',NULL,0,20,5,125.00,0,1,NOW()),
(13, 'Fuentes y Regulación', 'Óscar Rivas', 'Diseño de fuentes lineales y conmutadas', 8, '978-8000000003', 300, 'PowerLab','2022','fuentes_regulacion.jpg','/libros/fuentes_regulacion.pdf',NULL,0,16,5,155.00,0,1,NOW()),
(14, 'Señales y Filtros', 'Patricia Vela', 'Análisis de señales y filtros analógicos/digitales', 8, '978-8000000004', 320, 'Signal Press','2021','senales_filtros.jpg','/libros/senales_filtros.pdf',NULL,0,14,5,160.00,0,1,NOW()),
(15, 'Electrónica de Potencia', 'Elena Cordero', 'Convertidores, drivers y control', 8, '978-8000000005', 380, 'PowerLab','2023','electronica_potencia.jpg','/libros/electronica_potencia.pdf',NULL,0,10,5,185.00,0,1,NOW()),

-- CAT 9: Inteligencia Artificial (5)
(16, 'Fundamentos de IA', 'Stuart Russell', 'Búsqueda, probabilidad, aprendizaje y agentes', 9, '978-9000000001', 350, 'AI Publications','2021','fund_ia.jpg','/libros/fund_ia.pdf',NULL,0,22,5,190.00,0,1,NOW()),
(17, 'Aprendizaje Automático Práctico', 'Tom Mitchell', 'Modelos supervisados y no supervisados', 9, '978-9000000002', 320, 'AI Press','2020','ml_practico.jpg','/libros/ml_practico.pdf',NULL,0,18,5,175.00,0,1,NOW()),
(18, 'Redes Neuronales Profundas', 'Ian Goodfellow', 'CNN, RNN, optimización y regularización', 9, '978-9000000003', 420, 'Deep Books','2022','deep_learning.jpg','/libros/deep_learning.pdf',NULL,0,15,5,210.00,0,1,NOW()),
(19, 'Procesamiento de Lenguaje Natural', 'Christopher Manning', 'Embeddings, transformers y aplicaciones', 9, '978-9000000004', 360, 'NLP House','2023','pln.jpg','/libros/pln.pdf',NULL,0,17,5,200.00,0,1,NOW()),
(20, 'Visión por Computador', 'Richard Szeliski', 'Formación de imagen, características y reconocimiento', 9, '978-9000000005', 400, 'Vision Lab','2021','vision.jpg','/libros/vision.pdf',NULL,0,13,5,195.00,0,1,NOW()),

-- CAT 10: Matemáticas y Física (5)
(21, 'Cálculo para Ingeniería', 'James Stewart', 'Límites, derivadas, integrales y aplicaciones', 10, '978-1000000001', 680, 'Math Ed','2022','calculo.jpg','/libros/calculo.pdf',NULL,0,25,8,180.00,0,1,NOW()),
(22, 'Álgebra Lineal Aplicada', 'Gilbert Strang', 'Vectores, matrices, descomposiciones y aplicaciones', 10, '978-1000000002', 520, 'Linear Press','2021','algebra_lineal.jpg','/libros/algebra_lineal.pdf',NULL,0,20,6,170.00,0,1,NOW()),
(23, 'Probabilidad y Estadística', 'Morris H. DeGroot', 'Modelos probabilísticos e inferencia', 10, '978-1000000003', 600, 'Stats House','2020','prob_est.jpg','/libros/prob_est.pdf',NULL,0,18,6,175.00,0,1,NOW()),
(24, 'Física Universitaria', 'Hugh D. Young', 'Mecánica, ondas, termodinámica y electromagnetismo', 10, '978-1000000004', 760, 'PhysBooks','2019','fisica_uni.jpg','/libros/fisica_uni.pdf',NULL,0,14,6,190.00,0,1,NOW()),
(25, 'Métodos Numéricos', 'Richard L. Burden', 'Ecuaciones, interpolación, integración y EDOs', 10, '978-1000000005', 520, 'Num Press','2023','metodos_numericos.jpg','/libros/metodos_numericos.pdf',NULL,0,16,6,165.00,0,1,NOW());



----------------------------------------------CURSOS---------------------------------------------------------------

INSERT INTO `cursos`
(`id`,`titulo`,`descripcion`,`video_url`,`docente_id`,`categoria_id`,`imagen_portada`,`nivel`,`estado`,`fecha_creacion`,`fecha_actualizacion`)
VALUES
-- Categoría 1: Robótica (ids 1..5)
(1,'Robótica desde Cero','Introducción a sensores, actuadores y control con Arduino','https://youtu.be/rob1',14,1,'/img/cursos/rob_basico.jpg','Principiante','Publicado',NOW(),NOW()),
(2,'Robots Móviles','Line follower, evasión de obstáculos y BLE','https://youtu.be/rob2',25,1,'/img/cursos/rob_moviles.jpg','Intermedio','Publicado',NOW(),NOW()),
(3,'Brazos Robóticos','Cinemática básica y control por PWM','https://youtu.be/rob3',14,1,'/img/cursos/rob_brazos.jpg','Intermedio','Publicado',NOW(),NOW()),
(4,'Robótica Educativa en el Aula','Planificaciones STEAM y proyectos guiados','https://youtu.be/rob4',25,1,'/img/cursos/rob_aula.jpg','Principiante','Publicado',NOW(),NOW()),
(5,'Visión para Robótica','Introducción a visión por computador en robots','https://youtu.be/rob5',14,1,'/img/cursos/rob_vision.jpg','Avanzado','Publicado',NOW(),NOW()),

-- Categoría 2: Programación (ids 6..10)
(6,'Programación en C desde Cero','Sintaxis, funciones, punteros y estructuras','https://youtu.be/prog1',25,2,'/img/cursos/c_desde_cero.jpg','Principiante','Publicado',NOW(),NOW()),
(7,'POO en Java','Clases, herencia, interfaces y colecciones','https://youtu.be/prog2',14,2,'/img/cursos/java_poo.jpg','Intermedio','Publicado',NOW(),NOW()),
(8,'Python para Ciencia de Datos','Numpy, Pandas y visualización','https://youtu.be/prog3',25,2,'/img/cursos/python_cd.jpg','Intermedio','Publicado',NOW(),NOW()),
(9,'Patrones de Diseño','GoF, SOLID y arquitectura limpia','https://youtu.be/prog4',14,2,'/img/cursos/patrones.jpg','Avanzado','Publicado',NOW(),NOW()),
(10,'APIs con Node.js','REST, autenticación y testing','https://youtu.be/prog5',25,2,'/img/cursos/node_apis.jpg','Intermedio','Publicado',NOW(),NOW()),

-- Categoría 3: Electrónica (ids 11..15)
(11,'Electrónica Básica','Leyes de Ohm/Kirchhoff, mediciones y seguridad','https://youtu.be/elec1',14,3,'/img/cursos/elec_basica.jpg','Principiante','Publicado',NOW(),NOW()),
(12,'Diseño de PCBs','Flujo EDA, ruteo, fabricación y montaje','https://youtu.be/elec2',25,3,'/img/cursos/pcbs.jpg','Intermedio','Publicado',NOW(),NOW()),
(13,'Sensores en Profundidad','DHT22, MPU6050, ultrasonido, magnetómetro','https://youtu.be/elec3',14,3,'/img/cursos/sensores.jpg','Intermedio','Publicado',NOW(),NOW()),
(14,'Fuentes Conmutadas','Topologías, control y protección','https://youtu.be/elec4',25,3,'/img/cursos/fuentes.jpg','Avanzado','Publicado',NOW(),NOW()),
(15,'Instrumentación y Medición','Osciloscopio, multímetro y generador de funciones','https://youtu.be/elec5',14,3,'/img/cursos/instrumentacion.jpg','Principiante','Publicado',NOW(),NOW()),

-- Categoría 4: Inteligencia Artificial (ids 16..20)
(16,'Fundamentos de IA','Búsqueda, agentes racionales y probabilidad','https://youtu.be/ia1',25,4,'/img/cursos/ia_fund.jpg','Principiante','Publicado',NOW(),NOW()),
(17,'ML con Scikit-Learn','Regresión, clasificación y validación','https://youtu.be/ia2',14,4,'/img/cursos/ml_sklearn.jpg','Intermedio','Publicado',NOW(),NOW()),
(18,'Redes Neuronales','Perceptrón, backprop y regularización','https://youtu.be/ia3',25,4,'/img/cursos/redes_nn.jpg','Intermedio','Publicado',NOW(),NOW()),
(19,'Visión por Computador','Características, detección y reconocimiento','https://youtu.be/ia4',14,4,'/img/cursos/vision.jpg','Avanzado','Publicado',NOW(),NOW()),
(20,'NLP con Transformers','Embeddings, atención y fine-tuning','https://youtu.be/ia5',25,4,'/img/cursos/nlp_transformers.jpg','Avanzado','Publicado',NOW(),NOW()),

-- Categoría 5: Ciencias de Datos (ids 21..25)
(21,'Estadística para Data Science','Distribuciones, estimación e inferencia','https://youtu.be/ds1',14,5,'/img/cursos/estadistica.jpg','Principiante','Publicado',NOW(),NOW()),
(22,'Limpieza y Preparación de Datos','Pipelines y manejo de valores faltantes','https://youtu.be/ds2',25,5,'/img/cursos/data_cleaning.jpg','Intermedio','Publicado',NOW(),NOW()),
(23,'Visualización Efectiva','Principios de diseño y librerías','https://youtu.be/ds3',14,5,'/img/cursos/visualizacion.jpg','Intermedio','Publicado',NOW(),NOW()),
(24,'Aprendizaje No Supervisado','Clustering y reducción de dimensión','https://youtu.be/ds4',25,5,'/img/cursos/unsupervised.jpg','Avanzado','Publicado',NOW(),NOW()),
(25,'MLOps Básico','Versionado, despliegue y monitoreo de modelos','https://youtu.be/ds5',14,5,'/img/cursos/mlops.jpg','Avanzado','Publicado',NOW(),NOW());

----------------------------------------------MATERIALES---------------------------------------------------------------

INSERT INTO `materiales`
(`id`,`titulo`,`descripcion`,`tipo`,`archivo`,`enlace_externo`,`tamaño_archivo`,`duracion`,
 `categoria_id`,`docente_id`,`imagen_preview`,`publico`,`descargas`,`estado`,`fecha_creacion`,`fecha_actualizacion`)
VALUES
-- Categoría 1: Robótica (1..5)
(1,'Manual de Sensores para Robótica','Guía PDF de sensores (DHT22, HC-SR04, MPU6050) y conexiones','pdf',
 '/materiales/robotica/manual_sensores.pdf',NULL,2048,0,1,14,'/materiales/img/manual_sensores.jpg',1,3,1,NOW(),NOW()),
(2,'Video: Calibración de Servos SG90','Procedimiento paso a paso para calibrar servos SG90','video',
 '/materiales/robotica/video_servo_calibracion.mp4','https://youtu.be/rob_servo',512000,780,1,25,'/materiales/img/servo_calibracion.jpg',1,5,1,NOW(),NOW()),
(3,'Guía: Brazo Robótico 3 DOF','Montaje, cinemática básica y control PWM','guia',
 '/materiales/robotica/guia_brazo_3dof.pdf',NULL,3072,0,1,14,'/materiales/img/brazo_3dof.jpg',1,2,1,NOW(),NOW()),
(4,'Código: Seguidor de Línea','Sketch Arduino para seguidor de línea con 5 sensores','codigo',
 '/materiales/robotica/line_follower.ino','https://git.example/line_follower',64,0,1,25,'/materiales/img/line_follower.jpg',1,8,1,NOW(),NOW()),
(5,'Dataset: Lecturas Ultrasonido','CSV con distancias medidas en diferentes escenarios','dataset',
 '/materiales/robotica/dataset_ultrasonido.csv',NULL,2560,0,1,14,'/materiales/img/dataset_ultra.jpg',0,1,1,NOW(),NOW()),

-- Categoría 2: Programación (6..10)
(6,'Apuntes de POO en Java','Colecciones, genéricos y patrones básicos','pdf',
 '/materiales/programacion/poo_java.pdf',NULL,1536,0,2,25,'/materiales/img/poo_java.jpg',1,4,1,NOW(),NOW()),
(7,'Video: Patrones de Diseño','Factory, Strategy y Observer con ejemplos','video',
 '/materiales/programacion/patrones_diseno.mp4','https://youtu.be/patrones_java',430000,900,2,14,'/materiales/img/patrones.jpg',1,6,1,NOW(),NOW()),
(8,'Guía: APIs REST con Node.js','Buenas prácticas, rutas, middlewares y testing','guia',
 '/materiales/programacion/guia_api_node.pdf',NULL,2048,0,2,25,'/materiales/img/api_node.jpg',1,2,1,NOW(),NOW()),
(9,'Código: Búsqueda Binaria en C','Implementación y pruebas unitarias','codigo',
 '/materiales/programacion/busqueda_binaria.c','https://git.example/c-busqueda',32,0,2,14,'/materiales/img/codigo_c.jpg',1,7,1,NOW(),NOW()),
(10,'Dataset: Logs de API de Prueba','Logs anonimizados para ejercicios de parsing','dataset',
 '/materiales/programacion/logs_api.csv',NULL,8192,0,2,25,'/materiales/img/logs_api.jpg',0,0,1,NOW(),NOW()),

-- Categoría 3: Electrónica (11..15)
(11,'Manual: Leyes de Ohm y Kirchhoff','Resumen teórico con ejemplos de laboratorio','pdf',
 '/materiales/electronica/ohm_kirchhoff.pdf',NULL,1792,0,3,14,'/materiales/img/ohm_kirchhoff.jpg',1,12,1,NOW(),NOW()),
(12,'Video: Diseño de PCB en KiCad','Flujo, ruteo y exportación de gerbers','video',
 '/materiales/electronica/kicad_pcb.mp4','https://youtu.be/kicad_pcb',480000,1200,3,25,'/materiales/img/kicad.jpg',1,9,1,NOW(),NOW()),
(13,'Guía: Medición con Multímetro','Continuidad, resistencia, voltaje y corriente','guia',
 '/materiales/electronica/guia_multimetro.pdf',NULL,1024,0,3,14,'/materiales/img/multimetro.jpg',1,3,1,NOW(),NOW()),
(14,'Código: Lectura de MPU6050 (I2C)','Ejemplo Arduino para acelerómetro/giroscopio','codigo',
 '/materiales/electronica/mpu6050_i2c.ino','https://git.example/mpu6050',48,0,3,25,'/materiales/img/mpu6050.jpg',1,6,1,NOW(),NOW()),
(15,'Dataset: Señales de Sensores','CSV con señales capturadas a 1 kHz','dataset',
 '/materiales/electronica/senales_sensores.csv',NULL,12288,0,3,14,'/materiales/img/senales.jpg',0,2,1,NOW(),NOW()),

-- Categoría 4: Inteligencia Artificial (16..20)
(16,'Notas: Fundamentos de IA','Agentes racionales, búsqueda y probabilidad','pdf',
 '/materiales/ia/fundamentos_ia.pdf',NULL,2048,0,4,25,'/materiales/img/fund_ia.jpg',1,5,1,NOW(),NOW()),
(17,'Video: Clasificación con sklearn','Pipeline, métricas y validación cruzada','video',
 '/materiales/ia/sklearn_clasificacion.mp4','https://youtu.be/sklearn_clf',520000,1100,4,14,'/materiales/img/sklearn.jpg',1,8,1,NOW(),NOW()),
(18,'Guía: Red Neuronal desde Cero','Implementación forward/backprop en numpy','guia',
 '/materiales/ia/nn_desde_cero.pdf',NULL,3072,0,4,25,'/materiales/img/nn.jpg',1,4,1,NOW(),NOW()),
(19,'Código: CNN básica (PyTorch)','Modelo simple para MNIST/CIFAR','codigo',
 '/materiales/ia/cnn_pytorch.py','https://git.example/cnn',64,0,4,14,'/materiales/img/cnn.jpg',1,7,1,NOW(),NOW()),
(20,'Dataset: Imágenes de Demostración','Subset de 2k imágenes para práctica','dataset',
 '/materiales/ia/dataset_imagenes.zip',NULL,204800,0,4,25,'/materiales/img/dataset_img.jpg',0,1,1,NOW(),NOW()),

-- Categoría 5: Ciencias de Datos (21..25)
(21,'Apuntes de Estadística Descriptiva','Medidas, distribuciones y visualización','pdf',
 '/materiales/ds/estadistica_descriptiva.pdf',NULL,2304,0,5,14,'/materiales/img/estadistica.jpg',1,6,1,NOW(),NOW()),
(22,'Video: Limpieza de Datos con Pandas','Tratamiento de nulos, tipos y outliers','video',
 '/materiales/ds/pandas_limpieza.mp4','https://youtu.be/pandas_clean',610000,1300,5,25,'/materiales/img/pandas.jpg',1,10,1,NOW(),NOW()),
(23,'Guía: Visualización Efectiva','Principios de diseño y librerías Python','guia',
 '/materiales/ds/visualizacion_efectiva.pdf',NULL,2048,0,5,14,'/materiales/img/viz.jpg',1,3,1,NOW(),NOW()),
(24,'Código: Pipeline de ML (sklearn)','Preprocesamiento, grid search y evaluación','codigo',
 '/materiales/ds/pipeline_ml.py','https://git.example/pipeline',72,0,5,25,'/materiales/img/pipeline.jpg',1,5,1,NOW(),NOW()),
(25,'Dataset: Ventas Limpias (Demo)','Dataset limpio para prácticas de análisis','dataset',
 '/materiales/ds/ventas_limpias.csv',NULL,10240,0,5,14,'/materiales/img/ventas.jpg',0,0,1,NOW(),NOW());


----------------------------------------------progreso_estudiantes---------------------------------------------------------------
INSERT INTO `progreso_estudiantes`
(`id`,`estudiante_id`,`curso_id`,`progreso_porcentaje`,`tiempo_estudiado`,`ultima_actividad`,`completado`,`fecha_inscripcion`)
VALUES
-- Cursos 1..5  (estudiante 18/26 alternados)
(1,  18,  1,  12.50,  60,  NOW(), 0, NOW()),
(2,  26,  2,  28.00, 120,  NOW(), 0, NOW()),
(3,  18,  3,  45.30, 240,  NOW(), 0, NOW()),
(4,  26,  4,  60.00, 360,  NOW(), 0, NOW()),
(5,  18,  5,  80.00, 500,  NOW(), 0, NOW()),

-- Cursos 6..10
(6,  26,  6, 100.00, 720,  NOW(), 1, NOW()),
(7,  18,  7,   5.00,  30,  NOW(), 0, NOW()),
(8,  26,  8,  33.33, 150,  NOW(), 0, NOW()),
(9,  18,  9,  66.67, 400,  NOW(), 0, NOW()),
(10, 26, 10,  90.00, 540,  NOW(), 0, NOW()),

-- Cursos 11..15
(11, 18, 11,  20.00, 100,  NOW(), 0, NOW()),
(12, 26, 12,  55.00, 330,  NOW(), 0, NOW()),
(13, 18, 13,  70.00, 420,  NOW(), 0, NOW()),
(14, 26, 14,  85.00, 510,  NOW(), 0, NOW()),
(15, 18, 15, 100.00, 800,  NOW(), 1, NOW()),

-- Cursos 16..20
(16, 26, 16,  10.00,  60,  NOW(), 0, NOW()),
(17, 18, 17,  25.00, 150,  NOW(), 0, NOW()),
(18, 26, 18,  50.00, 300,  NOW(), 0, NOW()),
(19, 18, 19,  75.00, 450,  NOW(), 0, NOW()),
(20, 26, 20,  95.00, 600,  NOW(), 0, NOW()),

-- Cursos 21..25
(21, 18, 21,  30.00, 180,  NOW(), 0, NOW()),
(22, 26, 22,  65.00, 390,  NOW(), 0, NOW()),
(23, 18, 23,  88.00, 530,  NOW(), 0, NOW()),
(24, 26, 24, 100.00, 900,  NOW(), 1, NOW()),
(25, 18, 25,  42.00, 250,  NOW(), 0, NOW());


----------------------------------------------acceso_materiales---------------------------------------------------------------
INSERT INTO `acceso_materiales`
(`id`,`usuario_id`,`material_id`,`tipo_acceso`,`ip_address`,`user_agent`,`fecha_acceso`)
VALUES
-- Robótica (materiales 1..5)
(1,  18,  1, 'visualizado','10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-22 09:10:00'),
(2,  18,  1, 'descargado', '10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-22 09:12:30'),
(3,  26,  2, 'visualizado','10.0.0.26','Mozilla/5.0 (X11; Linux x86_64) Chrome/123','2025-08-22 10:05:00'),
(4,  26,  2, 'descargado', '10.0.0.26','Mozilla/5.0 (X11; Linux x86_64) Chrome/123','2025-08-22 10:06:40'),
(5,  18,  3, 'visualizado','10.0.0.18','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15','2025-08-22 11:20:00'),

-- Programación (materiales 6..10)
(6,  25,  7, 'visualizado','172.16.0.25','Mozilla/5.0 (Windows NT 11.0; Win64; x64) Edge/123','2025-08-23 08:30:00'),
(7,  25,  7, 'descargado', '172.16.0.25','Mozilla/5.0 (Windows NT 11.0; Win64; x64) Edge/123','2025-08-23 08:35:10'),
(8,  18,  8, 'visualizado','10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/118','2025-08-23 09:05:00'),
(9,  26,  9, 'visualizado','10.0.0.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/118','2025-08-23 09:40:00'),
(10, 26, 10, 'descargado', '10.0.0.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/118','2025-08-23 09:42:15'),

-- Electrónica (materiales 11..15)
(11, 14, 11, 'visualizado','192.168.1.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) Safari/605.1.15','2025-08-23 14:10:00'),
(12, 14, 11, 'descargado', '192.168.1.14','Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) Safari/605.1.15','2025-08-23 14:12:45'),
(13, 18, 12, 'visualizado','10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 08:05:00'),
(14, 26, 13, 'visualizado','10.0.0.26','Mozilla/5.0 (X11; Linux x86_64) Chrome/123','2025-08-24 08:30:00'),
(15, 18, 14, 'descargado', '10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 09:00:00'),

-- Inteligencia Artificial (materiales 16..20)
(16, 20, 16, 'visualizado','192.168.1.20','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 16:45:00'),
(17, 18, 17, 'visualizado','10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 17:05:00'),
(18, 26, 18, 'visualizado','10.0.0.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 17:20:00'),
(19, 18, 19, 'descargado', '10.0.0.18','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 17:35:00'),
(20, 26, 20, 'descargado', '10.0.0.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-24 17:50:00'),

-- Ciencias de Datos (materiales 21..25)
(21,  1, 21, 'visualizado','192.168.1.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124','2025-08-25 09:00:00'),
(22, 15, 22, 'visualizado','192.168.1.15','Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) Safari/605.1.15','2025-08-25 10:10:00'),
(23, 25, 23, 'descargado', '172.16.0.25','Mozilla/5.0 (Windows NT 11.0; Win64; x64) Edge/123','2025-08-25 11:20:00'),
(24, 27, 24, 'visualizado','192.168.1.27','Mozilla/5.0 (Android 13; Mobile) Chrome/120','2025-08-25 12:30:00'),
(25, 26, 25, 'visualizado','10.0.0.26','Mozilla/5.0 (X11; Linux x86_64) Chrome/123','2025-08-25 13:45:00');


----------------------------------------------descargas_libros---------------------------------------------------------------
INSERT INTO `descargas_libros`
(`id`,`usuario_id`,`libro_id`,`ip_address`,`user_agent`,`fecha_descarga`)
VALUES
(1 , 18,  1, '10.0.0.18', NULL, NOW()),
(2 , 26,  2, '10.0.0.26', NULL, NOW()),
(3 , 18,  3, '10.0.0.18', NULL, NOW()),
(4 , 26,  4, '10.0.0.26', NULL, NOW()),
(5 , 18,  5, '10.0.0.18', NULL, NOW()),
(6 , 26,  6, '10.0.0.26', NULL, NOW()),
(7 , 18,  7, '10.0.0.18', NULL, NOW()),
(8 , 26,  8, '10.0.0.26', NULL, NOW()),
(9 , 18,  9, '10.0.0.18', NULL, NOW()),
(10, 26, 10, '10.0.0.26', NULL, NOW()),
(11, 20, 11, '192.168.1.20', NULL, NOW()),  -- invitado
(12, 27, 12, '192.168.1.27', NULL, NOW()),  -- invitado
(13, 14, 13, '192.168.1.14', NULL, NOW()),  -- docente
(14, 25, 14, '172.16.0.25', NULL, NOW()),   -- docente
(15,  1, 15, '192.168.1.1',  NULL, NOW()),  -- admin
(16, 15, 16, '192.168.1.15', NULL, NOW()),  -- admin
(17, 18, 17, '10.0.0.18', NULL, NOW()),
(18, 26, 18, '10.0.0.26', NULL, NOW()),
(19, 18, 19, '10.0.0.18', NULL, NOW()),
(20, 26, 20, '10.0.0.26', NULL, NOW()),
(21, 20, 21, '192.168.1.20', NULL, NOW()),
(22, 27, 22, '192.168.1.27', NULL, NOW()),
(23, 18, 23, '10.0.0.18', NULL, NOW()),
(24, 26, 24, '10.0.0.26', NULL, NOW()),
(25, 18, 25, '10.0.0.18', NULL, NOW());


------------------------------------------------ventas---------------------------------------------------------------
INSERT INTO `ventas`
(`id`,`numero_venta`,`cliente_id`,`vendedor_id`,`subtotal`,`descuento`,`impuestos`,`total`,`tipo_pago`,`estado`,`notas`,`fecha_venta`,`fecha_actualizacion`)
VALUES
(1,  'VTA-2025-001', 18, 14, 180.00, 0.00, 23.40, 203.40, 'Efectivo',      'Completada', NULL, NOW(), NOW()),
(2,  'VTA-2025-002', 26, 25,  85.00, 8.50,  9.95,  86.45, 'Transferencia', 'Pendiente',  NULL, NOW(), NOW()),
(3,  'VTA-2025-003', 14, 14, 350.00,35.00, 40.95, 355.95, 'Tarjeta',       'Completada', NULL, NOW(), NOW()),
(4,  'VTA-2025-004', 25, 25, 270.00, 0.00, 35.10, 305.10, 'QR',            'Completada', NULL, NOW(), NOW()),
(5,  'VTA-2025-005', 20, 14,  99.90, 0.00, 12.99, 112.89, 'Efectivo',      'Cancelada',  NULL, NOW(), NOW()),
(6,  'VTA-2025-006', 27, 25,  49.50, 4.50,  5.85,  50.85, 'Transferencia', 'Reembolsada',NULL, NOW(), NOW()),
(7,  'VTA-2025-007', 18, 14, 420.00,21.00, 51.87, 450.87, 'Tarjeta',       'Completada', NULL, NOW(), NOW()),
(8,  'VTA-2025-008', 26, 25, 215.75,15.75, 26.02, 226.02, 'QR',            'Completada', NULL, NOW(), NOW()),
(9,  'VTA-2025-009', 14, 14, 640.00,40.00, 78.00, 678.00, 'Efectivo',      'Pendiente',  NULL, NOW(), NOW()),
(10, 'VTA-2025-010', 25, 25,  75.00, 0.00,  9.75,  84.75, 'Transferencia', 'Completada', NULL, NOW(), NOW()),
(11, 'VTA-2025-011', 20, 14, 130.00,10.00, 15.60, 135.60, 'Tarjeta',       'Completada', NULL, NOW(), NOW()),
(12, 'VTA-2025-012', 27, 25, 199.99, 0.00, 26.00, 225.99, 'QR',            'Cancelada',  NULL, NOW(), NOW()),
(13, 'VTA-2025-013', 18, 14, 510.00,25.00, 63.05, 548.05, 'Efectivo',      'Reembolsada',NULL, NOW(), NOW()),
(14, 'VTA-2025-014', 26, 25, 320.00,20.00, 39.00, 339.00, 'Transferencia', 'Completada', NULL, NOW(), NOW()),
(15, 'VTA-2025-015', 14, 14, 289.50, 0.00, 37.64, 327.14, 'Tarjeta',       'Completada', NULL, NOW(), NOW()),
(16, 'VTA-2025-016', 25, 25, 950.00,50.00,117.00,1017.00, 'QR',            'Pendiente',  NULL, NOW(), NOW()),
(17, 'VTA-2025-017', 20, 14,  35.00, 0.00,  4.55,  39.55, 'Efectivo',      'Completada', NULL, NOW(), NOW()),
(18, 'VTA-2025-018', 27, 25,  60.00, 0.00,  7.80,  67.80, 'Transferencia', 'Completada', NULL, NOW(), NOW()),
(19, 'VTA-2025-019', 18, 14, 780.00,78.00, 91.26, 793.26, 'Tarjeta',       'Cancelada',  NULL, NOW(), NOW()),
(20, 'VTA-2025-020', 26, 25, 145.25,10.25, 17.58, 152.58, 'QR',            'Completada', NULL, NOW(), NOW()),
(21, 'VTA-2025-021', 14, 14,  59.90, 5.90,  7.02,  61.02, 'Efectivo',      'Completada', NULL, NOW(), NOW()),
(22, 'VTA-2025-022', 25, 25, 499.00,49.00, 58.50, 508.50, 'Transferencia', 'Completada', NULL, NOW(), NOW()),
(23, 'VTA-2025-023', 20, 14, 125.75, 0.00, 16.35, 142.10, 'Tarjeta',       'Pendiente',  NULL, NOW(), NOW()),
(24, 'VTA-2025-024', 27, 25,  88.80, 8.80, 10.40,  90.40, 'QR',            'Completada', NULL, NOW(), NOW()),
(25, 'VTA-2025-025', 18, 14, 230.40,20.40, 27.30, 237.30, 'Efectivo',      'Completada', NULL, NOW(), NOW()),
(26,'VTA-2025-026', 18,14,  45.00,0.00,  5.85,  50.85,'Efectivo','Completada',NULL,NOW(),NOW()),
(27,'VTA-2025-027', 26,25,   8.00,0.00,  1.04,   9.04,'Transferencia','Completada',NULL,NOW(),NOW()),
(28,'VTA-2025-028', 14,14,  12.00,0.00,  1.56,  13.56,'Tarjeta','Pendiente',NULL,NOW(),NOW()),
(29,'VTA-2025-029', 25,25,  15.00,0.00,  1.95,  16.95,'QR','Completada',NULL,NOW(),NOW()),
(30,'VTA-2025-030', 20,14,  10.00,0.00,  1.30,  11.30,'Efectivo','Completada',NULL,NOW(),NOW()),
(31,'VTA-2025-031', 27,25,   5.00,0.00,  0.65,   5.65,'Transferencia','Completada',NULL,NOW(),NOW()),
(32,'VTA-2025-032', 18,14,   9.00,0.00,  1.17,  10.17,'Tarjeta','Completada',NULL,NOW(),NOW()),
(33,'VTA-2025-033', 26,25,  80.00,0.00, 10.40,  90.40,'QR','Pendiente',NULL,NOW(),NOW()),
(34,'VTA-2025-034', 14,14,   6.00,0.00,  0.78,   6.78,'Efectivo','Completada',NULL,NOW(),NOW()),
(35,'VTA-2025-035', 25,25,  18.00,0.00,  2.34,  20.34,'Transferencia','Completada',NULL,NOW(),NOW()),
(36,'VTA-2025-036', 20,14,  95.00,0.00, 12.35, 107.35,'Tarjeta','Completada',NULL,NOW(),NOW()),
(37,'VTA-2025-037', 27,25,  16.00,0.00,  2.08,  18.08,'QR','Completada',NULL,NOW(),NOW()),
(38,'VTA-2025-038', 18,14,   7.50,0.00,  0.98,   8.48,'Efectivo','Completada',NULL,NOW(),NOW()),
(39,'VTA-2025-039', 26,25,  25.00,0.00,  3.25,  28.25,'Transferencia','Completada',NULL,NOW(),NOW()),
(40,'VTA-2025-040', 14,14,  22.00,0.00,  2.86,  24.86,'Tarjeta','Completada',NULL,NOW(),NOW()),
(41,'VTA-2025-041', 25,25,   4.00,0.00,  0.52,   4.52,'QR','Completada',NULL,NOW(),NOW()),
(42,'VTA-2025-042', 20,14, 180.00,0.00, 23.40, 203.40,'Efectivo','Completada',NULL,NOW(),NOW()),
(43,'VTA-2025-043', 27,25, 350.00,0.00, 45.50, 395.50,'Transferencia','Completada',NULL,NOW(),NOW()),
(44,'VTA-2025-044', 18,14,  12.50,0.00,  1.63,  14.13,'Tarjeta','Completada',NULL,NOW(),NOW()),
(45,'VTA-2025-045', 26,25,   3.50,0.00,  0.46,   3.96,'QR','Completada',NULL,NOW(),NOW()),
(46,'VTA-2025-046', 14,14,   2.80,0.00,  0.36,   3.16,'Efectivo','Completada',NULL,NOW(),NOW()),
(47,'VTA-2025-047', 25,25,  14.00,0.00,  1.82,  15.82,'Transferencia','Completada',NULL,NOW(),NOW()),
(48,'VTA-2025-048', 20,14,   9.50,0.00,  1.24,  10.74,'Tarjeta','Completada',NULL,NOW(),NOW()),
(49,'VTA-2025-049', 27,25,  22.00,0.00,  2.86,  24.86,'QR','Completada',NULL,NOW(),NOW()),
(50,'VTA-2025-050', 18,14,  11.00,0.00,  1.43,  12.43,'Efectivo','Completada',NULL,NOW(),NOW());



----------------------------------------------detalle_ventas---------------------------------------------------------------
-- TRUNCATE TABLE detalle_ventas;

INSERT INTO `detalle_ventas`
(`id`,`venta_id`,`producto_tipo`,`producto_id`,`cantidad`,`precio_unitario`,`subtotal`)
VALUES
(1 ,  1, 'libro',  1, 1, 180.00, 180.00),
(2 ,  2, 'libro',  2, 1,  85.00,  85.00),
(3 ,  3, 'libro',  3, 1, 350.00, 350.00),
(4 ,  4, 'libro',  4, 1, 270.00, 270.00),
(5 ,  5, 'libro',  5, 1,  99.90,  99.90),
(6 ,  6, 'libro',  6, 1,  49.50,  49.50),
(7 ,  7, 'libro',  7, 1, 420.00, 420.00),
(8 ,  8, 'libro',  8, 1, 215.75, 215.75),
(9 ,  9, 'libro',  9, 1, 640.00, 640.00),
(10, 10, 'libro', 10, 1,  75.00,  75.00),
(11, 11, 'libro', 11, 1, 130.00, 130.00),
(12, 12, 'libro', 12, 1, 199.99, 199.99),
(13, 13, 'libro', 13, 1, 510.00, 510.00),
(14, 14, 'libro', 14, 1, 320.00, 320.00),
(15, 15, 'libro', 15, 1, 289.50, 289.50),
(16, 16, 'libro', 16, 1, 950.00, 950.00),
(17, 17, 'libro', 17, 1,  35.00,  35.00),
(18, 18, 'libro', 18, 1,  60.00,  60.00),
(19, 19, 'libro', 19, 1, 780.00, 780.00),
(20, 20, 'libro', 20, 1, 145.25, 145.25),
(21, 21, 'libro', 21, 1,  59.90,  59.90),
(22, 22, 'libro', 22, 1, 499.00, 499.00),
(23, 23, 'libro', 23, 1, 125.75, 125.75),
(24, 24, 'libro', 24, 1,  88.80,  88.80),
(25, 25, 'libro', 25, 1, 230.40, 230.40),
(26,26,'componente', 1,'Arduino UNO R3',                         1,  45.00,  45.00),
(27,27,'componente', 2,'Sensor Ultrasónico HC-SR04',             1,   8.00,   8.00),
(28,28,'componente', 3,'Sensor de Temperatura/Humedad DHT22',    1,  12.00,  12.00),
(29,29,'componente', 4,'Módulo Acelerómetro/Giroscopio MPU-6050',1,  15.00,  15.00),
(30,30,'componente', 5,'Protoboard 830 puntos',                  1,  10.00,  10.00),
(31,31,'componente', 6,'Set Jumpers 65 pcs',                     1,   5.00,   5.00),
(32,32,'componente', 7,'Servo SG90 9g',                          1,   9.00,   9.00),
(33,33,'componente', 8,'Motor Paso a Paso NEMA17',               1,  80.00,  80.00),
(34,34,'componente', 9,'Driver A4988',                           1,   6.00,   6.00),
(35,35,'componente',10,'Fuente 12V 2A',                          1,  18.00,  18.00),
(36,36,'componente',11,'Raspberry Pi 4 (4GB RAM)',               1,  95.00,  95.00),
(37,37,'componente',12,'ESP32 DevKit',                            1,  16.00,  16.00),
(38,38,'componente',13,'Módulo Relay 2 canales',                 1,   7.50,   7.50),
(39,39,'componente',14,'Soldador de Estaño 40W',                 1,  25.00,  25.00),
(40,40,'componente',15,'Multímetro Digital',                     1,  22.00,  22.00),
(41,41,'componente',16,'Kit Resistencias 1/4W (500 uds)',        1,   4.00,   4.00),
(42,42,'componente',17,'Kit Arduino para Principiantes',         1, 180.00, 180.00),
(43,43,'componente',18,'Kit Avanzado de Robótica',               1, 350.00, 350.00),
(44,44,'componente',19,'Lote de LEDs 300 pcs',                   1,  12.50,  12.50),
(45,45,'componente',20,'Cable USB A-B',                          1,   3.50,   3.50),
(46,46,'componente',21,'Sensor Infrarrojo IR',                   1,   2.80,   2.80),
(47,47,'componente',22,'Módulo Bluetooth HC-05',                 1,  14.00,  14.00),
(48,48,'componente',23,'Shield Driver L298N',                    1,   9.50,   9.50),
(49,49,'componente',24,'Módulo GPS NEO-6M',                      1,  22.00,  22.00),
(50,50,'componente',25,'Pantalla LCD 16x2 I2C',                  1,  11.00,  11.00);


----------------------------------------------acceso_invitados---------------------------------------------------------------

-- INSERTs para invitados actuales (usuarios 20 y 27)
INSERT INTO `acceso_invitados`
(`id`,`usuario_id`,`fecha_inicio`,`fecha_vencimiento`,`dias_restantes`,`ultima_notificacion`,
 `notificaciones_enviadas`,`acceso_bloqueado`,`fecha_creacion`,`fecha_actualizacion`)
VALUES
-- Usuario 27: ventana 2025-08-31 → 2025-09-03 (faltan 2 días)
(2, 27, '2025-08-31', '2025-09-03', 2, NULL,
 JSON_ARRAY(), 0, NOW(), NOW());



----------------------------------------------laboratorios---------------------------------------------------------------
INSERT INTO `laboratorios`
(`id`,`nombre`,`descripcion`,`objetivos`,`categoria_id`,`docente_responsable_id`,
 `participantes`,`componentes_utilizados`,`tecnologias`,`resultado`,`conclusiones`,
 `nivel_dificultad`,`duracion_dias`,`fecha_inicio`,`fecha_fin`,`estado`,
 `publico`,`destacado`,`fecha_creacion`,`fecha_actualizacion`)
VALUES
(1,
 'Robot Seguidor de Línea (PID)',
 'Construcción de un robot móvil que sigue una línea usando sensores IR y control PID.',
 'Diseñar chasis, integrar sensores IR y servos, implementar control PID y tuning de parámetros.',
 1, 14,
 JSON_ARRAY(18,26,25),                 -- est. 18,26 + docente 25 como apoyo
 JSON_ARRAY(1,2,7,9,14,20),            -- UNO, HC-SR04, SG90, A4988, Soldador, Cable
 JSON_ARRAY('Arduino IDE','C/C++','PID','Tinkercad'),
 'Robot estable con seguimiento >93% en circuitos curvos a 0.3 m/s.',
 'El tuning del PID fue clave; el filtrado de ruido en IR mejoró la estabilidad.',
 'Intermedio', 21, '2025-07-10','2025-07-31','Completado',
 1,1, NOW(),NOW()
),
(2,
 'Red CNN para Clasificación de Componentes',
 'Entrenamiento de una CNN pequeña para clasificar imágenes de componentes electrónicos comunes.',
 'Recolectar dataset, aplicar data augmentation, entrenar CNN y evaluar métricas.',
 4, 25,
 JSON_ARRAY(18,26,14),                 -- est. 18,26 + docente 14 apoyo
 JSON_ARRAY(13,21,23),                 -- Relay, kit resistencias, L298N (para dataset)
 JSON_ARRAY('Python','PyTorch','OpenCV','Jupyter'),
 'Modelo con 88.5% de accuracy en 10 clases.',
 'La iluminación uniforme y el balance de clases fueron determinantes.',
 'Avanzado', 28, '2025-08-01','2025-08-29','Completado',
 1,1, NOW(),NOW()
),
(3,
 'Estación IoT con ESP32 y Dashboard',
 'Despliegue de una mini red de sensores con ESP32 y tablero web para visualizar variables ambientales.',
 'Programar ESP32, publicar por MQTT/HTTP, persistir en BD y construir dashboard.',
 5, 14,
 JSON_ARRAY(18,20,27),                 -- est. 18 + invitados 20,27
 JSON_ARRAY(12,3,5,11,15),             -- ESP32, DHT22, protoboard, RPi (gateway), multímetro
 JSON_ARRAY('ESP32-IDF','WiFi','MQTT','Flask','Chart.js'),
 'Dashboard en producción con alertas por umbrales.',
 'Se optimizó transmisión con payloads compactos y reconexión robusta.',
 'Intermedio', 30, '2025-07-15','2025-08-14','Completado',
 1,0, NOW(),NOW()
),
(4,
 'Brazo Robótico 4DOF con Control Inverso',
 'Construcción de un brazo 4DOF controlado por servos, con cinemática inversa básica.',
 'Modelar cinemática, diseñar trayectorias y validar precisión de posicionamiento.',
 1, 25,
 JSON_ARRAY(26,14),                    -- est. 26 + docente 14 apoyo
 JSON_ARRAY(7,1,19,10),                -- SG90, UNO, LEDs (indicadores), fuente 12V
 JSON_ARRAY('Arduino','Python','Cinemática','Qt'),
 'Precisión ±3mm en workspace definido; teleoperación desde GUI.',
 'Limitaciones mecánicas requieren refuerzo del eslabón 2; siguiente fase: control por visión.',
 'Avanzado', 35, '2025-08-10',NULL,'En Progreso',
 1,0, NOW(),NOW()
),
(5,
 'Análisis de Ventas con ML (MLOps mini)',
 'Pipeline de predicción de demanda sobre dataset de ventas con despliegue simple.',
 'Explorar datos, crear features, entrenar modelo base y exponer endpoint.',
 5, 14,
 JSON_ARRAY(18,26,20),                 -- est. 18,26 + invitado 20
 JSON_ARRAY(23),                        -- Shield L298N solo de ejemplo de dataset técnico
 JSON_ARRAY('Python','Pandas','scikit-learn','FastAPI','Docker'),
 'MAE inicial 12.3; endpoint REST listo para pruebas A/B.',
 'El feature de estacionalidad semanal fue el mayor aporte; falta ajustar holidays.',
 'Intermedio', 14, '2025-08-20','2025-09-03','Planificado',
 1,1, NOW(),NOW()
);
