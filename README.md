# Linamnam (phpontech)

A Filipino recipe sharing platform.

## Features
- User authentication
- Recipe posting, liking, saving, commenting
- Responsive frontend
- AJAX actions for interactivity

## Structure
- `controllers/` — Backend logic
- `models/` — Data models
- `views/` — HTML templates
- `assets/` — CSS, JS, images
- `includes/` — Config, utility functions

## Setup
1. Install Docker Desktop
2. Copy `.env.example` to `.env` (optional, defaults are already set in `docker-compose.yml`)
3. Start containers:
	- `docker compose up --build -d`
4. Import the included SQL schema into MySQL (PowerShell):
	- `Get-Content -Raw database/schema.sql | docker compose exec -T db mysql -uroot -proot linamnam`
5. Open the app at `http://localhost:8081`

## Docker Services
- App (PHP + Apache): `http://localhost:8081`
- MySQL: internal Docker service `db` (no host port exposed)

To access MySQL shell:
- `docker compose exec db mysql -uroot -proot linamnam`

## Stop
- `docker compose down`
- To also remove DB data volume: `docker compose down -v`

## API Endpoints
See `ajax_actions.php` and `controllers/` for details.

## License
MIT
