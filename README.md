# API Endpoints Documentation

## 1. Create User Signup

### Endpoint:
- **Method:** POST  
- **URL:** `https://api-compass.cainethings.com/user/create`

### Purpose:
This function registers a new user in the system and triggers an OTP verification process. It ensures that either an email or phone number is provided to send the OTP, allowing the user to complete the signup process.

### Use Case:
A new user signs up for the service by providing their email, phone number, and password. The system generates an OTP and sends it for verification.

### Request Payload:
```json
{
  "email": "user@example.com",  // Optional if phone_number is provided
  "phone_number": "1234567890",  // Optional if email is provided
  "password": "securepassword",
  "first_name": "John",
  "last_name": "Doe"
}
```

### Responses:
- **200 OK:** User created successfully, OTP sent.
- **400 Bad Request:** Missing required fields.
- **500 Internal Server Error:** Database error.

---

## 2. Verify User Signup

### Endpoint:
- **Method:** POST  
- **URL:** `https://api-compass.cainethings.com/user/create/verify`

### Purpose:
Confirms user registration by validating the OTP.

### Use Case:
A user enters the OTP received via email or SMS to complete the signup process.

### Request Payload:
```json
{
  "otp": "123456",
  "email": "user@example.com",  // Optional if session data is missing
  "phone_number": "1234567890"  // Optional if session data is missing
}
```

### Responses:
- **200 OK:** User verified successfully.
- **400 Bad Request:** OTP is required.
- **400 Bad Request:** User is already verified or does not exist.
- **404 Not Found:** User not found.
- **401 Unauthorized:** Invalid OTP.
- **500 Internal Server Error:** Database error.


---


## 3. Resend OTP for Verification

### Endpoint:
- **Method:** POST  
- **URL:** `https://api-compass.cainethings.com/user/create/resend-otp`

### Purpose:
Resends the OTP if the user has not yet completed verification. Ensures that only unverified users receive OTPs.

### Use Case:
A user who has signed up but has not received or lost the OTP requests a resend.

### Request Payload:
```json
{
  "email": "user@example.com",  // Optional if phone_number is provided
  "phone_number": "1234567890"  // Optional if email is provided
}
```

### Responses:
- **200 OK:** OTP sent successfully.
- **400 Bad Request:** Missing required fields.
- **404 Not Found:** User not found.
- **400 Bad Request:** User is already verified.
- **500 Internal Server Error:** Database error.
---

## 4. User Login

### Endpoint:
- **Method:** POST  
- **URL:** `https://api-compass.cainethings.com/user/login`

### Purpose:
Authenticates users and starts a session.

### Use Case:
A registered user logs in by providing their email and password.

### Request Payload:
```json
{
  "email": "user@example.com",  // Required
  "password": "securepassword"
}
```

### Responses:
- **200 OK:** Login successful, returns session token.
- **401 Unauthorized:** Invalid credentials.
- **404 Not Found:** User not found.
- **500 Internal Server Error:** Database error.

---

## 5. User Logout

### Endpoint:
- **Method:** GET  
- **URL:** `https://api-compass.cainethings.com/user/logout`

### Purpose:
Ends the user session and logs them out.

### Use Case:
A logged-in user wishes to log out from the system.

### Responses:
- **200 OK:** Successfully logged out.
- **401 Unauthorized:** No active session found.

---

## 6. Forgot Password - Send OTP

### Endpoint:
- **Method:** POST  
- **URL:** `https://api-compass.cainethings.com/user/forgot-password`

### Purpose:
Sends an OTP to reset the user's password.

### Use Case:
A user forgets their password and requests a reset via OTP.

### Request Payload:
```json
{
  "email": "user@example.com"  // Required
}
```

### Responses:
- **200 OK:** OTP sent successfully.
- **404 Not Found:** User not found.
- **500 Internal Server Error:** Database error.

---

## 7. Reset Password

### Endpoint:
- **Method:** POST  
- **URL:** `https://api-compass.cainethings.com/user/reset-password`

### Purpose:
Verifies the OTP and allows the user to set a new password.

### Use Case:
A user who has forgotten their password enters the OTP and sets a new password.

### Request Payload:
```json
{
  "otp": "123456",
  "email": "user@example.com",  // Required
  "new_password": "newsecurepassword"
}
```

### Responses:
- **200 OK:** Password reset successfully.
- **400 Bad Request:** Missing required fields.
- **401 Unauthorized:** Invalid OTP.
- **404 Not Found:** User not found.
- **500 Internal Server Error:** Database error.

---

## 8. Get Session Details

### Endpoint:
- **Method:** GET  
- **URL:** `https://api-compass.cainethings.com/user/session`

### Purpose:
Retrieves details about the current user session.

### Use Case:
A user wants to check their login status and details.

### Responses:
- **200 OK:** Returns user session data.
- **401 Unauthorized:** No active session found.
- **500 Internal Server Error:** Database error.

