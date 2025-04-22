# Herd ENT - API Documentation

## Overview

Herd ENT provides a RESTful API that allows developers to interact with the system programmatically. The API uses JSON for data exchange and follows RESTful conventions.

## Authentication

All API endpoints require authentication using Laravel Sanctum. To authenticate:

1. Obtain an API token by sending a POST request to `/api/login` with your credentials.
2. Include the token in the `Authorization` header of all subsequent requests:
   ```
   Authorization: Bearer {your_api_token}
   ```

### Login Endpoint

```
POST /api/login
```

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "your_password"
}
```

**Response:**
```json
{
    "token": "1|a1b2c3d4e5f6g7h8i9j0...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com"
    }
}
```

### Logout Endpoint

```
POST /api/logout
```

**Headers:**
```
Authorization: Bearer {your_api_token}
```

**Response:**
```json
{
    "message": "Successfully logged out"
}
```

## API Endpoints

### Patients

#### List Patients

```
GET /api/patients
```

**Query Parameters:**
- `per_page`: Number of results per page (default: 15)
- `page`: Page number (default: 1)
- `search`: Search term for patient name or ID
- `sort_by`: Field to sort by (default: 'created_at')
- `sort_dir`: Sort direction ('asc' or 'desc', default: 'desc')

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Smith",
            "email": "john@example.com",
            "phone": "123-456-7890",
            "date_of_birth": "1980-01-01",
            "gender": "male",
            "address": "123 Main St",
            "city": "Anytown",
            "state": "CA",
            "zip": "12345",
            "created_at": "2023-01-01T00:00:00.000000Z",
            "updated_at": "2023-01-01T00:00:00.000000Z"
        },
        // More patients...
    ],
    "links": {
        "first": "http://localhost:8000/api/patients?page=1",
        "last": "http://localhost:8000/api/patients?page=10",
        "prev": null,
        "next": "http://localhost:8000/api/patients?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "path": "http://localhost:8000/api/patients",
        "per_page": 15,
        "to": 15,
        "total": 150
    }
}
```

#### Get Patient

```
GET /api/patients/{id}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "John Smith",
        "email": "john@example.com",
        "phone": "123-456-7890",
        "date_of_birth": "1980-01-01",
        "gender": "male",
        "address": "123 Main St",
        "city": "Anytown",
        "state": "CA",
        "zip": "12345",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

#### Create Patient

```
POST /api/patients
```

**Request Body:**
```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "phone": "123-456-7890",
    "date_of_birth": "1985-05-15",
    "gender": "female",
    "address": "456 Oak St",
    "city": "Somewhere",
    "state": "NY",
    "zip": "67890"
}
```

**Response:**
```json
{
    "data": {
        "id": 2,
        "name": "Jane Doe",
        "email": "jane@example.com",
        "phone": "123-456-7890",
        "date_of_birth": "1985-05-15",
        "gender": "female",
        "address": "456 Oak St",
        "city": "Somewhere",
        "state": "NY",
        "zip": "67890",
        "created_at": "2023-01-02T00:00:00.000000Z",
        "updated_at": "2023-01-02T00:00:00.000000Z"
    },
    "message": "Patient created successfully"
}
```

#### Update Patient

```
PUT /api/patients/{id}
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
    "data": {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane.smith@example.com",
        "phone": "123-456-7890",
        "date_of_birth": "1985-05-15",
        "gender": "female",
        "address": "456 Oak St",
        "city": "Somewhere",
        "state": "NY",
        "zip": "67890",
        "created_at": "2023-01-02T00:00:00.000000Z",
        "updated_at": "2023-01-03T00:00:00.000000Z"
    },
    "message": "Patient updated successfully"
}
```

#### Delete Patient

```
DELETE /api/patients/{id}
```

**Response:**
```json
{
    "message": "Patient deleted successfully"
}
```

### Appointments

#### List Appointments

```
GET /api/appointments
```

**Query Parameters:**
- `per_page`: Number of results per page (default: 15)
- `page`: Page number (default: 1)
- `date`: Filter by date (format: YYYY-MM-DD)
- `status`: Filter by status (scheduled, completed, cancelled)
- `patient_id`: Filter by patient ID
- `clinician_id`: Filter by clinician ID

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "patient_id": 1,
            "clinician_id": 3,
            "title": "Initial Consultation",
            "start_time": "2023-01-15T09:00:00.000000Z",
            "end_time": "2023-01-15T09:30:00.000000Z",
            "status": "completed",
            "notes": "Patient complained of sore throat",
            "created_at": "2023-01-10T00:00:00.000000Z",
            "updated_at": "2023-01-15T10:00:00.000000Z",
            "patient": {
                "id": 1,
                "name": "John Smith"
            },
            "clinician": {
                "id": 3,
                "name": "Dr. Jane Wilson"
            }
        },
        // More appointments...
    ],
    "links": {
        "first": "http://localhost:8000/api/appointments?page=1",
        "last": "http://localhost:8000/api/appointments?page=5",
        "prev": null,
        "next": "http://localhost:8000/api/appointments?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "path": "http://localhost:8000/api/appointments",
        "per_page": 15,
        "to": 15,
        "total": 75
    }
}
```

