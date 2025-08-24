# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**TECH-HOME** is a comprehensive educational management system for "Tech Home Bolivia - Escuela de Robótica y Tecnología Avanzada" (Robotics and Advanced Technology School). It combines learning management, e-commerce for electronic components/books, user management, and reporting capabilities.

### Business Context
- Educational institution specializing in robotics and technology
- Multi-role system: Administrators, Teachers, Students, Vendors
- E-commerce platform for electronic components and books
- Course and material management system
- Comprehensive reporting and analytics

## Architecture Overview

### Custom MVC Framework
This is **NOT** Laravel, Symfony, or any standard framework. It's a completely custom-built MVC framework with Laravel-inspired patterns.

#### Core Framework Components (`/Core/`)
- **Router.php**: Custom routing system with middleware support, parameter extraction, named routes
- **Route.php**: Route objects with middleware and parameter handling
- **Request.php**: Singleton request handler supporting GET/POST/PUT/DELETE/PATCH with JSON and form data
- **Response.php**: Response handler with JSON support, redirects, and HTTP status codes
- **Model.php**: Custom ORM with query builder, relationships (belongsTo, hasOne, hasMany), soft deletes
- **DB.php**: Database singleton with PDO, connection management, transactions
- **QueryBuilder.php**: Fluent query builder supporting complex queries, eager loading
- **Validation.php**: Comprehensive validation system with custom rules
- **Session.php**: Session management with flash messages
- **Controller.php**: Base controller class
- **Middleware.php**: Interface for middleware implementation
- **MiddlewareFactory.php**: Advanced middleware factory with parameter parsing

### Directory Structure
```
/App/
├── Controllers/     # MVC Controllers
├── Models/          # Data models with relationships
├── Middleware/      # Custom middleware (Auth, Role)
├── Services/        # Business logic services
├── Enums/           # Enums and constants

/Core/               # Custom framework
├── Router.php       # Route handling
├── Model.php        # ORM base class
├── DB.php          # Database layer
├── QueryBuilder.php # Query builder
├── Validation.php   # Validation system
└── helpers.php      # Global helper functions

/resources/
├── views/          # PHP template views
└── PHPMailer/      # Email library

/database/
├── migrations/     # SQL migration files
└── seeders/        # SQL seed data

/routes/
├── web.php         # Web routes
└── api.php         # API routes

/public/            # Static assets
├── css/
├── js/
└── imagenes/
```

## Development Environment Setup

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP)
- **PHP 8.0+** with extensions: PDO, ctype, filter, hash
- **MySQL 5.7+** with UTF8MB4 support

### Environment Configuration

1. **Copy environment files:**
   ```bash
   cp .env.example .env
   ```

2. **Configure database (.env):**
   ```env
   DB_DRIVER=mysql
   DB_HOST=localhost
   DB_NAME=tech_home
   DB_USER=root
   DB_PASS=''
   ```

3. **Configure email (.env.example.mail):**
   ```env
   MAIL_SERVICE_CLASS=SimpleMailService  # or PHPMailerService
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_FROM_ADDRESS=noreply@techhome.bo
   MAIL_FROM_NAME="Tech Home Bolivia"
   APP_URL=http://localhost/TECH-HOME
   PASSWORD_RESET_TOKEN_EXPIRATION_MINUTES=15
   ```

### Database Setup
1. **Create database:** `CREATE DATABASE tech_home CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
2. **Run migrations:** Execute SQL files in `/database/migrations/` in order
3. **Seed data:** Execute SQL files in `/database/seeders/`

### Development Server
- Place in XAMPP's `htdocs/TECH-HOME`
- Access via `http://localhost/TECH-HOME`
- No build process required - direct PHP execution

## Framework Deep Dive

### Routing System

#### Route Definition (`routes/web.php`)
```php
// Basic routes
Router::get('/admin/dashboard', [AdminController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware('role:administrador|has:admin.dashboard');

// Route with parameters
Router::get('/usuarios/{id}/editar', [AdminController::class, 'editarUsuario'])
    ->name('usuarios.editar')
    ->middleware('role:administrador|has:admin.usuarios.editar');
```

#### Route Parameters
- Automatic parameter extraction: `{id}` becomes method parameter
- Type conversion (numeric strings → integers)
- Pattern matching with regex

