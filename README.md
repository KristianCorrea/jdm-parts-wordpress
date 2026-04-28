# JDM Parts WordPress - Local Development Setup

Docker-based WordPress + WooCommerce environment for the custom storefront theme.

## What is in this repo

- **Tracked product code:** `theme/jdm-miami-theme`
- **Local WordPress files:** `wordpress/` (bind-mounted, not tracked)
- **Database storage:** Docker volume `db_data`

WordPress core and data are local infrastructure. The theme is the main product code.

## Requirements

- Docker
- Docker Compose
- Node.js + npm (for theme CSS build/watch)

## Project structure

```text
jdm-parts-wordpress/
├── docker-compose.yml
├── README.md
├── seed_data.sh
├── theme/
│   └── jdm-miami-theme/
└── wordpress/   # locally mounted volume created on container boot, ignored by git
```

## 1) Start containers

```bash
docker compose up -d
```

App URLs:

- Site: `http://localhost:8080`
- WP Admin: `http://localhost:8080/wp-admin`

On first run, complete the WordPress install wizard.

## 2) Install and activate WooCommerce

In WP Admin:

1. Go to **Plugins**
2. Install and activate **WooCommerce**
3. Complete WooCommerce setup wizard

## 3) Activate the custom theme

In WP Admin:

1. Go to **Appearance > Themes**
2. Activate **jdm-miami-theme**

## 4) Set up Tailwind (required)

Tailwind is part of the default workflow for this project.

```bash
cd theme/jdm-miami-theme
npm install
npx @tailwindcss/cli -i ./src/input.css -o ./assets/css/tailwind.css --watch
```

Keep the watcher running while developing so `assets/css/tailwind.css` updates automatically.

Do not compile to `style.css`. That file is reserved for the WordPress theme header.

## 5) Seed WooCommerce test data

The script `seed_data.sh` creates:

- Product categories
- Global WooCommerce attributes + terms (year, make, model, etc.)
- Sample products

### Before running seed

- Containers are up (`docker compose up -d`)
- WordPress install is complete
- WooCommerce is active

### Run seed

From project root:

```bash
chmod +x seed_data.sh
./seed_data.sh
```

The script is idempotent for seeded records and can be re-run safely.

## Daily workflow

1. Start services: `docker compose up -d`
2. Start Tailwind watcher in `theme/jdm-miami-theme`:

   ```bash
   npx @tailwindcss/cli -i ./src/input.css -o ./assets/css/tailwind.css --watch
   ```

3. Edit theme files and refresh browser.

No Docker image rebuild is needed for regular theme changes.
You also usually do not need to recreate containers for normal theme edits.

## Useful commands

```bash
# Stop environment
docker compose down

# Reset everything (destroys DB + local WP state)
docker compose down -v
docker compose up -d

# Open shell in WordPress app container
docker exec -it jdm_wp_app bash
```

## Notes on mounts and data

- `./wordpress` -> `/var/www/html`
- `./theme/jdm-miami-theme` -> `/var/www/html/wp-content/themes/jdm-miami-theme`
- `db_data` docker volume stores MySQL data

