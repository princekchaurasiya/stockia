# Stockia

Stockia is a **single-tenant** Laravel web application for **stock/market data analysis** with a spiritual dashboard identity. Users can upload Excel/CSV sheets (e.g. NSE Market Activity), view data in tables and charts, manage data sources, and access trading learning content.

---

## Tech Stack

| Layer      | Technology |
|-----------|------------|
| Backend   | PHP 8.2, Laravel 12, Livewire 4 |
| Frontend  | Blade, Bootstrap 5, Tailwind 4, Vite, React (Recharts) |
| Database  | MySQL |
| Auth      | Laravel session; roles: `superadmin`, `admin`, `user` |

---

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Set DB_* in .env
php artisan migrate
php artisan storage:link
npm install && npm run build
php artisan serve
```

- **Storage link:** Required for avatar uploads and optional `public/storage` assets.
- **Adminer:** Optional DB UI at `public/adminer.php` (see [docs/FEATURES.md](docs/FEATURES.md)).

---

## Main Routes

| Route | Description | Auth |
|-------|-------------|------|
| `/` | Redirects to dashboard | — |
| `/dashboard` | Dashboard (banner + market status/sentiment) | — |
| `/login`, `/register` | Auth | Guest |
| `/profile` | Profile edit (avatar, name, email, password) | Auth |
| `/settings` | Settings | — |
| `/trading-learning` | Trading Learning (batches → modules → lectures → videos) | Auth |
| `/information-websites` | Information/research links (card/list) | Auth |
| `/market-activity` | Market Activity upload/view | Auth |
| `/admin/trading-learning` | Admin: manage batches, modules, lectures, videos | Admin |
| `/admin/information-links` | Admin: information links CRUD | Admin |
| `/admin/data-source-links` | Admin: data source links CRUD | Admin |
| `/admin/admins` | Superadmin: admin users CRUD | Superadmin |

---

## Project Layout (Key Areas)

- **Controllers:** `app/Http/Controllers/` (Dashboard, Profile, Learning, InformationWebsite, etc.)
- **Models:** `app/Models/` (User, Batch, Module, Lecture, LectureVideo, DataSourceLink, etc.)
- **Livewire:** `app/Livewire/` (Learning: BatchList, LectureList, LectureView; Admin: AdminLectureManager, AdminVideoManager, BatchManager, ModuleManager; Tables: BaseTable, InformationLinksTable, DataSourceLinksTable, AdminUsersTable; Inputs: BatchSelect, ModuleSelect, modals)
- **Views:** `resources/views/` (layouts/app, dashboard, learning/index, admin/learning/index, profile/edit, etc.)
- **Assets:** `public/images/banner/` (dashboard spiritual banner: `spritual.png`, `spritual2.png`)

---

## Documentation

- **[docs/FEATURES.md](docs/FEATURES.md)** — Features built so far (layout, dashboard, profile, Trading Learning, admin, information websites, assets).
- **PROJECT_AUDIT.md** — Technology stack, architecture, database, security (audit report).
- **DATABASE_AUDIT_REPORT.md** — Database schema and table details.

---

## Seeded Users (UserSeeder)

| Role       | Email                   | Password  |
|------------|-------------------------|-----------|
| Super Admin| superadmin@example.com  | password  |
| Admin      | admin@example.com       | password  |
| User       | test@example.com       | password  |
