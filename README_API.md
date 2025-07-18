# Laravel Filament API for Mobile App

This document explains how to implement and use the API backend for your mobile app using Laravel Filament.

## 🚀 What's Been Implemented

### 1. **Authentication System**
- ✅ Laravel Sanctum for token-based authentication
- ✅ User registration and login endpoints
- ✅ Token management (create, validate, revoke)
- ✅ Protected routes with middleware

### 2. **API Structure**
- ✅ RESTful API endpoints
- ✅ Consistent JSON responses
- ✅ Proper HTTP status codes
- ✅ Request validation
- ✅ API resources for data formatting

### 3. **User Management**
- ✅ Full CRUD operations for users
- ✅ Search and filtering capabilities
- ✅ Pagination support
- ✅ Sorting options

### 4. **Project Management (Example Resource)**
- ✅ Full CRUD operations for projects
- ✅ Relationship with users
- ✅ Advanced filtering and search
- ✅ Date and budget handling

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php          # Authentication endpoints
│   │       ├── BaseController.php          # Base API controller
│   │       ├── UserController.php          # User management
│   │       └── ProjectController.php       # Project management
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginRequest.php            # Login validation
│   │       ├── RegisterRequest.php         # Registration validation
│   │       ├── StoreUserRequest.php        # User creation validation
│   │       ├── UpdateUserRequest.php       # User update validation
│   │       ├── StoreProjectRequest.php     # Project creation validation
│   │       └── UpdateProjectRequest.php    # Project update validation
│   └── Resources/
│       ├── UserResource.php                # User data formatting
│       └── ProjectResource.php             # Project data formatting
├── Models/
│   ├── User.php                           # User model with Sanctum
│   └── Project.php                        # Project model
routes/
└── api.php                                # API routes
```

## 🔧 Setup Instructions

### 1. Install Dependencies
```bash
composer require laravel/sanctum
```

### 2. Publish Sanctum Configuration
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Update Bootstrap Configuration
Make sure your `bootstrap/app.php` includes API routes:
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // Add this line
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

## 📡 Available API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/user` - Get current user

### Users
- `GET /api/users` - List users (with search, sort, pagination)
- `POST /api/users` - Create user
- `GET /api/users/{id}` - Get specific user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Projects
- `GET /api/projects` - List projects (with filters)
- `POST /api/projects` - Create project
- `GET /api/projects/{id}` - Get specific project
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

## 🧪 Testing the API

### 1. Start the Development Server
```bash
php artisan serve
```

### 2. Run the Test Script
```bash
php test_api.php
```

### 3. Manual Testing with curl
```bash
# Register a user
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get user profile (replace TOKEN with actual token)
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer TOKEN"
```

## 📱 Mobile App Integration

### 1. Authentication Flow
```javascript
// Register user
const register = async (userData) => {
    const response = await fetch('/api/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    });
    const data = await response.json();
    
    // Store token securely
    await SecureStore.setItemAsync('auth_token', data.token);
    return data;
};

// Login user
const login = async (credentials) => {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials)
    });
    const data = await response.json();
    
    // Store token securely
    await SecureStore.setItemAsync('auth_token', data.token);
    return data;
};
```

### 2. API Client Setup
```javascript
// API client with authentication
class ApiClient {
    constructor() {
        this.baseUrl = 'http://your-domain.com/api';
    }
    
    async getToken() {
        return await SecureStore.getItemAsync('auth_token');
    }
    
    async request(endpoint, options = {}) {
        const token = await this.getToken();
        
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(token && { 'Authorization': `Bearer ${token}` }),
                ...options.headers,
            },
            ...options,
        };
        
        const response = await fetch(`${this.baseUrl}${endpoint}`, config);
        
        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`);
        }
        
        return response.json();
    }
    
    // User methods
    async getUsers(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.request(`/users?${queryString}`);
    }
    
    async createUser(userData) {
        return this.request('/users', {
            method: 'POST',
            body: JSON.stringify(userData),
        });
    }
    
    // Project methods
    async getProjects(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.request(`/projects?${queryString}`);
    }
    
    async createProject(projectData) {
        return this.request('/projects', {
            method: 'POST',
            body: JSON.stringify(projectData),
        });
    }
}
```

## 🔄 Adding New Resources

To add a new resource (e.g., Tasks, Clients, etc.), follow this pattern:

### 1. Create Model and Migration
```bash
php artisan make:model Task -m
```

### 2. Define Migration
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('status')->default('pending');
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

### 3. Create Controller
```bash
php artisan make:controller Api/TaskController
```

### 4. Create Request Classes
```bash
php artisan make:request Api/StoreTaskRequest
php artisan make:request Api/UpdateTaskRequest
```

### 5. Create Resource
```bash
php artisan make:resource TaskResource
```

### 6. Add Routes
```php
// routes/api.php
Route::apiResource('tasks', TaskController::class);
```

## 🛡️ Security Features

### 1. Token Authentication
- Secure token generation with Laravel Sanctum
- Automatic token expiration
- Token revocation on logout

### 2. Request Validation
- Comprehensive input validation
- Custom error messages
- SQL injection prevention

### 3. Rate Limiting
Add to your routes for additional security:
```php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Your protected routes
});
```

## 📊 Response Format

All API responses follow a consistent format:

### Success Response
```json
{
    "success": true,
    "message": "Resource created successfully",
    "data": {
        "id": 1,
        "name": "Example",
        "created_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## 🚀 Production Deployment

### 1. Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. CORS Configuration
Update `config/cors.php` for mobile app access:
```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Restrict in production
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### 3. Security Headers
Add security middleware in production:
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    
    return $response;
}
```

## 📚 Additional Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)
- [Filament Documentation](https://filamentphp.com/docs)
- [API Design Best Practices](https://restfulapi.net/)

## 🤝 Support

If you need help with:
- Adding new API endpoints
- Customizing responses
- Mobile app integration
- Security implementation

Feel free to ask questions or refer to the comprehensive API documentation in `API_DOCUMENTATION.md`. 