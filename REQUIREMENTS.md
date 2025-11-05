# API Gestión de Usuarios

**Goal:**
Desarrollar una API completa de Gestión de Usuarios utilizando PHP (Laravel) y MySQL, cumpliendo los siguientes requisitos funcionales y técnicos.

**Requerimientos del Proyecto:**
1. CRUD de Usuarios
- Crear, leer, actualizar y eliminar usuarios.
- Los campos del usuario deben incluir: id, nombre, email, password (hasheado), fecha_registro.

2. Autenticación JWT

- Implementar autenticación con JSON Web Token (JWT).
- El token debe expirar cada 5 minutos y permitir refrescamiento mediante un endpoint seguro.
- Solo los usuarios autenticados pueden acceder a las rutas protegidas (por ejemplo, las de CRUD).

3. Estadísticas de Usuarios

- Crear endpoints que devuelvan estadísticas de usuarios registrados:
    - Por día.
    - Por semana.
    - Por mes.
- Las estadísticas deben basarse en el campo fecha_registro de la base de datos.

4. Base de Datos

- Utilizar MySQL.
- Crear migraciones y seeders iniciales para pruebas.

**Requisitos Técnicos:**
- Framework: Laravel (última versión estable)
- Base de datos: MySQL
- Autenticación: JWT (via paquete firebase/php-jwt)
- Documentación: generar Swagger/OpenAPI para describir los endpoints.
- Buenas prácticas: controladores RESTful, middlewares, validaciones de entrada, manejo de excepciones, y respuesta en formato JSON.

**Entregables esperados:**

- Estructura completa del proyecto Laravel con rutas, controladores, modelos, migraciones y seeders.
- Implementación funcional del flujo de autenticación JWT.
- Endpoints REST con validaciones y respuestas JSON.
- Código limpio y comentado.
- Archivo README.md explicando cómo iniciar el proyecto y probar los endpoints.
