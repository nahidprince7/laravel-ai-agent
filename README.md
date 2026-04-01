# LaravelAIAgent Docker Setup (Inertia + Vue)

## Structure
- `docker/` — All Docker configuration files
- `Source/` — Place your Laravel 12 + Inertia/Vue files here

## Services
| Service        | URL                    |
|----------------|------------------------|
| Laravel App    | http://localhost:8000  |
| Vite Dev Server| http://localhost:5173  |
| phpMyAdmin     | http://localhost:8080  |
| MySQL          | localhost:3306         |

## Getting Started

1. Copy your Laravel app files into the `Source/` folder.

2. Make sure your `vite.config.js` has the correct server config for Docker:
   ```js
   server: {
       host: '0.0.0.0',
       port: 5173,
       hmr: {
           host: 'localhost',
       },
   }
   ```

3. From the `docker/` directory, run:
   ```bash
   docker-compose up -d --build
   ```

4. The `node` container will automatically run `npm install && npm run dev`

## Database Credentials
- Database: `laravel`
- Root Password: `root`
