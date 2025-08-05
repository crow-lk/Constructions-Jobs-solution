# API Documentation

## Authentication Endpoints

### Login
- **POST** `/api/login`
- **Description**: Authenticate user and get access token
- **Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Login successful",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "worker",
      "business_registration_number": "BRN123456",
      "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
      "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
    },
    "token": "1|abc123..."
  }
  ```

### Register
- **POST** `/api/register`
- **Description**: Register a new user
- **Body** (multipart/form-data):
  ```json
  {
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "worker",
    "business_registration_number": "BRN123456",
    "business_registration_document": "[file upload]"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Registration successful",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "worker",
      "business_registration_number": "BRN123456",
      "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
      "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
    },
    "token": "1|abc123..."
  }
  ```

### Logout
- **POST** `/api/logout`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Logout user and invalidate token
- **Response**:
  ```json
  {
    "message": "Logged out successfully"
  }
  ```

### Get Current User
- **GET** `/api/user`
- **Headers**: `Authorization: Bearer {token}`
- **Description**: Get current authenticated user details
- **Response**:
  ```json
  {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "worker",
      "business_registration_number": "BRN123456",
      "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
      "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
    }
  }
  ```

## User Management Endpoints

### List Users
- **GET** `/api/users`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `search` (optional): Search by name or email
  - `sort_by` (optional): Field to sort by
  - `sort_direction` (optional): asc or desc
  - `per_page` (optional): Number of items per page (default: 15)
- **Response**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "worker",
        "business_registration_number": "BRN123456",
        "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
        "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
      }
    ],
    "links": {...},
    "meta": {...}
  }
  ```

### Create User
- **POST** `/api/users`
- **Headers**: `Authorization: Bearer {token}`
- **Body** (multipart/form-data):
  ```json
  {
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "role": "worker",
    "business_registration_number": "BRN123456",
    "business_registration_document": "[file upload]"
  }
  ```
- **Response**:
  ```json
  {
    "message": "User created successfully",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "worker",
      "business_registration_number": "BRN123456",
      "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
      "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
    }
  }
  ```

### Get User
- **GET** `/api/users/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "worker",
      "business_registration_number": "BRN123456",
      "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
      "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
    }
  }
  ```

### Update User
- **PUT/PATCH** `/api/users/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Body** (multipart/form-data):
  ```json
  {
    "name": "John Doe Updated",
    "email": "updated@example.com",
    "role": "worker",
    "business_registration_number": "BRN123456",
    "business_registration_document": "[file upload]"
  }
  ```
- **Response**:
  ```json
  {
    "message": "User updated successfully",
    "user": {
      "id": 1,
      "name": "John Doe Updated",
      "email": "updated@example.com",
      "role": "worker",
      "business_registration_number": "BRN123456",
      "business_registration_document": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
      "business_registration_document_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
    }
  }
  ```

### Delete User
- **DELETE** `/api/users/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "message": "User deleted successfully"
  }
  ```

## File Upload Endpoints

### Upload Business Registration Document
- **POST** `/api/files/business-registration-document/upload`
- **Headers**: `Authorization: Bearer {token}`
- **Body** (multipart/form-data):
  ```json
  {
    "document": "[file upload - PDF, JPG, JPEG, PNG, max 10MB]",
    "user_id": 1
  }
  ```
- **Response**:
  ```json
  {
    "message": "Business registration document uploaded successfully",
    "file_path": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
    "file_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf"
  }
  ```

### Get Business Registration Document
- **GET** `/api/files/business-registration-document/get`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `user_id`: User ID
- **Response**:
  ```json
  {
    "file_path": "business-registration-documents/business_registration_1_1234567890_abc123.pdf",
    "file_url": "http://localhost:8000/storage/business-registration-documents/business_registration_1_1234567890_abc123.pdf",
    "file_name": "business_registration_1_1234567890_abc123.pdf"
  }
  ```

### Download Business Registration Document
- **GET** `/api/files/business-registration-document/download`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `user_id`: User ID
- **Response**: File download

### Delete Business Registration Document
- **DELETE** `/api/files/business-registration-document/delete`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
  ```json
  {
    "user_id": 1
  }
  ```
- **Response**:
  ```json
  {
    "message": "Business registration document deleted successfully"
  }
  ```

## Role Endpoints

### List Roles
- **GET** `/api/roles`
- **Description**: Get all available roles
- **Response**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "admin",
        "description": "Administrator",
        "permissions": ["all"]
      },
      {
        "id": 2,
        "name": "worker",
        "description": "Worker",
        "permissions": ["create", "read", "update"]
      },
      {
        "id": 3,
        "name": "client",
        "description": "Client",
        "permissions": ["read"]
      }
    ]
  }
  ```