#### Named Routes
- Generate URLs: `route('admin.dashboard')` or `route('usuarios.editar', ['id' => 1])`
- Template usage: `<?= route('login') ?>`

### Middleware System

#### Advanced Middleware Factory
Supports complex middleware expressions:
```php
// Role-based
'role:administrador,docente'

// Permission-based
'has:admin.usuarios.ver,admin.usuarios.editar'

// Combined (role OR permission)
'role:administrador|has:admin.usuarios.ver'

// Multiple roles with specific permission
'role:docente,estudiante|has:cursos.ver'
```

#### Built-in Middleware
- **AuthMiddleware**: Session-based authentication
- **RoleMiddleware**: Complex role and permission checking with super admin support

### Database & ORM System

#### Model Definition
```php
class User extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'apellido', 'email', 'password'];
    protected $hidden = ['password'];
    protected $timestamps = false; // Custom timestamp handling
}
```

#### Query Builder Features
```php
// Fluent queries
User::where('estado', '=', 1)
    ->whereRaw('fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [7])
    ->orderBy('fecha_creacion', 'desc')
    ->limit(10)
    ->get();

// Eager loading
User::with(['roles'])->get();

// Scopes
User::activos()->porRol(1)->get();
```

#### Relationships
```php
// belongsTo
public function role() {
    return $this->belongsTo(Role::class, 'role_id', 'id');
}

// hasMany
public function orders() {
    return $this->hasMany(Order::class, 'user_id', 'id');
}
```

### Authentication & Authorization

#### Laravel Spatie-inspired Role System
```php
// Role assignment
$user->assignRole('administrador');
$user->syncRoles(['docente', 'estudiante']);

// Permission checking
$user->can('admin.usuarios.ver');
$user->hasRole('administrador');
$user->hasAnyRole(['docente', 'administrador']);

// Complex permission queries
if ($user->can('admin.ventas.crear') || $user->hasRole('vendedor')) {
    // Allow access
}
```

#### Database Schema
- `usuarios` (users)
- `roles` 
- `permissions`
- `model_has_roles` (pivot table)
- `model_has_permissions` (pivot table)
- `role_has_permissions` (pivot table)

### View System

#### Template Engine
PHP-based templates with layout support:
```php
// Controller
return view('admin.dashboard', $data, 'layouts.app');

// View access
<?= $variable ?>
<?php if ($condition): ?>
<?php endif; ?>

// Global helpers
<?= route('admin.dashboard') ?>
<?= asset('css/admin.css') ?>
<?= auth()->nombre ?>
<?= csrf_token() ?>
```

#### Layout System
- **Layout**: `resources/views/layouts/app.view.php`
- **Components**: Sidebar, Header, Footer as included components
- **Content**: `<?= $layoutContent ?>` in layouts
- **Flash Messages**: `<?= flashGet('errors') ?>`

### Validation System

#### Validation Rules
```php
$validator = new Validation();
$isValid = $validator->validate($data, [
    'nombre' => 'required|string|min:3|max:50',
    'email' => 'required|email|unique:User,email',
    'password' => 'required|min:8|confirmed',
    'roles' => 'array|min:1'
]);

if (!$isValid) {
    $errors = $validator->getErrors(); // Associative array by field
}
```

#### Custom Rules
- `unique:Model,column` - Database uniqueness
- `confirmed` - Field confirmation (password_confirmation)
- `in:value1,value2` - Allowed values
- `same:other_field` - Field matching

### Service Layer

#### Service Pattern
```php
class AdminService 
{
    public function showDashboard() {
        // Complex business logic
        // Statistics aggregation
        // Data transformation
        return $compiledData;
    }
}
```

#### Email Service Factory
```php
// Factory pattern for email services
$mailService = mailService(); // Uses MAIL_SERVICE_CLASS from .env
$mailService->send($to, $subject, $body);
```

## Key Business Logic

### User Roles & Permissions

#### Built-in Roles
- **Administrador**: Full system access, super admin privileges
- **Docente**: Course management, materials, laboratories  
- **Estudiante**: Course access, materials viewing
- **Vendedor**: Component and book sales management

