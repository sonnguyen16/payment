# API Documentation

## Authentication

All API requests require authentication using Laravel Sanctum or session-based auth.

## Endpoints

### Payment Requests

#### List Payment Requests
```http
GET /payment-requests
```

**Query Parameters:**
- `status` - Filter by status
- `priority` - Filter by priority
- `search` - Search in description

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "type": "advance",
      "amount": "5000000.00",
      "status": "pending_department_head",
      "created_at": "2025-01-01 10:00:00"
    }
  ]
}
```

#### Create Payment Request
```http
POST /payment-requests
```

**Body:**
```json
{
  "type": "advance",
  "amount": 5000000,
  "description": "Tạm ứng công tác",
  "reason": "Đi công tác Hà Nội",
  "expected_date": "2025-01-15",
  "priority": "normal",
  "project_id": 1
}
```

#### Submit for Approval
```http
POST /payment-requests/{id}/submit
```

### Approvals

#### Approve Request
```http
POST /approvals/{id}/approve
```

**Body:**
```json
{
  "note": "Approved"
}
```

#### Reject Request
```http
POST /approvals/{id}/reject
```

**Body:**
```json
{
  "reason": "Không đủ ngân sách"
}
```

### Documents

#### Upload Document
```http
POST /payment-requests/{id}/documents
```

**Form Data:**
- `file` - File to upload (max 10MB)
- `type` - Document type (invoice, receipt, contract, other)

#### Download Document
```http
GET /documents/{id}
```

### Projects

#### List Projects
```http
GET /projects
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "code": "PRJ001",
      "name": "Dự án A",
      "budget": "100000000.00",
      "spent": "50000000.00",
      "remaining_budget": "50000000.00"
    }
  ]
}
```

### Notifications

#### Get Unread Notifications
```http
GET /notifications/unread
```

#### Mark as Read
```http
POST /notifications/{id}/read
```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "amount": ["Vui lòng nhập số tiền"]
  }
}
```

### 500 Server Error
```json
{
  "message": "Server Error"
}
```
