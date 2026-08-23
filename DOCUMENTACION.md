# 📄 Documentación — Sistema de Renta de Carros XYZ

## Proyecto Completo de Programación en Web

**Materia:** Programación en Web  
**Autor:** Moisés Antonio Balderas López  
**Fecha:** 23 de agosto de 2026

---

## 📑 Índice

1. [Descripción General](#1-descripción-general)
2. [Funcionalidades Implementadas](#2-funcionalidades-implementadas)
3. [Estructura de Archivos](#3-estructura-de-archivos)
4. [Base de Datos](#4-base-de-datos)
5. [Búsqueda Dinámica](#5-búsqueda-dinámica)
6. [Autenticación de Usuarios](#6-autenticación-de-usuarios)
7. [Verificación de Sesión para Reservas](#7-verificación-de-sesión-para-reservas)
8. [Envío de Correo con PHPMailer](#8-envío-de-correo-con-phpmailer)
9. [Redirección de Usuarios No Autenticados](#9-redirección-de-usuarios-no-autenticados)
10. [Panel de Administración (CRUD)](#10-panel-de-administración-crud)
11. [Guía de Instalación](#11-guía-de-instalación)
12. [Tecnologías Utilizadas](#12-tecnologías-utilizadas)

---

## 1. Descripción General

Sistema web completo de **Renta de Carros XYZ** que permite a los usuarios explorar vehículos disponibles, buscar por marca o modelo en tiempo real, registrarse, iniciar sesión y realizar reservas de vehículos. El sistema incluye verificación de sesión, envío de correos de confirmación con PHPMailer, redirección de usuarios no autenticados y un panel de administración con CRUD completo usando DataTables.

---

## 2. Funcionalidades Implementadas

| Funcionalidad | Descripción | Estado |
|---------------|-------------|--------|
| Estructura del sitio | HTML5 semántico + CSS responsive con tema oscuro | ✅ |
| Búsqueda dinámica | Filtro en tiempo real por marca y modelo sin recargar | ✅ |
| Autenticación | Registro e inicio de sesión con sesiones PHP | ✅ |
| Verificación de sesión | Solo usuarios autenticados pueden reservar | ✅ |
| Envío de correo | PHPMailer con SMTP de Gmail | ✅ |
| Redirección | Usuarios no autenticados redirigidos a login | ✅ |
| Panel admin CRUD | DataTables con crear, editar y eliminar usuarios | ✅ |
| Cerrar sesión | Destruir sesión y redirigir al inicio | ✅ |

---

## 3. Estructura de Archivos

```
Programacion-en-web/
├── index.html              → Página principal
├── conocenos.html          → Información de la empresa
├── contacto.html           → Formulario de contacto
├── vehiculos.php           → Catálogo de vehículos + reserva (con sesión PHP)
├── vehiculos.js            → Lógica JS: búsqueda, filtros, selección
├── vehiculos.css           → Estilos para vehículos y panel admin
├── login.php               → Inicio de sesión con redirección por tipo
├── registro.php            → Registro de nuevos usuarios
├── logout.php              → Cerrar sesión
├── admin.php               → Panel de administración (CRUD + DataTables)
├── admin_acciones.php      → Endpoints para acciones CRUD
├── procesar_reserva.php    → Procesa reservas y envía correo
├── enviar_correo.php       → Configuración de PHPMailer
├── conexion.php            → Conexión a MySQL
├── renta_carros.sql        → Schema de la base de datos
├── css/
│   └── style.css           → Estilos globales
├── images/
│   ├── index/              → Imágenes de la página principal
│   ├── conocenos/          → Imágenes de conócenos
│   └── vehiculos/          → Imágenes de los vehículos
├── phpmailer/
│   ├── Exception.php       → PHPMailer Exception
│   ├── PHPMailer.php       → PHPMailer clase principal
│   └── SMTP.php            → PHPMailer SMTP
└── DOCUMENTACION.md        → Este archivo
```

---

## 4. Base de Datos

### Nombre: `renta_carros`

### Tabla: `usuarios`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| ID | INT AUTO_INCREMENT | Identificador único |
| Correo | VARCHAR(255) | Correo electrónico |
| Contraseña | VARCHAR(10) | Contraseña del usuario |
| Rol | VARCHAR(50) | Rol en su empresa |
| Nombre | VARCHAR(255) | Nombre completo |
| Tipo | ENUM('usuario','admin') | Tipo de usuario en el sistema |

### Tabla: `vehiculos`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| ID | INT AUTO_INCREMENT | Identificador único |
| Marca | VARCHAR(100) | Marca del vehículo |
| Modelo | VARCHAR(100) | Modelo del vehículo |
| Anio | INT(4) | Año del vehículo |
| Precio | DECIMAL(10,2) | Precio por día en MXN |
| Imagen | VARCHAR(255) | Ruta de la imagen |

### Tabla: `reservas`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| ID | INT AUTO_INCREMENT | Identificador único |
| UsuarioID | INT (FK) | Referencia al usuario |
| VehiculoID | INT (FK) | Referencia al vehículo |
| FechaInicio | DATE | Fecha de inicio de la renta |
| FechaFin | DATE | Fecha de fin de la renta |
| Dias | INT | Número de días |
| CostoTotal | DECIMAL(10,2) | Costo total calculado |
| FechaReserva | DATETIME | Fecha y hora de la reserva |

---

## 5. Búsqueda Dinámica

### Archivos involucrados:
- `vehiculos.php` — Controles HTML (input + select)
- `vehiculos.js` — Lógica de filtrado
- `vehiculos.css` — Estilos

### Características:
- 🔍 **Búsqueda en tiempo real** al escribir (evento `input`)
- 🏷️ **Filtro por marca** con menú desplegable (evento `change`)
- 🔄 **Filtros combinados** — texto + marca funcionan juntos
- 📱 **Sin recarga de página** — JavaScript puro del lado del cliente
- ❌ **Mensaje "sin resultados"** cuando no hay coincidencias

### Función principal:
```javascript
function filtrarVehiculos() {
    var textoBusqueda = document.getElementById("busqueda-vehiculo").value.toLowerCase().trim();
    var marcaSeleccionada = document.getElementById("filtro-marca").value;
    // Filtra por coincidencia parcial en marca/modelo + marca seleccionada
}
```

---

## 6. Autenticación de Usuarios

### Registro (`registro.php`):
- Formulario con campos: Nombre, Correo, Contraseña, Rol
- Validación de campos vacíos
- Verificación de correo duplicado
- Tipo de usuario se asigna como `'usuario'` por defecto
- Prepared statements para prevenir SQL injection

### Login (`login.php`):
- Verificación de credenciales contra la base de datos
- Creación de sesión PHP con datos del usuario
- Variables de sesión: `usuario_id`, `usuario_nombre`, `usuario_rol`, `usuario_correo`, `usuario_tipo`
- Redirección automática según tipo:
  - **Admin** → `admin.php`
  - **Usuario** → `vehiculos.php`

### Cerrar sesión (`logout.php`):
- Destruye todas las variables de sesión
- Redirige a `index.html`

---

## 7. Verificación de Sesión para Reservas

### Archivo: `vehiculos.php`

**Funcionamiento:**
1. Al cargar la página, se verifica si existe `$_SESSION["usuario_id"]`
2. Si **hay sesión**: se muestra el formulario completo de reserva
3. Si **no hay sesión**: se muestra un mensaje indicando que debe iniciar sesión, con enlaces a login y registro
4. Variable JavaScript `sesionActiva` se inyecta desde PHP para validación del lado del cliente

### Archivo: `procesar_reserva.php`

**Proceso de reserva:**
1. Verifica sesión activa (doble verificación: cliente + servidor)
2. Valida datos del formulario (vehículo, fechas)
3. Calcula días y costo total
4. Guarda la reserva en la tabla `reservas`
5. Envía correo de confirmación con PHPMailer
6. Redirige de vuelta a `vehiculos.php` con mensaje de éxito

---

## 8. Envío de Correo con PHPMailer

### Archivo: `enviar_correo.php`

**Configuración:**
- Servidor SMTP: `smtp.gmail.com`
- Puerto: `587`
- Encriptación: `STARTTLS`
- Autenticación: Correo de Gmail + Contraseña de aplicación

**Para configurar el correo:**
1. Abrir `enviar_correo.php`
2. Reemplazar `'TU_CORREO@gmail.com'` con tu correo de Gmail
3. Reemplazar `'TU_CONTRASENA_APP'` con tu contraseña de aplicación
4. Para obtener la contraseña de aplicación: https://myaccount.google.com/apppasswords

**Contenido del correo:**
- Formato HTML con diseño que coincide con el tema del sitio
- Detalles: vehículo, fechas, días, precio por día, costo total
- Texto alternativo para clientes que no soportan HTML

---

## 9. Redirección de Usuarios No Autenticados

### Flujo de redirección:

1. Usuario sin sesión intenta reservar en `vehiculos.php`
2. El formulario no se muestra, solo un mensaje con enlace a login
3. Si intenta acceder directamente a `procesar_reserva.php` sin sesión:
   - Se redirige a `login.php?redireccion=vehiculos`
4. En `login.php`, se muestra mensaje: "Debes iniciar sesión para completar una reserva"
5. Después de hacer login exitoso, se redirige automáticamente a `vehiculos.php`

---

## 10. Panel de Administración (CRUD)

### Archivo: `admin.php`

**Acceso:** Solo usuarios con `Tipo = 'admin'`

**Funcionalidades:**

| Acción | Descripción |
|--------|-------------|
| **Crear** | Formulario para agregar nuevos usuarios con todos los campos |
| **Leer** | Tabla DataTables con búsqueda, paginación y ordenamiento |
| **Actualizar** | Modal de edición con todos los campos del usuario |
| **Eliminar** | Confirmación antes de eliminar, protección contra auto-eliminación |

**DataTables:**
- Librería jQuery DataTables para tabla interactiva
- Búsqueda integrada en la tabla
- Paginación automática
- Ordenamiento por columnas
- Traducido al español (es-MX)
- Tema oscuro personalizado con CSS

**Tabla de reservas:**
- Vista de todas las reservas realizadas
- Incluye datos del usuario, vehículo, fechas y costos
- DataTables con las mismas funcionalidades

### Archivo: `admin_acciones.php`
- Maneja las acciones POST (crear, editar) y GET (eliminar)
- Validación de permisos de administrador
- Prepared statements para seguridad
- Mensajes de confirmación/error

**Usuario administrador de prueba:**
- Correo: `admin@rentacarrosxyz.com`
- Contraseña: `admin123`

---

## 11. Guía de Instalación

### Requisitos:
- XAMPP con Apache y MySQL
- PHP 7.4 o superior

### Pasos:

1. **Copiar archivos** a `C:\xampp\htdocs\Programacion-en-web\`

2. **Iniciar XAMPP** — Activar Apache y MySQL

3. **Crear la base de datos:**
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Importar el archivo `renta_carros.sql`
   - O ejecutar las consultas SQL manualmente

4. **Verificar conexión:**
   - Abrir `conexion.php` y verificar que el puerto sea el correcto (por defecto `3307`)

5. **Configurar correo (opcional):**
   - Abrir `enviar_correo.php`
   - Reemplazar las credenciales SMTP con tu correo de Gmail y contraseña de aplicación

6. **Acceder al sistema:**
   - URL: `http://localhost/Programacion-en-web/index.html`

---

## 12. Tecnologías Utilizadas

| Tecnología | Uso |
|------------|-----|
| **HTML5** | Estructura semántica de todas las páginas |
| **CSS3** | Diseño responsive, tema oscuro, animaciones |
| **JavaScript** | Búsqueda dinámica, filtrado, validación, interactividad |
| **PHP** | Backend: sesiones, autenticación, CRUD, procesamiento |
| **MySQL** | Base de datos: usuarios, vehículos, reservas |
| **PHPMailer** | Envío de correos de confirmación por SMTP |
| **jQuery** | Requerido por DataTables |
| **DataTables** | Tabla interactiva con búsqueda, paginación y ordenamiento |

---

> **Nota:** El sistema está diseñado para ejecutarse en un entorno local con XAMPP. Para producción, se recomienda implementar hashing de contraseñas, HTTPS y validación adicional del lado del servidor.