#### Permission Structure
```
admin.dashboard
admin.usuarios.ver, .crear, .editar, .eliminar, .roles, .permisos
admin.ventas.ver, .crear
admin.reportes
admin.configuracion
cursos.ver, .crear
libros.ver, .crear  
componentes.ver, .crear
materiales.ver
laboratorios.ver
```

### Dashboard System

#### Statistics & Metrics
- User counts by role (active/total)
- Course, book, and component inventories
- Sales analytics and recent activity
- System health and session monitoring
- Real-time updates via AJAX

#### Widgets
- Quick actions (create users, courses, components)
- Recent activity feed
- Active sessions monitoring  
- Sales transactions
- Inventory alerts (low stock)

## Development Patterns & Conventions

### Code Style
- **PSR-4** autoloading for `App\` namespace
- **Camel case** for methods: `crearUsuario()`, `editarRol()`
- **Snake case** for database: `fecha_creacion`, `usuario_id`
- **Spanish** for business logic (user-facing): `crearUsuario`, `reportes`
- **English** for technical terms: `QueryBuilder`, `MiddlewareFactory`

### Error Handling
```php
try {
    $result = $this->service->complexOperation();
    return view('success', $result);
} catch (Exception $e) {
    return view('errors.500', ['message' => $e->getMessage()]);
}
```

### Response Patterns
```php
// Web responses
return view('admin.dashboard', $data);
return redirect(route('login'))->with('error', 'Mensaje');

// API responses  
return Response::json(['success' => true, 'data' => $data]);
return Response::error('Mensaje de error', 400);
```

### Form Handling
```php
public function guardarUsuario(Request $request) {
    $validator = new Validation();
    $data = $request->only(['nombre', 'apellido', 'email']);
    
    if (!$validator->validate($data, $rules)) {
        return redirect(back())
            ->with('errors', $validator->getErrors())
            ->with('old', $data);
    }
    
    // Process valid data
}
```

## Helper Functions (`Core/helpers.php`)

### Global Utilities
```php
// Views and responses
view($view, $data, $layout) // Render view with layout
redirect($url) // HTTP redirect
response()->json($data) // JSON response

// Authentication
auth() // Current user object
isAuth() // Boolean check
session($key) // Session data
flashGet($key) // Flash messages

// URLs and assets
route($name, $params) // Generate route URL
asset($path) // Asset URL with BASE_URL
url($path) // General URL helper

// Data formatting
formatearMoneda($amount) // Bolivian currency format
formatearNumero($number) // Number formatting  
tiempoTranscurrido($date) // Human-readable time ago
formatearBytes($bytes) // File size formatting

// Security
csrf_token() // CSRF token
CSRF() // CSRF input field

// Debugging
dd($var) // Dump and die
```

## Security Features

### Implemented Security
- **CSRF Protection**: `csrf_token()`, `csrf_verify()`
- **SQL Injection**: Prepared statements via PDO
- **Authentication**: Session-based with timeout
- **Authorization**: Role and permission-based access control
- **Password Security**: `password_hash()` and `password_verify()`
- **Input Validation**: Comprehensive validation system
- **XSS Protection**: `htmlspecialchars()` in templates

### Session Management
- Session-based authentication
- Flash message system
- Session verification endpoints
- Automatic session cleanup

## Common Development Tasks

### Adding New Routes
1. Define route in `routes/web.php`
2. Add middleware if needed
3. Create controller method
4. Create view file
5. Test route accessibility

### Creating New Models
1. Extend `Core\Model`
2. Define table, fillable, relationships
3. Add business logic methods
4. Create database migration

### Adding Middleware
1. Implement `Core\Middleware` interface
2. Register in `MiddlewareFactory`
3. Apply to routes with parameters

### Form Processing Pattern
1. Validate input with `Validation` class
2. Handle validation errors with flash messages
3. Process valid data
4. Redirect with success/error messages

## Performance Considerations

### Database
- Use query builder for complex queries
- Implement eager loading for relationships
- Utilize database indexes properly
- Consider query caching for statistics

### Security
- Validate all user input
- Use prepared statements
- Implement proper session handling
- Regular security audits of permissions

### Code Organization
- Follow service layer pattern
- Keep controllers thin
- Use dependency injection
- Maintain separation of concerns

This system is production-ready with comprehensive user management, e-commerce capabilities, and educational tools specifically designed for a technical training institute.