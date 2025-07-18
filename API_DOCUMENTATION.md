# API Documentation for Mobile App

This document provides comprehensive information about the API endpoints available for your mobile app integration with Laravel Filament backend.

## Base URL
```
http://your-domain.com/api
```

## Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header for protected routes.

### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

## Authentication Endpoints

### 1. Register User
**POST** `/api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "message": "Registration successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123..."
}
```

### 2. Login User
**POST** `/api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123..."
}
```

### 3. Get Current User
**GET** `/api/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Logout User
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

## User Management Endpoints

### 1. List Users
**GET** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (optional): Search by name or email
- `sort_by` (optional): Field to sort by (name, email, created_at)
- `sort_direction` (optional): asc or desc
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://your-domain.com/api/users?page=1",
        "last": "http://your-domain.com/api/users?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
```

### 2. Get Single User
**GET** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 3. Create User
**POST** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "message": "User created successfully",
    "user": {
        "id": 2,
        "name": "Jane Doe",
        "email": "jane@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Update User
**PUT/PATCH** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com"
}
```

**Response:**
```json
{
    "message": "User updated successfully",
    "user": {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane.smith@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 5. Delete User
**DELETE** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "User deleted successfully"
}
```

## Error Responses

All endpoints return consistent error responses:

### Validation Errors (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```

### Authentication Errors (401)
```json
{
    "message": "Unauthenticated."
}
```

### Not Found Errors (404)
```json
{
    "message": "Resource not found."
}
```

### Server Errors (500)
```json
{
    "message": "Internal server error."
}
```

## Mobile App Integration Tips

### 1. Token Storage
Store the authentication token securely in your mobile app:
- iOS: Keychain
- Android: Encrypted SharedPreferences
- React Native: SecureStore or AsyncStorage with encryption

### 2. Token Refresh
Implement automatic token refresh when the token expires:
```javascript
// Example for React Native
const refreshToken = async () => {
    try {
        const response = await fetch('/api/refresh', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${currentToken}`,
                'Content-Type': 'application/json',
            },
        });
        const data = await response.json();
        // Store new token
        await SecureStore.setItemAsync('auth_token', data.token);
    } catch (error) {
        // Redirect to login
    }
};
```

### 3. Error Handling
Implement proper error handling for network requests:
```javascript
const handleApiError = (error) => {
    if (error.status === 401) {
        // Token expired, redirect to login
        navigateToLogin();
    } else if (error.status === 422) {
        // Validation errors, show to user
        showValidationErrors(error.errors);
    } else {
        // Generic error
        showErrorMessage(error.message);
    }
};
```

### 4. Offline Support
Consider implementing offline support for better user experience:
- Cache frequently accessed data
- Queue actions when offline
- Sync when connection is restored

## Adding New Resources

To add new API resources (e.g., Projects, Tasks, Clients), follow this pattern:

1. Create the model and migration
2. Create the API controller extending BaseController
3. Create request validation classes
4. Create API resources
5. Add routes to `routes/api.php`

Example for a Project resource:
```php
// routes/api.php
Route::apiResource('projects', ProjectController::class);

// app/Http/Controllers/Api/ProjectController.php
class ProjectController extends BaseController
{
    public function index(Request $request)
    {
        $projects = Project::paginate($request->per_page ?? 15);
        return ProjectResource::collection($projects);
    }
    
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());
        return $this->createdResponse(new ProjectResource($project));
    }
    
    // ... other methods
}
```

## Testing the API

You can test the API endpoints using tools like:
- Postman
- Insomnia
- curl commands
- Laravel's built-in testing

Example curl command:
```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
``` 