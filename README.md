# API Gestión de Usuarios

API REST completa para gestión de usuarios desarrollada con Laravel 12, incluyendo autenticación JWT, CRUD de usuarios y estadísticas de registro.

## Documentación Swagger

La documentación interactiva está disponible en:
**https://fsj29-api-users.fqstudio.dev/api/documentation**

### Credenciales para Sandbox

Para probar los endpoints en Swagger, usa las siguientes credenciales:

- **Email:** `test@example.com`
- **Password:** `password123`

**Nota:** Después de hacer login, copia el `access_token` y úsalo en el botón "Authorize" de Swagger para autenticar las peticiones protegidas.

## Endpoints

### Autenticación

#### POST `/api/auth/login`
Autentica un usuario y retorna un par de tokens JWT (access_token y refresh_token).

**Request:**
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "name": "Usuario Test",
      "email": "test@example.com",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}
```

#### POST `/api/auth/refresh`
Renueva el access token usando el refresh token. Devuelve un nuevo par de tokens.

**Request:**
```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

### Usuarios (Requieren autenticación JWT)

**Nota:** Todas las rutas requieren el `access_token` en el header `Authorization: Bearer {access_token}`.

#### GET `/api/users`
Lista todos los usuarios registrados.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Usuario Test",
      "email": "test@example.com",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### GET `/api/users/{id}`
Obtiene un usuario específico por ID.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Usuario Test",
    "email": "test@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### POST `/api/users`
Crea un nuevo usuario.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123!"
}
```

**Validación de contraseña:**
- Mínimo 8 caracteres
- Al menos una letra minúscula (a-z)
- Al menos una letra mayúscula (A-Z)
- Al menos un número (0-9)
- Al menos un carácter especial (@$!%*#?&)

**Response:**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 2,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### PUT `/api/users/{id}`
Actualiza un usuario existente. Todos los campos son opcionales.

**Request:**
```json
{
  "name": "John Updated",
  "email": "john.updated@example.com",
  "password": "NewPassword123!"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 2,
    "name": "John Updated",
    "email": "john.updated@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### DELETE `/api/users/{id}`
Elimina un usuario.

**Response:**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

### Estadísticas (Requieren autenticación JWT)

#### GET `/api/statistics/daily`
Obtiene estadísticas diarias de usuarios registrados.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "date": "2024-01-01",
      "registered_users": 5
    },
    {
      "date": "2024-01-02",
      "registered_users": 3
    }
  ]
}
```

#### GET `/api/statistics/weekly`
Obtiene estadísticas semanales de usuarios registrados.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "year": 2024,
      "week": 1,
      "from_date": "2024-01-01",
      "to_date": "2024-01-07",
      "registered_users": 15
    }
  ]
}
```

#### GET `/api/statistics/monthly`
Obtiene estadísticas mensuales de usuarios registrados.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "year": 2024,
      "month": 1,
      "period": "2024-01",
      "registered_users": 45
    }
  ]
}
```

## Notas Importantes

- **Access Token**: Expira después de 5 minutos. Se usa para autenticarse en todas las peticiones protegidas.
- **Refresh Token**: Expira después de 7 días. Se usa solo para renovar el access token.
- Usa el endpoint `/api/auth/refresh` con el `refresh_token` para obtener un nuevo par de tokens.
- Todos los endpoints excepto `/api/auth/login` y `/api/auth/refresh` requieren el `access_token` en el header `Authorization: Bearer {access_token}`.
- El patrón implementado sigue OAuth 2.0 con access token y refresh token separados.

## Tecnologías

- Laravel 12
- PHP 8.2+
- MySQL
- Firebase JWT (firebase/php-jwt)
- L5-Swagger (darkaonline/l5-swagger)
