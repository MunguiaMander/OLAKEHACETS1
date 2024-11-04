# OLAKEHACETS1
# Manual Técnico

## Descripción General
Este proyecto es una aplicación web desarrollada en Laravel para la gestión de eventos. El sistema permite a los usuarios ver y registrarse en eventos, a los publicadores crear y gestionar eventos, y a los administradores moderar publicaciones y manejar reportes.

### Tecnologías Utilizadas
- **Backend**: Laravel (PHP 8.3.12)
- **Frontend**: Bootstrap 4.5, Bootstrap Icons, JavaScript
- **Base de Datos**: MySQL
- **Otras Herramientas**: Vite para la compilación de assets

## Estructura de Directorios
    /app
    ├── Http
    │   ├── Controllers
    │   │   ├── AdminController.php
    │   │   ├── AuthController.php
    │   │   ├── EventController.php
    │   │   └── PublisherController.php
    │   └── Middleware
    ├── Models
    │   ├── User.php
    │   ├── Event.php
    │   ├── Post.php
    │   └── Attendance.php
    /resources
    ├── js
    │   ├── app.js
    │   ├── notifications.js
    │   └── publisher.js
    └── views
        ├── auth
        ├── dashboard.blade.php
        ├── home.blade.php
        ├── publisher.blade.php
        └── welcome.blade.php
        

## Configuración de Base de Datos
### Estructura de Tablas
1. **app_users**: Tabla que contiene los usuarios con su rol y estado.
2. **posts**: Publicaciones asociadas a los eventos.
3. **events**: Detalles de cada evento (fecha, hora, ubicación, etc.).
4. **attendances**: Tabla relacional entre usuarios y eventos.
5. **notifications**: Tabla para gestionar notificaciones a usuarios.

> La base de datos `olakehace` se puede inicializar ejecutando el script SQL provisto en el archivo `olakehacebd.sql`.

### Relaciones Clave
- `User` (app_users) tiene muchos `Posts`.
- `Post` tiene un `Event`.
- `Event` tiene muchas `Attendances`.
  
## Funcionalidades
### Asistencia a un Evento
- **Descripción**: Los usuarios pueden registrarse para asistir a un evento. Si ya están registrados, recibirán una notificación.
- **Ubicación en el Código**:
  - `EventController::attendEvent()`: Controlador que maneja la lógica de registro de asistencia.
  - `attendances` (tabla): Guarda las relaciones entre usuarios y eventos.

### Creación de Eventos
- **Descripción**: Los publicadores pueden crear eventos que serán visibles en la página de inicio.
- **Ubicación en el Código**:
  - `PublisherController::store()`: Lógica para almacenar un nuevo evento.
  - `events` (tabla): Contiene todos los eventos registrados.

### Visualización de Asistentes
- **Descripción**: Los publicadores pueden ver la lista de asistentes a sus eventos desde el panel de administración.
- **Ubicación en el Código**:
  - `PublisherController::showAttendees()`: Lógica que devuelve la lista de asistentes a un evento específico.
  - `attendances` (tabla): Relaciona los usuarios con eventos.

### Moderación de Publicaciones
- **Descripción**: Los administradores pueden aprobar, ocultar o banear publicaciones. Además, pueden ver las publicaciones reportadas.
- **Ubicación en el Código**:
  - `AdminController`: Contiene las funciones de moderación.
  - `posts` (tabla): Almacena el estado de cada publicación.

# Script de Instalación para Laravel, Composer y npm

## Prerrequisitos

   ```bash
   #!/bin/bash

    # Configuración inicial
    PROJECT_NAME="olakehace"
    DB_NAME="olakehacedb"
    DB_USER=""
    DB_PASSWORD=""
    APP_URL="http://127.0.0.1:8000"

    # Actualizar e instalar dependencias
    echo "Actualizando el sistema e instalando dependencias..."
    sudo apt update
    sudo apt install -y curl php php-mbstring php-xml php-bcmath php-zip unzip git nodejs npm

    # Instalar Composer
    echo "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer

    # Instalar dependencias de Composer
    echo "Instalando dependencias de Composer..."
    composer install

    # Instalar dependencias de npm
    echo "Instalando dependencias de npm..."
    npm install

    # Configurar archivo .env
    echo "Configurando el archivo .env..."
    cp .env.example .env
    php artisan key:generate

    # Compilar assets usando Vite
    echo "Compilando assets con Vite..."
    npm run build

    # Iniciar el servidor de desarrollo
    echo "Iniciando el servidor de Laravel..."
    php artisan serve

    echo "¡Instalación y configuración completa!"
    echo "Visita http://127.0.0.1:8000/login en tu navegador para ver el proyecto."
```