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

---

## 📦 4. Installation & Setup

### 1. Clone the repository
```bash
git clone git@github.com:alveljkovic/massdata-imports.git
cd massdata-imports
