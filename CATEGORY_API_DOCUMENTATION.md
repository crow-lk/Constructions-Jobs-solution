# Category and SubCategory API Documentation

This document describes the API endpoints for managing categories and subcategories in the DimoConstructions application.

## Base URL
```
/api
```

## Authentication
Most endpoints are public and don't require authentication. Protected endpoints will be clearly marked.

## Categories API

### 1. List All Categories
**GET** `/categories`

Returns a paginated list of all categories with optional filtering and search capabilities.

**Query Parameters:**
- `status` (optional): Filter by status - `active` or `inactive`
- `search` (optional): Search categories by name
- `with_subcategories_count` (optional): Include subcategories count in response
- `with_subcategories` (optional): Include subcategories data in response
- `per_page` (optional): Number of items per page (default: 15)

**Example Request:**
```bash
GET /api/categories?status=active&search=construction&with_subcategories_count=true&per_page=10
```

**Example Response:**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Construction",
        "status": "active",
        "sub_categories_count": 5,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 1,
    "last_page_url": "...",
    "links": [...],
    "next_page_url": null,
    "path": "...",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### 2. Get Active Categories
**GET** `/categories/active`

Returns all active categories with subcategories count.

**Example Request:**
```bash
GET /api/categories/active
```

**Example Response:**
```json
{
  "success": true,
  "message": "Active categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Construction",
      "status": "active",
      "sub_categories_count": 5,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

### 3. Get Specific Category
**GET** `/categories/{id}`

Returns a specific category by ID with optional subcategories data.

**Query Parameters:**
- `with_subcategories` (optional): Include subcategories data in response

**Example Request:**
```bash
GET /api/categories/1?with_subcategories=true
```

**Example Response:**
```json
{
  "success": true,
  "message": "Category retrieved successfully",
  "data": {
    "id": 1,
    "name": "Construction",
    "status": "active",
    "sub_categories_count": 5,
    "sub_categories": [
      {
        "id": 1,
        "name": "Residential Construction",
        "status": "active",
        "category_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

## SubCategories API

### 1. List All SubCategories
**GET** `/subcategories`

Returns a paginated list of all subcategories with optional filtering and search capabilities.

**Query Parameters:**
- `status` (optional): Filter by status - `active` or `inactive`
- `category_id` (optional): Filter by category ID
- `search` (optional): Search subcategories by name
- `per_page` (optional): Number of items per page (default: 15)

**Example Request:**
```bash
GET /api/subcategories?category_id=1&status=active&search=residential&per_page=10
```

**Example Response:**
```json
{
  "success": true,
  "message": "Subcategories retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Residential Construction",
        "status": "active",
        "category_id": 1,
        "category": {
          "id": 1,
          "name": "Construction",
          "status": "active"
        },
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 1,
    "last_page_url": "...",
    "links": [...],
    "next_page_url": null,
    "path": "...",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### 2. Get Active SubCategories
**GET** `/subcategories/active`

Returns all active subcategories with category information.

**Example Request:**
```bash
GET /api/subcategories/active
```

**Example Response:**
```json
{
  "success": true,
  "message": "Active subcategories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Residential Construction",
      "status": "active",
      "category_id": 1,
      "category": {
        "id": 1,
        "name": "Construction",
        "status": "active"
      },
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

### 3. Get Specific SubCategory
**GET** `/subcategories/{id}`

Returns a specific subcategory by ID with category information.

**Example Request:**
```bash
GET /api/subcategories/1
```

**Example Response:**
```json
{
  "success": true,
  "message": "Subcategory retrieved successfully",
  "data": {
    "id": 1,
    "name": "Residential Construction",
    "status": "active",
    "category_id": 1,
    "category": {
      "id": 1,
      "name": "Construction",
      "status": "active"
    },
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### 4. Get SubCategories by Category
**GET** `/categories/{category_id}/subcategories`

Returns subcategories for a specific category with optional filtering.

**Query Parameters:**
- `status` (optional): Filter by status - `active` or `inactive`
- `search` (optional): Search subcategories by name
- `per_page` (optional): Number of items per page (default: 15)

**Example Request:**
```bash
GET /api/categories/1/subcategories?status=active&search=residential
```

**Example Response:**
```json
{
  "success": true,
  "message": "Subcategories for category 'Construction' retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Residential Construction",
        "status": "active",
        "category_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 1,
    "last_page_url": "...",
    "links": [...],
    "next_page_url": null,
    "path": "...",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### 5. Get Active SubCategories by Category
**GET** `/categories/{category_id}/subcategories/active`

Returns active subcategories for a specific category.

**Example Request:**
```bash
GET /api/categories/1/subcategories/active
```

**Example Response:**
```json
{
  "success": true,
  "message": "Active subcategories for category 'Construction' retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Residential Construction",
      "status": "active",
      "category_id": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

## Error Responses

All endpoints return consistent error responses in the following format:

```json
{
  "success": false,
  "message": "Error description"
}
```

**Common HTTP Status Codes:**
- `200`: Success
- `400`: Bad Request
- `404`: Not Found
- `500`: Internal Server Error

## Data Models

### Category Model
```json
{
  "id": "integer",
  "name": "string",
  "status": "string (active|inactive)",
  "sub_categories_count": "integer (optional)",
  "sub_categories": "array (optional)",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### SubCategory Model
```json
{
  "id": "integer",
  "name": "string",
  "status": "string (active|inactive)",
  "category_id": "integer",
  "category": "object (optional)",
  "category_name": "string (optional)",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

## Usage Examples

### Frontend Integration Examples

**JavaScript/Fetch API:**
```javascript
// Get all active categories
fetch('/api/categories/active')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Categories:', data.data);
    }
  });

// Get subcategories for a specific category
fetch('/api/categories/1/subcategories?status=active')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Subcategories:', data.data);
    }
  });
```

**Axios:**
```javascript
// Search categories
axios.get('/api/categories', {
  params: {
    search: 'construction',
    status: 'active',
    with_subcategories_count: true
  }
})
.then(response => {
  if (response.data.success) {
    console.log('Categories:', response.data.data);
  }
});
```

### cURL Examples

```bash
# Get all categories
curl -X GET "http://localhost:8000/api/categories"

# Get active categories with subcategories count
curl -X GET "http://localhost:8000/api/categories/active"

# Get subcategories for category ID 1
curl -X GET "http://localhost:8000/api/categories/1/subcategories"

# Search subcategories
curl -X GET "http://localhost:8000/api/subcategories?search=residential&status=active"
```

## Notes

1. All endpoints are currently public and don't require authentication
2. Pagination is available on list endpoints with a default of 15 items per page
3. Search functionality supports partial matching on category and subcategory names
4. Status filtering supports 'active' and 'inactive' values
5. All timestamps are returned in ISO 8601 format
6. The API follows RESTful conventions and returns consistent response formats 