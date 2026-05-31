# GestorPro - Guía de instalación paso a paso

Sistema de gestión de inventario y ventas.
Stack: JavaScript + PHP + MySQL + Apache (XAMPP)

---

## PASO 1 — Instalar XAMPP

1. Descarga XAMPP desde: https://www.apachefriends.org/es/index.html
2. Instálalo en la ruta por defecto (C:\xampp en Windows)
3. Abre el **Panel de Control de XAMPP**
4. Arranca los módulos **Apache** y **MySQL** pulsando "Start"
   - Apache: puerto 80 (servidor web)
   - MySQL: puerto 3306 (base de datos)

---

## PASO 2 — Copiar los archivos del proyecto

1. Navega a la carpeta: `C:\xampp\htdocs\`
2. Crea una carpeta nueva llamada `inventario`
3. Copia TODOS los archivos del proyecto dentro:

```
C:\xampp\htdocs\inventario\
├── index.html
├── .htaccess
├── database.sql
└── api\
    ├── config.php
    ├── auth.php
    ├── dashboard.php
    ├── productos.php
    ├── ventas.php
    ├── clientes.php
    └── usuarios.php
```

---

## PASO 3 — Crear la base de datos

1. Abre el navegador y ve a: http://localhost/phpmyadmin
2. En el menú de la izquierda, haz clic en **"Nueva"** (o "New")
3. Escribe el nombre: `inventario_db` y pulsa **Crear**
4. Con la base de datos seleccionada, haz clic en la pestaña **SQL**
5. Abre el archivo `database.sql` con el Bloc de notas
6. Copia todo el contenido y pégalo en el cuadro de texto de phpMyAdmin
7. Pulsa el botón **Continuar**
8. Verás las tablas creadas en el panel izquierdo ✓

---

## PASO 4 — Configurar la conexión a MySQL

Abre el archivo `api/config.php` con cualquier editor de texto y ajusta:

```php
define('DB_USER', 'root');   // Usuario de MySQL (por defecto 'root' en XAMPP)
define('DB_PASS', '');       // Contraseña (por defecto vacía en XAMPP)
```

> **Nota:** En XAMPP la instalación por defecto tiene usuario `root` y contraseña vacía.
> Si tú configuraste una contraseña, ponla aquí.

---

## PASO 5 — Abrir la aplicación

1. Abre el navegador
2. Ve a: **http://localhost/inventario/**
3. ¡Ya está funcionando!

---

## USUARIOS DE PRUEBA

| Rol       | Email                 | Contraseña |
|-----------|-----------------------|------------|
| Admin     | admin@tienda.com      | 1234       |
| Vendedor  | carlos@tienda.com     | 1234       |
| Almacén   | laura@tienda.com      | 1234       |

### Diferencias por rol:
- **Admin**: acceso completo, incluyendo gestión de usuarios
- **Vendedor**: puede ver dashboard, gestionar productos, hacer ventas y ver clientes
- **Almacén**: mismo que vendedor, sin acceso al módulo de usuarios

---

## SOLUCIÓN DE PROBLEMAS

### "No puedo conectar a la base de datos"
- Verifica que MySQL esté corriendo en el panel de XAMPP
- Revisa usuario y contraseña en `api/config.php`
- Asegúrate de que la base de datos `inventario_db` existe en phpMyAdmin

### "Página en blanco o error 404"
- Comprueba que Apache esté corriendo
- Verifica que los archivos están en `C:\xampp\htdocs\inventario\`
- Prueba a acceder directamente a: http://localhost/inventario/index.html

### "Error al hacer login"
- Comprueba en phpMyAdmin que la tabla `usuarios` tiene datos
- Si no tiene datos, vuelve a ejecutar el archivo `database.sql`

### El módulo "mod_rewrite" no está activo
- En XAMPP, abre `C:\xampp\apache\conf\httpd.conf`
- Busca `#LoadModule rewrite_module` y quita el `#`
- Reinicia Apache

---

## FUNCIONALIDADES

- ✅ Login con sesiones PHP y roles (admin / vendedor / almacén)
- ✅ Dashboard con KPIs, gráficos en tiempo real (Chart.js)
- ✅ Gestión completa de productos (CRUD + alertas de stock)
- ✅ Registro de ventas con múltiples productos, descuenta stock automáticamente
- ✅ Gestión de clientes (CRUD)
- ✅ Gestión de usuarios con roles (solo admin)
- ✅ Filtros por fecha y estado en ventas
- ✅ Alertas visuales de stock bajo
