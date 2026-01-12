# 🚀 Deploying This Laravel App to a DigitalOcean Droplet

> End‑to‑end guide for taking this project from your local machine to a live server.

---

## 1. Prerequisites

### 1.1. What you need

- A DigitalOcean Droplet running **Ubuntu 22.04+** (recommended)
- SSH access to the droplet (root or sudo user)
- A Git repo with this project (you already pushed to `origin main`)
- Basic domain (optional but recommended)

### 1.2. Information to note

- Droplet IP: `YOUR_DROPLET_IP`
- Git repo URL: `git@github.com:your-org/your-repo.git` or `https://github.com/...`
- Domain name (if any): `yourdomain.com`

Replace these placeholders in the commands below.

---

## 2. SSH into the Droplet

On your local machine:

```bash
ssh root@YOUR_DROPLET_IP
# or, if you have a non-root sudo user
ssh youruser@YOUR_DROPLET_IP
```

Make sure you can log in without errors before continuing.

---

## 3. Install System Dependencies

From inside the droplet:

```bash
# Update package index
sudo apt update && sudo apt upgrade -y

# Install PHP + common extensions + Git + Nginx + Node (if needed later)
sudo apt install -y \
  php-cli php-fpm php-mbstring php-xml php-curl php-zip php-mysql php-gd \
  unzip git nginx

# Install Composer globally
cd /tmp
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Verify
php -v
composer -V
nginx -v
```

If you plan to run front-end builds on the server:

```bash
# Install Node.js LTS via NodeSource
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

---

## 4. Create a Deploy User and App Directory (Recommended)

To avoid running the app as root:

```bash
sudo adduser deploy
sudo usermod -aG sudo deploy

# Create app directory
sudo mkdir -p /var/www/clouvie-backend
sudo chown deploy:www-data /var/www/clouvie-backend
```

Log in as `deploy` for the remaining steps:

```bash
su - deploy
cd /var/www/clouvie-backend
```

---

## 5. Clone the Project

```bash
cd /var/www/clouvie-backend

git clone YOUR_REPO_URL .

# Verify files
ls
```

You should see `artisan`, `app/`, `config/`, `docs/`, etc.

---

## 6. Environment Setup (.env)

### 6.1. Copy example env

```bash
cp .env.example .env
```

### 6.2. Generate app key

```bash
php artisan key:generate
```

### 6.3. Configure database

Edit `.env` on the server:

```bash
nano .env
```

Set values like (for your domain `backend.clouvie.com`):

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://backend.clouvie.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clouvie
DB_USERNAME=clouvie_user
DB_PASSWORD=strong_password_here
```

> Make sure the database and user exist (see next step).

---

## 7. Create MySQL Database and User

If MySQL is not installed yet:

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

Create DB and user:

```bash
sudo mysql

CREATE DATABASE clouvie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'clouvie_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON clouvie.* TO 'clouvie_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Ensure `.env` DB settings match these values.

---

## 8. Install PHP Dependencies

From the project directory `/var/www/clouvie-backend`:

```bash
composer install --no-dev --optimize-autoloader
```

This installs all required PHP packages for production.

---

## 9. Run Migrations

```bash
php artisan migrate --force
```

`--force` allows migrations in production.

---

## 10. Build Frontend Assets (Optional)

If you want to serve compiled assets (Vite) from this server:

```bash
npm install
npm run build
```

This will build into `public/build` using your existing `vite.config.js`.

---

## 11. Configure File Permissions

Still in `/var/www/clouvie-backend`:

```bash
# Ensure storage and cache directories are writable
sudo chown -R deploy:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
```

This allows Laravel to write logs, cache, and compiled views.

---

## 12. Configure Nginx for Laravel

Switch back to a sudo-capable user (if needed) and create an Nginx site config:

```bash
sudo nano /etc/nginx/sites-available/clouvie-backend
```

Example config:

```nginx
server {
    listen 80;
    server_name backend.clouvie.com YOUR_DROPLET_IP;

    root /var/www/clouvie-backend/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php-fpm.sock; # or php8.3-fpm.sock depending on version
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable the site and test Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/clouvie-backend /etc/nginx/sites-enabled/

sudo nginx -t
sudo systemctl reload nginx
```

At this point, visiting `http://YOUR_DROPLET_IP` or `http://backend.clouvie.com` should load the Laravel app. After enabling HTTPS (next section), use `https://backend.clouvie.com`.

---

## 13. Configure Supervisor for Queue Workers (Optional)

If you start using Laravel queues later, you can run `php artisan queue:work` under Supervisor. For now, you can skip this if you’re not using queues.

---

## 14. Deploying Updates

Whenever you push new code to Git and want to update the droplet:

```bash
ssh deploy@YOUR_DROPLET_IP
cd /var/www/clouvie-backend

git pull origin main

composer install --no-dev --optimize-autoloader
php artisan migrate --force   # if there are new migrations
npm run build                 # if frontend assets changed

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

This keeps the server in sync with your local/main branch.

---

## 15. (Optional) Enable HTTPS with Lets Encrypt

If you have a domain pointing to the droplet (for example `backend.clouvie.com`), you can use Certbot:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d backend.clouvie.com
```

Follow the prompts. Certbot will update your Nginx config and set up auto-renewal.

---

## 16. Quick Verification Checklist

- [ ] `https://backend.clouvie.com` (or `http://YOUR_DROPLET_IP` before SSL) loads the Laravel home page
- [ ] `php artisan --version` works on the droplet
- [ ] `/api/users` and `/api/register` respond correctly
- [ ] `/api/waitlist` accepts POST requests
- [ ] `/admin/waitlist` shows waitlist signups

If any step fails, check:

- Laravel logs: `storage/logs/laravel.log`
- Nginx logs: `/var/log/nginx/error.log`
- Permissions on `storage/` and `bootstrap/cache/`

---

**Last Updated:** January 4, 2026

This guide is specific to this project but can be reused as a template for similar Laravel apps.
