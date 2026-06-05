# Stockia — Features Built (Documentation)

This document describes the features and UI changes implemented in the project so far.

---

## 1. Application Layout

- **Top navbar:** Stockia brand; user dropdown (Profile, Settings, Logout) when authenticated; Login / Register when guest.
- **Sidebar:** App navigation only (Dashboard, Settings, etc.). Logout removed from sidebar; available in navbar dropdown.
- **Bootstrap:** Layout and components use Bootstrap 5; no custom dashboard CSS in `app.css` or layout—Bootstrap used for cards, grid, badges, progress bars, etc.

---

## 2. Dashboard

- **URL:** `/dashboard` (route name: `dashboard`).
- **Layout:**
  - **Left column (col-lg-8):** Spiritual banner image (Krishna/Arjun/Saraswati). Image is responsive (`img-fluid w-100 h-auto`), no fixed height.
  - **Right column (col-lg-4):** Sticky sidebar with:
    - **Market Status** — Badge: Bullish / Bearish / Sideways (from latest Market Activity report).
    - **Latest Upload** — Report date of latest upload or "—".
    - **Market Sentiment** — Progress bar: Bullish % / Bearish % (derived from gainers vs losers in report).
  - Top Gainers and Top Losers have been **removed** from the dashboard; only status, latest upload, and sentiment remain in the sidebar.
- **Banner image:** Uses `public/images/banner/spritual2.png` if present, else `public/images/banner/spritual.png`. Optional: `public/assets/spiritual/dashboard-banner.webp` can be added for future use.
- **Controller:** `DashboardController` passes `marketStatus`, `latestUploadDate`, `sentimentBullishPct` (no gainers/losers passed to view).

---

## 3. Profile

- **Routes:** `GET /profile` (edit), `PUT /profile` (update). Names: `profile.edit`, `profile.update`.
- **Features:** Avatar upload, update name/email, change password. Profile is a dedicated page (not redirect to settings).
- **Storage:** Avatar stored under `storage/app/public`; `avatar_path` on `users` table. Run `php artisan storage:link` so avatars are served from `public/storage`.

---

## 4. Trading Learning Module

### 4.1 Data Model

- **Batch** → **Module** → **Lecture** → **LectureVideo** (multiple videos per lecture).
- **Tables:** `batches`, `modules`, `lectures`, `lecture_videos` (with FKs and CASCADE where appropriate). `lectures` table has no `youtube_url` (videos live in `lecture_videos`).
- **Models:** `App\Models\Batch`, `Module`, `Lecture`, `LectureVideo`. Lecture has `videos()` relationship ordered by `sort_order`.

### 4.2 Student View (`/trading-learning`)

- **Route:** `learning.index`; controller: `LearningController`.
- **UI:** Info alert with timeframe categories, exit rule, and candle wick rules (upper wick > body ⇒ bearish; lower wick > body ⇒ bullish).
- **Livewire components:**
  - **BatchList** — List batches; selecting one dispatches to LectureList.
  - **LectureList** — List lectures for selected batch; selecting one loads LectureView.
  - **LectureView** — Shows lecture title and list of videos (label, YouTube embed). Uses `wire:model.live` so selecting a lecture updates the videos list immediately.

### 4.3 Admin View (`/admin/trading-learning`)

- **Route:** `admin.learning.index`; view: `admin/learning/index`.
- **Sections:**
  - **Batches:** BatchManager — table with Edit, Delete; "+ Add batch" opens CreateBatchModal.
  - **Modules:** ModuleManager — table with Edit, Delete; "+ Add module" opens CreateModuleModal.
  - **Lectures:** AdminLectureManager — create lecture (BatchSelect, ModuleSelect with "+ Add batch/module" opening modals), lectures table with Edit, Toggle, Delete.
  - **Videos:** AdminVideoManager — Lecture dropdown (`wire:model.live="lecture_id"`), add/edit/toggle/delete videos (label, YouTube URL, optional video type, sort order). Videos list shows when a lecture is selected.
- **Select-or-create:** BatchSelect and ModuleSelect are Livewire components with `#[Modelable]`; parent uses `wire:model.live="batch_id"` and `wire:model.live="module_id"`. "+ Add batch" / "+ Add module" dispatch to CreateBatchModal / CreateModuleModal (sibling targets).
- **LectureVideo:** YouTube URL validation; index on `(lecture_id, is_active, sort_order)`.

---

## 5. Information Websites

- **Route:** `information.websites.index`; controller: `InformationWebsiteController`.
- **Features:** Cards with links; count and numbered list on cards; Card/List view toggle; shared search. Seeder includes extra links (e.g. IBEF, Trading Economics, Tijori Finance, Marketsmith India).

---

## 6. Admin Livewire Tables

- **BaseTable:** Shared base for list tables (no DataTables); pagination, search.
- **InformationLinksTable, DataSourceLinksTable, AdminUsersTable:** Used on admin index pages for information links, data source links, and admin users. Controllers no longer pass full collections; views embed Livewire table components.
- **Controls:** Shared partial for table controls (search, etc.).

---

## 7. Adminer

- **Location:** `public/adminer.php` (official single-file Adminer).
- **Usage:** Direct DB access; optional light-theme CSS. Not in git if desired; add to `.gitignore` or keep for dev only.

---

## 8. Learning Page Rules (Trading Learning Index)

Displayed in the info alert on `/trading-learning`:

