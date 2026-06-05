# Stockia — Project Audit Report

**Application:** Stockia (stock analysis)  
**Audit date:** March 2025  
**Scope:** Technology stack, architecture, database, multitenancy, security, and flows.

> **See also:** [README.md](README.md) for setup and main routes; [docs/FEATURES.md](docs/FEATURES.md) for features built so far (dashboard, profile, Trading Learning, admin, information websites, layout).

---

## 1. Executive Summary

Stockia is a **single-tenant** Laravel 12 web application for **stock/market data analysis**. Users can upload Excel/CSV sheets (e.g. NSE Nifty 50, Market Activity reports), view data in tables and charts, export to Excel, and manage data source links. It uses **role-based access** (superadmin, admin, user) with no tenant/team/organization isolation.

---

## 2. Technology Stack

### 2.1 Backend
| Technology | Version / Detail |
|------------|------------------|
| **PHP** | ^8.2 |
| **Laravel** | ^12.0 |
| **Laravel Tinker** | ^2.10.1 |
| **Livewire** | ^4.1 (reactive UI components) |
| **Maatwebsite Excel** | ^3.1 (import/export) |

### 2.2 Frontend
| Technology | Version / Detail |
|------------|------------------|
| **Vite** | ^7.0.7 |
| **Tailwind CSS** | ^4.0.0 (@tailwindcss/vite) |
| **Bootstrap** | ^5.3.8 |
| **jQuery** | ^4.0.0 |
| **React** | ^19.2.4 (used with Recharts) |
| **Recharts** | ^3.7.0 (charts) |
| **Axios** | ^1.11.0 |

### 2.3 Dev / Tooling
| Tool | Purpose |
|------|---------|
| Laravel Debugbar | ^4.1 — debug bar (env: `DEBUGBAR_ENABLED`) |
| Laravel Pail | ^1.2.2 — log tailing |
| Laravel Pint | ^1.24 — code style |
| Laravel Sail | ^1.41 — Docker dev |
| PHPUnit | ^11.5.3 — tests |
| Faker | ^1.23 — seeding |
| Nunomaduro Collision | ^8.6 — CLI error reporting |

### 2.4 Infrastructure (from .env)
| Concern | Default / Config |
|---------|------------------|
| **Database** | MySQL (`DB_CONNECTION=mysql`), DB name `stockia` |
| **Session** | Database driver (`SESSION_DRIVER=database`) |
| **Cache** | Database (`CACHE_STORE=database`) |
| **Queue** | Database (`QUEUE_CONNECTION=database`) |
| **Filesystem** | Local disk (`FILESYSTEM_DISK=local`) |
| **Broadcasting** | Log (`BROADCAST_CONNECTION=log`) |

---

## 3. Database Schema (Tables)

### 3.1 Application Tables (project migrations only)

| Table | Purpose |
|-------|---------|
| **users** | Laravel default + `role` (string, default `user`) |
| **data_source_links** | External data sources (name, slug, url, display_columns, is_active) |
| **sheet_uploads** | Upload metadata (file path, columns, row_count, report_date, user_id, data_source_link_id) |
| **stock_data** | Row-level data per sheet (sheet_upload_id, row_index, json `data`) |
| **nifty50_extended** | Nifty 50 reference (security_symbol, company_name, industry, weightages, sector, sort_order) |

### 3.2 Column Details

**users** (Laravel default + add_role migration)
- `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`
- `role` (string, 32) — values: `superadmin`, `admin`, `user`

**data_source_links**
- `id`, `name`, `slug` (unique, 64), `url` (text), `display_columns` (json, nullable), `is_active` (boolean), `timestamps`

**sheet_uploads**
- `id`, `user_id` (nullable FK → users), `data_source_link_id` (nullable FK → data_source_links)
- `name` (nullable), `original_name`, `path`, `columns` (json), `row_count`, `report_date` (nullable date), `timestamps`
- FKs: `nullOnDelete` for user and data_source_link

