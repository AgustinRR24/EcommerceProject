# E-commerce Project

Sistema de comercio electrónico desarrollado con Laravel 11, Filament 3 e Inertia.js + React.

## Configuración del Proyecto

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL
- XAMPP (para desarrollo local)

### Instalación

1. Clonar el repositorio
2. Copiar `.env.example` a `.env` y configurar las variables de entorno
3. Instalar dependencias:
```bash
composer install
npm install
```

4. Generar key de aplicación:
```bash
php artisan key:generate
```

5. Ejecutar migraciones:
```bash
php artisan migrate --seed
```

6. Crear enlace simbólico de storage:
```bash
php artisan storage:link
```

7. Compilar assets:
```bash
npm run dev
# o para producción
npm run build
```

## Desarrollo Local

### Iniciar servidores

**Opción 1: Usando npm (puede tener problemas de permisos en Windows)**
```bash
npm run dev
```

**Opción 2: Usando Node directamente**
```bash
node node_modules/vite/bin/vite.js
```

**Opción 3: Usando el archivo batch (solo Windows)**
```bash
.\dev.bat
```

### Servidor Laravel
```bash
php artisan serve
```

El proyecto estará disponible en `http://127.0.0.1:8000`

## Estructura del Proyecto

### Paneles de Administración

- **Panel Admin**: `http://127.0.0.1:8000/admin`
  - Gestión completa del e-commerce
  - Usuarios, productos, categorías, órdenes, etc.
  - Autenticación con guard `web`

- **Panel Customers**: `http://127.0.0.1:8000/customer`
  - Portal para clientes
  - Ver órdenes, perfil, historial
  - Autenticación con guard `web`

### Frontend (React + Inertia)

- **Landing**: `http://127.0.0.1:8000/`
- **Productos**: `http://127.0.0.1:8000/products`
- **Carrito**: `http://127.0.0.1:8000/cart`
- **Checkout**: `http://127.0.0.1:8000/checkout`

## Integración con MercadoPago

### Configuración

Agregar en `.env`:
```env
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_PUBLIC_KEY=tu_public_key
```

### Flujo de Pago en Desarrollo (Localhost)

**IMPORTANTE**: El auto-redirect de MercadoPago NO funciona con `localhost` o `127.0.0.1` en modo sandbox.

#### Proceso de pago en desarrollo:

1. **Realizar compra**: Agregar productos al carrito y proceder al checkout
2. **Pagar en MercadoPago**: Completar el pago en la ventana de MercadoPago
3. **Procesar el pago manualmente**: Después de pagar, MercadoPago NO te redirigirá automáticamente

#### Cómo completar una orden en desarrollo:

**Opción 1: URL Manual (Recomendado)**

Después de pagar en MercadoPago, acceder manualmente a:

```
http://127.0.0.1:8000/checkout/success?payment_id=PAYMENT_ID&status=approved&external_reference=ORDER_NUMBER
```

Donde:
- `PAYMENT_ID`: El ID que MercadoPago muestra después del pago (ej: `133468507383`)
- `ORDER_NUMBER`: El número de orden generado (ej: `ORD-0b6c8b04-cf05-4524-aa8f-708`)

**Ejemplo completo:**
```
http://127.0.0.1:8000/checkout/success?payment_id=133468507383&status=approved&external_reference=ORD-0b6c8b04-cf05-4524-aa8f-708
```

**Opción 2: Desde la Base de Datos**

1. Buscar la última orden creada en la tabla `orders`
2. Copiar el `order_number`
3. Usar la URL del ejemplo anterior reemplazando los valores

**Opción 3: Desde el Panel Admin**

1. Ir a `http://127.0.0.1:8000/admin/orders`
2. Buscar la orden con estado `pending`
3. Cambiar manualmente el estado a `completed`

### Flujo en Producción

En producción, con un dominio real (no localhost), el auto-redirect funcionará correctamente y el usuario será redirigido automáticamente después del pago.

## Solución de Problemas Comunes

### Error "This page has expired" en login

**Causa**: Sesiones corruptas o cookies antiguas

**Solución**:
1. Limpiar cookies del navegador para `127.0.0.1:8000`
2. O usar modo incógnito
3. Ejecutar:
```bash
php artisan optimize:clear
php artisan config:cache
```

### Imágenes no se muestran después de cambiar nombre del proyecto

**Causa**: El enlace simbólico de storage apunta al path antiguo

**Solución**:
```bash
# Eliminar enlace antiguo
rm public/storage
# En Windows CMD:
# del public\storage

# Crear nuevo enlace
php artisan storage:link
```

### Problemas con npm run dev en PowerShell

**Causa**: Política de ejecución de scripts deshabilitada en Windows

**Solución 1**: Cambiar política (PowerShell como administrador):
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

**Solución 2**: Usar el archivo batch:
```bash
.\dev.bat
```

**Solución 3**: Usar Node directamente:
```bash
node node_modules/vite/bin/vite.js
```

## Licencia

Este proyecto es de código abierto bajo la licencia MIT.
