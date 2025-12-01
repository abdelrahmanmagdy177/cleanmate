# CleanMate Customer API Documentation

Base URL: `http://your-domain.com/api/customer` (or similar, depending on deployment)

## Authentication

### Register
Create a new customer account.

- **Endpoint**: `POST /register`
- **Auth Required**: No
- **Parameters**:
  - `name` (string, required)
  - `email` (string, required, email format)
  - `phone` (string, required)
  - `password` (string, required, min 8 chars)
  - `password_confirmation` (string, required, matching password)
- **Response**:
  - `201 Created`: Returns created user data and access token.

### Login
Authenticate an existing customer.

- **Endpoint**: `POST /login`
- **Auth Required**: No
- **Parameters**:
  - `login` (string, required) - Email or Phone
  - `password` (string, required)
- **Response**:
  - `200 OK`: Returns access token and user data.

### Logout
Invalidate the current access token.

- **Endpoint**: `POST /logout`
- **Auth Required**: Yes (Sanctum)
- **Response**:
  - `200 OK`: `{"message": "Logged out successfully"}`

---

## Profile

### Update Area
Update the customer's selected service area.

- **Endpoint**: `POST /area`
- **Auth Required**: Yes
- **Parameters**:
  - `area_id` (integer, required, exists in active areas)
- **Response**:
  - `200 OK`: `{"message": "Area updated successfully", "user": {...}}`

---

## Addresses

### List Addresses
Get all addresses for a customer.

- **Endpoint**: `GET /customer-addresses`
- **Auth Required**: Yes
- **Parameters**:
  - `customer_email` (string, required) - *Note: Currently requires email explicitly.*
- **Response**:
  - `200 OK`: `{"data": [...]}`

### Create Address
Add a new address.

- **Endpoint**: `POST /customer-addresses`
- **Auth Required**: Yes
- **Parameters**:
  - `customer_email` (string, required)
  - `name` (string, required) - e.g., "Home", "Work"
  - `title` (string, required) - e.g., "My Apartment"
  - `address_details` (string, required)
  - `is_default` (boolean, optional)
- **Response**:
  - `201 Created`: `{"message": "Address created successfully", "data": {...}}`

### Update Address
Update an existing address.

- **Endpoint**: `PUT /customer-addresses/{id}`
- **Auth Required**: Yes
- **Parameters**:
  - `name` (string, optional)
  - `title` (string, optional)
  - `address_details` (string, optional)
  - `is_default` (boolean, optional)
- **Response**:
  - `200 OK`: `{"message": "Address updated successfully", "data": {...}}`

### Delete Address
Remove an address.

- **Endpoint**: `DELETE /customer-addresses/{id}`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"message": "Address deleted successfully"}`

---

## Zones & Areas

### List Zones
Get all active zones with their active areas.

- **Endpoint**: `GET /zones`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"data": [ { "id": 1, "name": "Zone A", "areas": [...] }, ... ]}`

### List Areas
Get all active areas with their zones.

- **Endpoint**: `GET /areas`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"data": [ { "id": 1, "name": "Downtown", "zone": {...} }, ... ]}`

---

## Services

### List Services
Get available services for the selected area.

- **Endpoint**: `GET /services`
- **Auth Required**: Yes
- **Parameters**:
  - `area_id` (integer, optional) - If not provided, uses the user's profile area.
- **Response**:
  - `200 OK`: Returns list of services with variants and prices for the area.

### Show Service
Get details of a specific service.

- **Endpoint**: `GET /services/{id}`
- **Auth Required**: Yes
- **Parameters**:
  - `area_id` (integer, optional)
- **Response**:
  - `200 OK`: Returns service details.

---

## Timeslots

### Get Available Timeslots
Get valid timeslots for a specific date and area.

- **Endpoint**: `GET /timeslots`
- **Auth Required**: Yes
- **Parameters**:
  - `date` (date, required, YYYY-MM-DD, >= today)
  - `area_id` (integer, required)
- **Response**:
  - `200 OK`: `{"data": [ {"id": 1, "start_time": "10:00", "end_time": "12:00", "available": true}, ... ]}`

---

## Cart

### Get Cart
Get current cart items and total.

- **Endpoint**: `GET /cart`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"cart_items": [...], "total": 150.00}`

### Add to Cart
Add an item to the cart.

- **Endpoint**: `POST /cart`
- **Auth Required**: Yes
- **Parameters**:
  - `service_id` (integer, required)
  - `variant_id` (integer, required)
  - `quantity` (integer, required, min 1)
  - `customer_address_id` (integer, optional) - *Check if required by business logic*
- **Response**:
  - `201 Created`: `{"message": "Item added...", "data": {...}}`

### Update Cart Item
Update quantity or details of a cart item.

- **Endpoint**: `PUT /cart/{id}`
- **Auth Required**: Yes
- **Parameters**:
  - `quantity` (integer, optional)
  - ... other updateable fields
- **Response**:
  - `200 OK`: `{"message": "Cart item updated...", "data": {...}}`

### Remove from Cart
Remove a specific item.

- **Endpoint**: `DELETE /cart/{id}`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"message": "Item removed..."}`

### Clear Cart
Remove all items from the cart.

- **Endpoint**: `DELETE /cart`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"message": "Cart cleared...", "items_removed": 5}`

---

## Orders

### Create Order
Place a new order.

- **Endpoint**: `POST /orders`
- **Auth Required**: Yes
- **Parameters**:
  - `customer_name` (string, required)
  - `customer_email` (string, required)
  - `customer_phone` (string, required)
  - `service_id` (integer, required)
  - `variant_id` (integer, required)
  - `timeslot_id` (integer, required)
  - `customer_address_id` (integer, required)
  - `space` (integer, required, min 1)
  - `order_date` (date, required, YYYY-MM-DD)
  - `payment_method` (string, required, 'cash' or 'credit')
  - `notes` (string, optional)
- **Response**:
  - `201 Created`: `{"message": "Order created...", "data": {...}, "price_breakdown": {...}}`

### List Orders
Get orders filtered by classification.

- **Endpoint**: `GET /orders`
- **Auth Required**: Yes
- **Parameters**:
  - `classification` (string, required) - e.g., 'processing', 'finished', or specific status.
- **Response**:
  - `200 OK`: `{"data": [...]}`

### List Processing Orders
Shortcut for processing orders.

- **Endpoint**: `GET /orders/processing`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"data": [...]}`

### List Finished Orders
Shortcut for finished orders.

- **Endpoint**: `GET /orders/finished`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"data": [...]}`

### Show Order
Get details of a specific order.

- **Endpoint**: `GET /orders/{id}`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"data": {...}, "price_breakdown": {...}}`

---

## Notifications

### List Notifications
Get all notifications for the customer.

- **Endpoint**: `GET /notifications`
- **Auth Required**: Yes
- **Parameters**:
  - `customer_email` (string, required)
- **Response**:
  - `200 OK`: `{"data": [...], "unread_count": 5}`

### Mark as Read
Mark a single notification as read.

- **Endpoint**: `POST /notifications/{id}/read`
- **Auth Required**: Yes
- **Response**:
  - `200 OK`: `{"message": "Notification marked as read"}`

### Mark All as Read
Mark all notifications as read.

- **Endpoint**: `POST /notifications/read-all`
- **Auth Required**: Yes
- **Parameters**:
  - `customer_email` (string, required)
- **Response**:
  - `200 OK`: `{"message": "All notifications marked as read"}`
