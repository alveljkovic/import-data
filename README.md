# 📁 Data Import System — Laravel Project

A Laravel-based system for importing, validating, storing, and auditing structured datasets.  
Includes: multi-file import configuration, validation rules, audit logging, import job tracking, filtered data views, and XLSX export.

---

## 👤 1. Contact Information

| Detail | Value |
|--------|--------|
| **Name** | Aleksandar Veljković |
| **Email** | aleksandar.veljkovic@gmail.com |

---

## 📝 2. Project Overview

This project provides a modular system for:

- Importing various CSV/XLSX files defined through a central **imports configuration file**
- Mapping columns dynamically to database tables
- Validating each row with detailed validation logs
- Storing failed rows and validation errors
- Creating audit entries for each row change or failed import
- Viewing imported datasets with filtering and global search
- Exporting currently filtered results to XLSX
- Tracking import statuses in a dedicated “Imports” section

---

## ⚙️ 3. Features

### ✔️ User Management
- Comprehensive user administration with role-based access control  
- Fine-grained permission management for restricting or granting specific actions  
- Assigning and revoking permissions for individual users directly through the admin panel

### ✔️ Import System  
- Supported import types defined in `config/data_import.php`
- Automatic column-to-database mapping
- Per-row validation with detailed error logs
- Saves original filename, import type, user, timestamps

### ✔️ Audit Logs  
- Every change to dataset tables is logged in `data_import_audits`
- Logs are displayed in a modal using pure vanilla JS

### ✔️ Imports Tab  
- Paginated list of all import processes  
- Displays: user, import type key, file key, original filename, status, timestamps  
- Log-view icon opens detailed validation logs

### ✔️ Imported Data Tab  
- Sub-tabs for each configured file (from `config/data_import.php`)
- Table view of dataset contents
- Column filters + global search
- XLSX export of filtered results

### ✔️ XLSX Export  
- Powered by **Maatwebsite Excel**

---

## 🛠️ Technical Stack

- **PHP 8.3** — Modern, high-performance runtime  
- **Laravel 12** — Robust application framework with built-in support for queues, jobs, events, and file storage  
- **Composer v2** — Dependency management with optimized autoloading
- **Roles/Permissions** — Associate users with roles and permissions `spatie/laravel-permission`
- **Laravel's authentication starter kit** - `laravel/breeze`
- **Admin LTE** - Easy AdminLTE integration with Laravel `jeroennoten/laravel-adminlte`
- **Excel/CSV lib** - Supercharged Excel exports and imports in Laravel `maatwebsite/excel`

---

## 📦 4. Installation & Setup

### 1. Clone the repository
```bash
git clone git@github.com:alveljkovic/import-data.git
cd import-data
```

### 2. Install dependencies
```bash
composer install
```

### 3. Install Breeze starter kit
```bash
php artisan breeze:install blade 
```

### 4. Install NPM
```bash
npm install
```

### 5. Generate App key
```bash
php artisan key:generate
```

### 6. Run migrations
```bash
php artisan migrate
```

### 7. Run seeder
```bash
php artisan db:seed
```
Seeder will seed the DB with:
- 2 users (credentials for login see in `database/seeders/UsersTableSeeder.php`)
- 2 Roles (`admin`, `user`)
- 2 Permissions (`user-management`, `data-import`). These are associated with seeded Admin user

### 8. Build assets
```bash
npm run build
```

### 9. Create Permissions manually
- Login as Admin User (see credentials in `database/seeders/UsersTableSeeder.php`).
- Go to User Management -> Permissions
- Create permissions: `import-orders` and `import-products`
- Assign all permissions to Admin user

### 10. Have fun