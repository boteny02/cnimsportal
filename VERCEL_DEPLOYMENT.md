# Vercel Deployment Guide for CNIMS Portal

This guide provides instructions for deploying the **CNIMS Portal** (Laravel 13 + Livewire 4 + Vite) to **Vercel** using the serverless PHP runtime.

---

## 🚀 Architecture Overview

- **Runtime:** [`vercel-php@0.9.0`](https://github.com/vercel-community/php) serverless runtime configured in `vercel.json`.
- **Entry Point:** `api/index.php` intercepts incoming requests, dynamically initializes writable `/tmp/storage` directories, and hands off execution to Laravel's front controller (`public/index.php`).
- **Asset Compilation:** Vite compiles frontend assets during the build phase (`npm run build`) into `public/build`, which Vercel serves as static assets.
- **Filesystem Isolation:** Vercel functions run in a read-only environment. All storage caches, Blade view compilation, and session files are redirected to `/tmp/storage`.

---

## 📋 Vercel Project Configuration

When importing this repository into Vercel:

1. **Framework Preset:** Select **Other**.
2. **Root Directory:** `./` (default).
3. **Build & Development Settings:**
   - **Build Command:** `npm run build` (configured automatically in `vercel.json`).
   - **Output Directory:** Leave blank (Vercel routes requests via `vercel.json`).
   - **Install Command:** Leave default.

---

## 🔑 Required Environment Variables

Configure the following environment variables in the **Vercel Dashboard** under **Project Settings > Environment Variables**:

| Variable | Recommended Value | Description |
|---|---|---|
| `APP_NAME` | `CNIMS Portal` | Name of the application |
| `APP_ENV` | `production` | Application environment |
| `APP_DEBUG` | `false` | Disable debug output in production |
| `APP_KEY` | *(Your 32-character base64 key)* | Required encryption key |
| `APP_URL` | `https://<your-project>.vercel.app` | Production URL assigned by Vercel |
| `DB_CONNECTION` | `mysql` or `pgsql` | Database driver (e.g. Supabase, Neon, PlanetScale, RDS) |
| `DB_HOST` | *(Database host)* | Remote database host |
| `DB_PORT` | `3306` (MySQL) or `5432` (PostgreSQL) | Remote database port |
| `DB_DATABASE` | *(Database name)* | Database name |
| `DB_USERNAME` | *(Database username)* | Database user |
| `DB_PASSWORD` | *(Database password)* | Database password |
| `SESSION_DRIVER` | `cookie` | Cookie sessions are stateless and ideal for serverless |
| `CACHE_STORE` | `array` | In-memory cache for serverless invocations |
| `LOG_CHANNEL` | `stderr` | Sends logs to Vercel Function logs |

> **Note on SQLite:** For fast preview/testing without an external database, SQLite will automatically initialize inside `/tmp/database.sqlite`. However, data written to `/tmp` is ephemeral and does not persist across cold starts. Use a managed MySQL or PostgreSQL database for persistent production data.

---

## 📁 Key Deployment Files

- [`vercel.json`](file:///Users/mobolaji/Herd/cnimsportal/vercel.json): Vercel configuration specifying runtime, memory, route rewrites for Vite assets, and serverless environment variables.
- [`api/index.php`](file:///Users/mobolaji/Herd/cnimsportal/api/index.php): Serverless function entry point ensuring `/tmp/storage` directories are initialized before booting Laravel.
- [`.vercelignore`](file:///Users/mobolaji/Herd/cnimsportal/.vercelignore): Prevents uploading local `vendor`, `node_modules`, SQLite files, and tests to minimize deployment size.
- [`bootstrap/app.php`](file:///Users/mobolaji/Herd/cnimsportal/bootstrap/app.php): Sets dynamic storage path when running inside Vercel.

---

## 🔄 Running Database Migrations

Because Vercel serverless functions are ephemeral, run migrations using one of the following methods:

1. **Local Terminal (Recommended):** Connect your local environment to the remote database and run:
   ```bash
   php artisan migrate --force
   ```
2. **GitHub Actions Workflow:** Add a CI step that runs `php artisan migrate --force` against your production database prior to or alongside deployment.