**stock_data**
- `id`, `sheet_upload_id` (FK → sheet_uploads, cascadeOnDelete), `row_index`, `data` (json), `timestamps`

**nifty50_extended**
- `id`, `security_symbol` (indexed), `company_name`, `industry`, `nifty_weightage_pct`, `sector_thematic_index`, `sector_thematic_weightage_pct`, `relationship_of_index`, `sort_order`, `timestamps`

### 3.3 Laravel / Package Tables (used at runtime)
- **sessions** — session driver `database`
- **cache** — cache driver `database` (if used)
- **jobs** / **failed_jobs** — queue driver `database`
- **password_reset_tokens** — Laravel auth (if used)

---

## 4. Multitenancy

**Conclusion: Not multitenant.**

- No `tenant_id`, `team_id`, `organization_id`, or similar on any table.
- No tenant/team/workspace middleware or scoping in the codebase.
- Data separation is by **user** only where applicable:
  - **Dashboard / Market Activity:** sheet is scoped by `user_id` when logged in (`SheetUpload::where('user_id', $request->user()->id)`), else `whereNull('user_id')`.
  - **Sheet uploads:** optional `user_id` and `data_source_link_id`; no tenant context.
- Single shared database; no per-tenant DB or schema.

---

## 5. Authentication & Authorization

### 5.1 Authentication
- Laravel session-based auth (no API tokens in scope).
- Routes: `login`, `register`, `logout` (POST).
- Controllers: `App\Http\Controllers\Auth\LoginController`, `RegisterController`.
- New users get `role` = `user` (see `RegisterController`).

### 5.2 Authorization (Roles)
- **Middleware:** `App\Http\Middleware\EnsureRole` (alias `role`).
- **Usage:** `Route::middleware(['auth', 'role:admin,superadmin'])->...` for admin area.
- **Logic:** User must be logged in; if roles are specified, `user->role` must be in the list (e.g. `admin`, `superadmin`).
- **Roles:** `superadmin`, `admin`, `user` (stored in `users.role`).

### 5.3 Seeded Users (UserSeeder)
| Role | Email | Password |
|------|--------|----------|
| Super Admin | superadmin@example.com | password |
| Admin | admin@example.com | password |
| User | test@example.com | password |

---

## 6. Application Structure & How It’s Handled

### 6.1 Route Groups (high level)
- **Guest:** `login`, `register`.
- **Auth:** `logout`, `dashboard`, sheet export/view, nifty50 table/export, market-activity, indices, reports, uploads.
- **No auth required:** `settings` (index), `data-source/{id}/open`, `data-source/{id}/download`.
- **Admin (auth + role:admin,superadmin):** `admin/data-source-links` (index, store, edit, update, destroy).

### 6.2 Main Flows

**Data source links (admin)**
- **CRUD:** `DataSourceLinkController`: index, store, edit, update, destroy.
- **Public:** open (redirect to URL), download (proxy stream from URL).
- **Model:** `DataSourceLink`; config: `config/stockia.php` (nifty50 slug, market_activity, excluded_columns, etc.).

**Sheet upload & import**
- **Service:** `SheetImportService::import($file, $userId, $dataSourceLinkId)`.
- **Imports:** `StockSheetImport` (generic Excel/CSV with heading row → `SheetUpload` + `StockDatum` rows); `MarketActivityReportImport` (NSE Market Activity: detect header row, map columns, filter excluded indices, compute return, store in same `sheet_uploads` + `stock_data`).
- **Storage:** File stored under `storage/app/sheets/`; DB stores path and row data in `stock_data.data` (JSON).
- **Replace behaviour:** When `dataSourceLinkId` is set, previous uploads for that source (and same user or null user) are deleted before creating the new one.

**Dashboard**
- **Controller:** `DashboardController`: loads latest Market Activity sheet (by `data_source_link_id` for slug `market_activity`, scoped by current user or null).
- **Logic:** Uses `IndexClassifier::isBroadMarket()` (whitelist from `config/indices.php`), computes return, sorts; returns gainers/losers and market status (bullish/bearish/sideways).

