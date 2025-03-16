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

### Búsqueda de Reseñas

- **Búsqueda por Google Place ID**: Permite buscar y mostrar reseñas de establecimientos usando el ID de Google Places.
- **Búsqueda por Facebook Page ID**: Permite buscar y mostrar reseñas de páginas de Facebook usando su ID.
- **Visualización de reseñas**: Muestra información detallada como nombre del autor, foto de perfil, calificación, texto de la reseña y tiempo relativo.


### Interfaz de Usuario

- **Carrusel de reseñas**: Visualización de reseñas en un carrusel interactivo para una mejor experiencia de usuario.
- **Paginación**: Sistema de paginación para navegar fácilmente por grandes conjuntos de reseñas.
- **Indicadores de carga**: Feedback visual durante la carga de datos para mejorar la experiencia de usuario.
- **Manejo de errores**: Mensajes claros cuando ocurren errores en las búsquedas o conexiones.


### Rendimiento y Optimización

- **Sistema de caché**: Almacenamiento temporal de reseñas para reducir llamadas a las APIs y mejorar el rendimiento.
- **Indicador de caché**: Muestra cuando se están visualizando datos en caché con opción para actualizarlos.
- **Configuración centralizada de APIs**: Gestión unificada de endpoints y configuraciones de API.


### Seguridad

- **Protección CORS**: Control de acceso a recursos desde diferentes dominios.
- **Rate Limiting**: Limitación de tasas de solicitud para proteger las APIs externas y prevenir abusos.
- **Manejo seguro de tokens**: Gestión adecuada de tokens de API mediante variables de entorno.


### Características Técnicas

- **Arquitectura cliente-servidor**: Separación clara entre frontend (Vue.js) y backend (Laravel).
- **APIs RESTful**: Endpoints bien definidos para la comunicación entre cliente y servidor.
- **Diseño responsivo**: Interfaz adaptable a diferentes tamaños de pantalla.
- **Reintentos automáticos**: Mecanismo para reintentar solicitudes fallidas con backoff exponencial.


### Integración con APIs Externas

- **Google Places API**: Obtención de reseñas de establecimientos, lugares y negocios.
- **Facebook Graph API**: Acceso a reseñas y calificaciones de páginas de Facebook.
- **Gestión de límites de API**: Respeto de las cuotas y límites de las APIs externas.

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

## Dominio del Proyecto

-https://7560-38-25-17-101.ngrok-free.app (utilizar https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder para encontrar el place ID que desees encontrar, colocalo en el buscador de reseñas de Google)
![image](https://github.com/user-attachments/assets/3f2de2c5-065e-43cf-9879-8d9ad5e06894)
![image](https://github.com/user-attachments/assets/1aa6619e-3360-4022-bea2-8ecf85c4fbb4)

---