### Get Role
- **GET** `/api/roles/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "role": {
      "id": 1,
      "name": "admin",
      "description": "Administrator",
      "permissions": ["all"]
    }
  }
  ```

## Category Endpoints

### List Categories
- **GET** `/api/categories`
- **Description**: Get all categories
- **Response**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Construction",
        "description": "Construction services",
        "is_active": true
      }
    ]
  }
  ```

### Get Active Categories
- **GET** `/api/categories/active`
- **Description**: Get only active categories
- **Response**: Same as above but only active categories

### Get Category
- **GET** `/api/categories/{id}`
- **Description**: Get specific category
- **Response**:
  ```json
  {
    "category": {
      "id": 1,
      "name": "Construction",
      "description": "Construction services",
      "is_active": true,
      "sub_categories": [...]
    }
  }
  ```

## SubCategory Endpoints

### List SubCategories
- **GET** `/api/subcategories`
- **Description**: Get all subcategories
- **Response**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Plumbing",
        "description": "Plumbing services",
        "category_id": 1,
        "is_active": true
      }
    ]
  }
  ```

### Get Active SubCategories
- **GET** `/api/subcategories/active`
- **Description**: Get only active subcategories
- **Response**: Same as above but only active subcategories

### Get SubCategory
- **GET** `/api/subcategories/{id}`
- **Description**: Get specific subcategory
- **Response**:
  ```json
  {
    "sub_category": {
      "id": 1,
      "name": "Plumbing",
      "description": "Plumbing services",
      "category_id": 1,
      "is_active": true,
      "category": {...}
    }
  }
  ```

### Get SubCategories by Category
- **GET** `/api/categories/{category_id}/subcategories`
- **Description**: Get subcategories for a specific category
- **Response**: Same as subcategories list but filtered by category

### Get Active SubCategories by Category
- **GET** `/api/categories/{category_id}/subcategories/active`
- **Description**: Get active subcategories for a specific category
- **Response**: Same as above but only active subcategories

## Error Responses

All endpoints may return the following error responses:

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "message": "User not found."
}
```

### Server Error (500)
```json
{
  "message": "Internal server error."
}
```

## File Upload Requirements

### Business Registration Document
- **Accepted Formats**: PDF, JPG, JPEG, PNG
- **Maximum Size**: 10MB
- **Storage Location**: `storage/app/public/business-registration-documents/`
- **Access URL**: `http://your-domain.com/storage/business-registration-documents/filename`

### File Naming Convention
Files are automatically named using the pattern:
`business_registration_{user_id}_{timestamp}_{random_string}.{extension}`

Example: `business_registration_1_1234567890_abc123.pdf`

## Notes

1. **Authentication**: Most endpoints require Bearer token authentication
2. **File Uploads**: Use `multipart/form-data` for endpoints that accept file uploads
3. **Worker Role Requirements**: Users with "worker" role must provide both `business_registration_number` and `business_registration_document`
4. **File Storage**: Files are stored in the public disk and accessible via the storage link
5. **File Cleanup**: Files are automatically deleted when users are deleted or documents are replaced 