Similar endpoints exist for:
- `/api/appointments/{id}` (GET)
- `/api/appointments` (POST)
- `/api/appointments/{id}` (PUT)
- `/api/appointments/{id}` (DELETE)

### Invoices

#### List Invoices

```
GET /api/invoices
```

**Query Parameters:**
- `per_page`: Number of results per page (default: 15)
- `page`: Page number (default: 1)
- `patient_id`: Filter by patient ID
- `status`: Filter by status (draft, sent, paid, cancelled)
- `date_from`: Filter by date range start (format: YYYY-MM-DD)
- `date_to`: Filter by date range end (format: YYYY-MM-DD)

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "patient_id": 1,
            "encounter_id": 5,
            "invoice_number": "INV-2023-001",
            "amount": "150.00",
            "status": "paid",
            "due_date": "2023-02-15",
            "paid_date": "2023-02-10",
            "created_at": "2023-01-15T10:00:00.000000Z",
            "updated_at": "2023-02-10T14:30:00.000000Z",
            "patient": {
                "id": 1,
                "name": "John Smith"
            },
            "items": [
                {
                    "id": 1,
                    "invoice_id": 1,
                    "billing_code_id": 101,
                    "description": "Office Visit - Established Patient, Level 2",
                    "quantity": 1,
                    "unit_price": "75.00",
                    "total": "75.00",
                    "billing_code": {
                        "id": 101,
                        "code": "99212"
                    }
                },
                {
                    "id": 2,
                    "invoice_id": 1,
                    "billing_code_id": 203,
                    "description": "Strep Test",
                    "quantity": 1,
                    "unit_price": "75.00",
                    "total": "75.00",
                    "billing_code": {
                        "id": 203,
                        "code": "87880"
                    }
                }
            ]
        },
        // More invoices...
    ],
    // Pagination links and meta...
}
```

Similar endpoints exist for other invoice operations.

## Error Handling

All API endpoints return appropriate HTTP status codes:

- `200 OK`: Request succeeded
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `500 Server Error`: Internal server error

For validation errors, the response includes details about which fields failed validation:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "email": [
            "The email field must be a valid email address."
        ]
    }
}
```

## Rate Limiting

API requests are subject to rate limiting of 60 requests per minute per user. When the limit is exceeded, a `429 Too Many Requests` response is returned.

## Versioning

The current API version is v1. All endpoints are prefixed with `/api/v1/` for forward compatibility, although the `/api/` prefix currently works as well.

## Documentation Updates

This API documentation is current as of January 2024. Check for updates in the repository as new features are added. 