- **Timeframe categories:** Intraday (1 min, 5 min), Swing (15 min–2 h), Short Term (15 trading days), Medium Term (1–3 months), Long Term (1–2 years).
- **Exit rule:** Trades can exit anytime when target or stop loss is hit.
- **Candle wick rules:**
  - If the upper wick is bigger than the body → treat as bearish (green or red).
  - If the lower wick is bigger than the body → treat as bullish (green or red).

---

## 9. Assets and Static Files

- **Dashboard banner:** `public/images/banner/spritual.png`, `public/images/banner/spritual2.png`. Optional: `public/assets/spiritual/dashboard-banner.webp`.
- **Profile avatars:** Stored via Laravel Storage (`storage/app/public`); URL via `storage:link` → `public/storage/...`.

---

## 10. Summary Table

| Feature              | Route / Location        | Notes |
|----------------------|-------------------------|-------|
| Dashboard            | `/dashboard`            | Banner (col-8) + Market Status, Latest Upload, Sentiment (col-4 sticky). No Top Gainers/Losers. |
| Profile              | `/profile`              | Avatar, name, email, password. |
| Trading Learning     | `/trading-learning`    | Batches → Modules → Lectures → Videos (student). |
| Admin Trading Learning| `/admin/trading-learning` | Batches, Modules, Lectures, Videos CRUD; select-or-create dropdowns. |
| Information Websites | `/information-websites` | Cards, list toggle, count, search. |
| Admin tables         | Admin index views       | Livewire BaseTable-based tables. |
| Adminer              | `public/adminer.php`    | Optional DB UI. |
| Layout               | Navbar + sidebar        | User dropdown (Profile, Settings, Logout); Bootstrap-only styling for dashboard. |

---

## 11. Phase 1 MVP Portal Modules

### 11.1 Live Classes

- **Student:** `/live-classes` — upcoming/live classes with Join meeting links; past classes list.
- **Admin:** Same pages as students (`/live-classes`, etc.) with **Admin Management** section at top for CRUD.
- **Dashboard widget:** Today's live class with Join button when scheduled for today.

### 11.2 Announcements

- **Student:** `/announcements` — published announcements (pinned first).
- **Admin:** `/announcements` — create/edit/delete via Admin Management section (type, pin, active checkbox in form).
- **Dashboard widget:** Latest pinned announcement preview.

### 11.3 Market Research Hub

- **Student:** `/research` — upload research (PDF, Excel, CSV); view own pending/approved uploads; browse approved research from all students.
- **Admin:** `/research` — moderation queue at top (approve, reject with reason, delete).
- **Workflow:** Upload → `pending` → admin approve/reject → approved visible to all students.
- **Categories:** FII, DII, Open Interest, Sector, Stock Research, Other.
- **Service:** `ResearchUploadService` — UUID storage under `storage/app/public/research-uploads/`.

### 11.4 Trading Calendar

- **Student:** `/calendar` — events grouped by month (expiry, RBI, results, holiday, custom).
- **Admin:** `/calendar` — CRUD via Admin Management section.

### 11.5 Charts Repository

- **Student:** `/charts` — browse/download charts by category and date.
- **Admin:** `/charts` — upload/edit/delete via Admin Management section.
- **Service:** `ChartAssetService` — storage under `storage/app/public/chart-assets/`.

### 11.6 Dashboard & Navigation

- **Dashboard row:** Today's Live Class, Latest Announcement, Latest Recorded Lecture, Quick Links (Research, Calendar, Charts).
- **Sidebar:** Live Classes, Announcements, Research Hub, Trading Calendar, Charts (student); matching admin links under Admin section.

### 11.7 Database Tables (MVP)

| Table | Purpose |
|-------|---------|
| `live_classes` | Scheduled live sessions with meeting URLs |
| `announcements` | Admin posts for students |
| `research_uploads` | Student research with moderation status |
| `calendar_events` | Trading calendar entries |
| `chart_assets` | Admin-uploaded chart files |

See `docs/MVP_ROADMAP.md` for UAT acceptance checklist.

### 11.8 My Notes (personal notebook)

- **Route:** `/my-notes` (`notes.index`) — all authenticated users.
- **Students:** Create, edit, delete private notes; read admin shared notes.
- **Admins:** Same private notebook plus **Share with all students** toggle on any note.
- **Optional:** Link a note to a lecture for context.
- **Not email:** Notes are stored in-app in `user_notes` — no external library or email needed.
- **Separate from lecture notes:** Admin lecture notes on `/trading-learning` are still read-only context on each lecture; My Notes is each user's own notebook.

---

## 12. Updated Summary Table

| Feature              | Route / Location        | Notes |
|----------------------|-------------------------|-------|
| Dashboard            | `/dashboard`            | Banner + market sidebar + MVP widgets (live class, announcement, lecture, quick links). |
| Live Classes         | `/live-classes`         | Student schedule; admin CRUD on same page. |
| Announcements        | `/announcements`        | Student list; admin CRUD on same page. |
| Research Hub         | `/research`             | Student upload + browse; admin moderation on same page. |
| Trading Calendar     | `/calendar`             | Student calendar; admin CRUD on same page. |
| Charts               | `/charts`               | Student gallery; admin upload on same page. |
| Trading Learning Admin | `/admin/trading-learning` | Batches, modules, lectures, videos CRUD. |
