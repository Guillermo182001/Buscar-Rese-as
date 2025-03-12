# 📝 Buscar Reseñas

## 🚀 Descripción

Es una aplicación web que permite a los usuarios buscar reseñas de páginas, productos y lugares a través de las APIs de Google Places y Facebook Graph. El proyecto está dividido en dos partes principales:

- **Backend**: Desarrollado con Laravel (carpeta `api`, corriendo en el puerto 8000).
- **Frontend**: Desarrollado con Vue.js (carpeta `web`, corriendo en el puerto 8080).

## 📦 Estructura del Proyecto

```
├── api/   # Laravel backend
└── web/   # Vue.js frontend
```

## 🏗️ Instalación y Configuración

1. **Clonar el repositorio:**

   ```bash
   git clone https://github.com/Guillermo182001/Buscar-Rese-as.git
   cd Buscar-Rese-as
   ```

2. **Configurar el backend:**

   ```bash
   cd api
   cp .env.example .env
   composer install
   php artisan key:generate
   php artisan serve
   ```

3. **Configurar el frontend:**

   ```bash
   cd ../web
   npm install
   npm run serve
   ```

## 🌟 Funcionalidades (en progreso)

-

## ⚡ Uso

1. Abre el backend en tu navegador (por defecto, http\://localhost:8000).
2. Abre el frontend (por defecto, http\://localhost:8080).
3. Usa el buscador para encontrar reseñas de productos, páginas o lugares.

## 📄 Convenciones de Commits

Este proyecto sigue las convenciones de [Conventional Commits](https://www.conventionalcommits.org/):

- `feat`: Añadir una nueva funcionalidad
- `fix`: Corregir un error
- `chore`: Tareas de mantenimiento o configuración
- `docs`: Actualización de la documentación
- `style`: Cambios de estilo (espacios, puntos y comas, etc.)

## 📢 Estado del Proyecto

> **Este proyecto está en desarrollo activo.** Algunas funciones pueden estar incompletas o no funcionar correctamente.

## 📚 Tecnologías utilizadas

-Laravel — Backend.

-Vue.js — Frontend.

-Axios — Para peticiones HTTP.

-Google Places API y Facebook Graph API — Para obtener reseñas.

---
