# Authentication API

Base URL during local development:

```text
http://127.0.0.1:8088/api
```

Use `http://10.0.2.2:8088/api` from the Android emulator, or
`http://YOUR_COMPUTER_LAN_IP:8088/api` from a physical phone.

## Signup

```http
POST /auth/signup
Content-Type: application/json
Accept: application/json
```

```json
{
  "name": "Ada Lovelace",
  "email": "ada@example.com",
  "phone": "+2348012345678",
  "password": "password123",
  "password_confirmation": "password123"
}
```

The response includes `debug_otp` only outside production when
`AUTH_EXPOSE_DEBUG_OTP=true`.

## Verify OTP

```http
POST /auth/verify-otp
```

```json
{
  "identifier": "ada@example.com",
  "otp": "123456"
}
```

Save `data.token.access_token` from the response.

## Login

```http
POST /auth/login
```

```json
{
  "identifier": "ada@example.com",
  "password": "password123"
}
```

`identifier` can be the user's email or phone number.

## Authenticated Requests

```http
Authorization: Bearer <access_token>
Accept: application/json
```

Available endpoints:

```text
GET  /auth/me
POST /auth/logout
POST /auth/refresh
```

The Monnify payment endpoints also use the same JWT bearer token.

## Forgot Password

```http
POST /auth/forgot-password
```

```json
{
  "identifier": "ada@example.com"
}
```

```http
POST /auth/reset-password
```

```json
{
  "identifier": "ada@example.com",
  "otp": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```
