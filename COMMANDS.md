# Cheat Sheet Commands - Sistem Absensi FansNet

## Setup Awal

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Generate application key
php artisan key:generate

# 3. Run migrations
php artisan migrate

# 4. Create storage link
php artisan storage:link

# 5. Seed sample data (optional)
php artisan db:seed --class=KaryawanSeeder
```

## Database Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database (drop all tables and re-run migrations)
php artisan migrate:fresh

# Reset database with seeders
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=KaryawanSeeder

# Create new migration
php artisan make:migration create_table_name

# Create model with migration
php artisan make:model NamaModel -m
```

## User Management

```bash
# Create admin user via tinker
php artisan tinker

# In tinker:
\App\Models\User::create([
    'name' => 'Admin', 
    'email' => 'admin@fansnet.com', 
    'password' => bcrypt('password123')
]);

# Change password
$user = \App\Models\User::where('email', 'admin@fansnet.com')->first();
$user->password = bcrypt('new_password');
$user->save();

# Exit tinker
exit
```

## Cache Management

```bash
# Clear all cache
php artisan optimize:clear

# Clear specific cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Development Server

```bash
# Start Laravel development server
php artisan serve

# Start on specific port
php artisan serve --port=8080

# Start on specific host
php artisan serve --host=192.168.1.100 --port=8000
```

## Artisan Make Commands

```bash
# Create controller
php artisan make:controller NamaController

# Create model
php artisan make:model NamaModel

# Create migration
php artisan make:migration create_nama_table

# Create seeder
php artisan make:seeder NamaSeeder

# Create middleware
php artisan make:middleware NamaMiddleware

# Create request validation
php artisan make:request NamaRequest
```

## File Permissions (Linux/Mac)

```bash
# Set storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (replace 'www-data' with your web server user)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

## Git Commands

```bash
# Initialize repository
git init

# Add all files
git add .

# Commit changes
git commit -m "Initial commit"

# Add remote
git remote add origin https://github.com/username/repo.git

# Push to remote
git push -u origin main

# Pull from remote
git pull origin main

# Check status
git status
```

## Composer Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Add new package
composer require vendor/package

# Remove package
composer remove vendor/package

# Dump autoload
composer dump-autoload
```

## Database Backup & Restore

```bash
# Backup database (MySQL)
mysqldump -u root -p absensi_fansnet > backup_$(date +%Y%m%d).sql

# Restore database
mysql -u root -p absensi_fansnet < backup_20251230.sql

# Backup with compression
mysqldump -u root -p absensi_fansnet | gzip > backup_$(date +%Y%m%d).sql.gz

# Restore from compressed backup
gunzip < backup_20251230.sql.gz | mysql -u root -p absensi_fansnet
```

## Logs

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log

# View last 100 lines
tail -n 100 storage/logs/laravel.log
```

## Production Deployment

```bash
# 1. Pull latest changes
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Set permissions
chmod -R 775 storage bootstrap/cache
```

## Debugging

```bash
# Enable debug mode (development only)
# In .env set:
APP_DEBUG=true
APP_ENV=local

# Disable debug mode (production)
APP_DEBUG=false
APP_ENV=production

# View routes
php artisan route:list

# View routes with specific filter
php artisan route:list --name=absensi

# Clear compiled files
php artisan clear-compiled
```

## Database Queries (Tinker)

```bash
php artisan tinker

# Get all karyawan
App\Models\Karyawan::all();

# Get today's absensi
App\Models\Absensi::whereDate('tanggal', today())->get();

# Get pengaturan
App\Models\Pengaturan::where('key', 'ip_kantor')->first();

# Count total karyawan
App\Models\Karyawan::count();

# Get active karyawan
App\Models\Karyawan::where('is_active', true)->get();
```

## Quick Fixes

```bash
# Fix storage link error
php artisan storage:link

# Fix permission issues (Linux/Mac)
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache

# Regenerate app key
php artisan key:generate

# Fix composer autoload
composer dump-autoload
```

## Testing Commands

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TestClassName

# Create test
php artisan make:test TestName
```

## Useful Queries

```sql
-- Check total absensi today
SELECT COUNT(*) FROM absensis WHERE tanggal = CURDATE();

-- Check karyawan yang belum absen today
SELECT k.* FROM karyawans k 
LEFT JOIN absensis a ON k.id = a.karyawan_id AND a.tanggal = CURDATE()
WHERE a.id IS NULL AND k.is_active = 1;

-- Rekap kehadiran per bulan
SELECT k.nama, 
    COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) as hadir,
    COUNT(CASE WHEN a.status = 'telat' THEN 1 END) as telat
FROM karyawans k
LEFT JOIN absensis a ON k.id = a.karyawan_id
WHERE MONTH(a.tanggal) = MONTH(CURDATE())
GROUP BY k.id, k.nama;
```

## Environment Variables

```bash
# Important .env variables for this project
APP_NAME="Sistem Absensi FansNet"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_fansnet
DB_USERNAME=root
DB_PASSWORD=
```

## Server Requirements

```bash
# Check PHP version
php -v

# Check installed PHP extensions
php -m

# Required extensions:
# - PHP >= 8.1
# - BCMath
# - Ctype
# - Fileinfo
# - JSON
# - Mbstring
# - OpenSSL
# - PDO
# - Tokenizer
# - XML
# - GD (for image processing)
```

---

**Quick Reference** - Sistem Absensi FansNet
Keep this file handy for daily development tasks!
