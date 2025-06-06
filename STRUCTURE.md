# 📁 Estructura del Proyecto: TECH-HOME

Este repositorio representa un sistema educativo completo desarrollado en PHP, organizado bajo el patrón MVC, con funcionalidades de gestión de cursos, materiales, usuarios y estudiantes. A continuación se describe la estructura principal:

## 📂 tech-home/

### ⚙️ config/
Archivos de configuración del sistema:
- `bootstrap.php`: Inicialización del sistema y autoloaders.
- `constantes.php`: Constantes globales (URLs, rutas, configuración).
- `database.php`: Parámetros de conexión a la base de datos.
- `rutas.php`: Definición de rutas principales del sistema.
- `sesion.php`: Manejo y control de sesiones.

### 🎮 controladores/
Controladores que contienen la lógica de negocio:
- `AuthControlador.php`: Registro, login, logout y autenticación.
- `CursoControlador.php`: CRUD de cursos y suscripciones.
- `DashboardControlador.php`: Panel principal (admin/docente/estudiante).
- `EstudianteControlador.php`: Funciones para estudiantes.
- `LibroControlador.php`: Gestión de biblioteca virtual.
- `MaterialControlador.php`: Subida y gestión de materiales educativos.
- `UsuarioControlador.php`: Gestión de usuarios y permisos.

### 🗄️ database/
Gestión y estructura de la base de datos:
- `Database.php`: Clase de conexión PDO.
- `backup.php`: Script de respaldo automático.
- `sql/`
  - `estructura_inicial.sql`: Tablas base.
  - `datos_iniciales.sql`: Roles, usuarios, categorías por defecto.
  - `actualizaciones/`: Scripts para futuras migraciones.

### 🔧 includes/
Funciones auxiliares reutilizables en el sistema:
- `funciones.php`: Funciones generales.
- `funciones_archivos.php`: Validación y carga de archivos.
- `funciones_auth.php`: Manejo de autenticación y roles.
- `funciones_validacion.php`: Validaciones comunes.

### 📚 librerias/
Librerías externas o personalizadas:
- `PDFGenerator.php`: Generación de certificados y documentos PDF.
- `VideoPlayer.php`: Reproductor de contenido multimedia.
- `ImageResizer.php`: Redimensionamiento de imágenes.

### 🗃️ modelos/
Modelos de datos que se conectan con la BD:
- `BaseModelo.php`: Funcionalidades comunes CRUD.
- `Usuario.php`, `Curso.php`, `Estudiante.php`, `Libro.php`, `Material.php`

### 🌐 publico/
Archivos accesibles públicamente desde el navegador.

#### 🎨 css/
Hojas de estilo organizadas:
- `admin/`, `estudiante/`: Estilos por rol.
- `app.css`, `auth.css`, `curso.css`, `dashboard.css`, `login.css`, `material.css`, `perfil.css`

#### 🖼️ imagenes/
Imágenes del sistema organizadas:
- `avatars/`, `banners/`, `cursos/`, `logos/`, `materiales/`, `redes_sociales/`

#### ⚡ js/
Scripts JavaScript del frontend:
- `modulos/`: JS modularizado (`auth.js`, `cursos.js`, `materiales.js`)
- `curso.js`, `estudiante.js`, `main.js`, `material.js`

#### 📤 uploads/
Archivos subidos por los usuarios:
- `cursos/`: Documentos, imágenes, videos por curso.
- `libros/`: Biblioteca digital (PDFs).
- `usuarios/`: Archivos personales de usuario.

### 🧰 utilidades/
Clases de utilidad para el sistema:
- `EnviadorEmail.php`: Envío de correos.
- `GeneradorPDF.php`: Certificados.
- `SubidaArchivos.php`: Manejo seguro de archivos.
- `ValidadorEntrada.php`: Validaciones personalizadas.

### 👁️ vistas/
Vistas organizadas por funcionalidad y rol:

#### 🔐 auth/
Login, registro y recuperación:
- `login.php`, `registro.php`, `recuperar.php`

#### 📚 cursos/
Gestión de cursos:
- `crear.php`, `editar.php`, `index.php`, `lecciones.php`, `ver.php`

#### 📊 dashboard/
Paneles personalizados por rol:
- `admin.php`, `docente.php`, `estudiante.php`

#### ❌ errores/
Páginas de error:
- `403.php`, `404.php`, `500.php`

#### 🎓 estudiantes/
Perfil del estudiante y estadísticas:
- `certificados.php`, `cursos_inscritos.php`, `perfil.php`, `progreso.php`

#### 🔗 includes/
Componentes visuales comunes:
- `alertas.php`, `breadcrumb.php`, `navegacion.php`

#### 🏗️ layouts/
Plantillas base reutilizables:
- `header.php`, `footer.php`, `main.php`, `sidebar.php`

#### 📖 libros/
Biblioteca virtual:
- `index.php`, `ver.php`, `descargar.php`

#### 📁 materiales/
Gestión de materiales:
- `index.php`, `ver.php`, `subir.php`, `descargar.php`

#### 👥 usuarios/
Administración de usuarios:
- `crear.php`, `editar.php`, `eliminar.php`, `index.php`

---

### Archivos raíz:
- `.gitignore`: Archivos ignorados por Git.
- `.htaccess`: Reglas para servidor Apache.
- `autoload.php`: Cargador automático de clases.
- `index.php`: Punto de entrada principal.
- `login.php`: Acceso directo a login.
- `logout.php`: Cierre de sesión.
- `README.md`: Documentación del sistema (este archivo).

---

> ✅ Este proyecto representa una solución educativa completa, modular, escalable y segura, ideal para instituciones que desean digitalizar su oferta educativa mediante tecnologías web modernas.