**Indices**
- **Controller:** `IndexController`: index (list) and show by slug; uses `DataSourceLink`, Market Activity data, and `IndexDataExtractor` for normalized index metrics.

**Nifty 50 extended**
- **Controller:** `Nifty50ExtendedController`, `Nifty50ExtendedExportController`; model `Nifty50Extended`; export classes `Nifty50Export`, `Nifty50ExtendedExport`.

**Reports / Uploads**
- **Controllers:** `ReportsController`, `UploadsController` — list and present reports/uploads (sheet-centric).

### 6.3 Key Services
| Service | Responsibility |
|---------|----------------|
| **SheetImportService** | Orchestrates Excel import, storage, and optional replace-by-source; calls `StockSheetImport` or `MarketActivityReportImport`. |
| **IndexDataExtractor** | Normalizes market activity row keys and numbers; computes return (log-return). |
| **IndexClassifier** | Determines if an index name is in the “broad market” whitelist (`config/indices.broad_market`). |

### 6.4 Config Files (app-specific)
- **config/stockia.php** — excluded_columns, column_display_names, nifty50 (slug, display_columns, export_headers), market_activity (URL, excluded_indices, section_boundaries, column names).
- **config/indices.php** — `broad_market` list of index names for dashboard filtering.

---

## 7. Models & Relationships

| Model | Table | Main relationships |
|-------|--------|---------------------|
| **User** | users | — (no `sheetUploads` defined in codebase) |
| **DataSourceLink** | data_source_links | hasMany SheetUpload (`sheetUploads`) |
| **SheetUpload** | sheet_uploads | belongsTo User, belongsTo DataSourceLink; hasMany StockDatum (`rows`) |
| **StockDatum** | stock_data | belongsTo SheetUpload |
| **Nifty50Extended** | nifty50_extended | — |

---

## 8. Views (Blade / Livewire)

- **Layouts:** `layouts/app.blade.php` (nav: Dashboard, Market Activity, Indices, Reports, Uploads, Settings, Admin when role admin/superadmin, Logout).
- **Auth:** `auth/login`, `auth/register`.
- **Features:** `home`, `dashboard`, `sheet/show`, `indices/index`, `indices/show`, `market-activity/index`, `market/table`, `market/upload-modal`, `nifty50-extended/index`, `reports/index`, `uploads/index`, `settings/index`, `data_source_links/index`, `data_source_links/edit`.
- **Components:** e.g. `components/⚡upload-sheet`, `⚡dashboard`, `⚡index-cards`, `data-table`, `data-source-links-card`, `partials/alerts`.

---

## 9. Security & Environment

- **Debug:** `APP_DEBUG` and `DEBUGBAR_ENABLED` (env); debug bar only when enabled.
- **Roles:** Enforced via `EnsureRole` middleware on admin routes.
- **CSRF:** Laravel default (web routes).
- **Passwords:** Hashed (Laravel default).
- **Sessions:** Stored in database; lifetime 120 min in .env example.

---

## 10. Summary Table

| Aspect | Detail |
|--------|--------|
| **Type** | Single-tenant stock/market analysis web app |
| **Framework** | Laravel 12, PHP 8.2 |
| **Frontend** | Blade, Livewire, Bootstrap, Tailwind, React (Recharts), Vite |
| **Database** | MySQL (stockia); sessions/cache/queue use DB |
| **Auth** | Session; roles: superadmin, admin, user |
| **Multitenancy** | None |
| **Main tables** | users, data_source_links, sheet_uploads, stock_data, nifty50_extended |
| **Main flows** | Data source CRUD (admin), sheet upload/import, dashboard (market activity), indices, Nifty50 extended, reports, uploads |

This document reflects the codebase and migrations as of the audit date. For deployment and hardening, consider env-specific `APP_DEBUG`/`DEBUGBAR_ENABLED`, rate limiting, and backup strategy for DB and `storage/app/sheets`.
