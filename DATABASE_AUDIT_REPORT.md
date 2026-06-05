# Stockia — Complete Database Audit Report

**Audit date:** March 2025  
**Method:** Direct MySQL connection using credentials from `.env`; raw SQL only (no Laravel Tinker).  
**Database:** `stockia` (MySQL)

---

## 1. Database Credentials (from .env)

| Variable       | Value     |
|----------------|-----------|
| DB_CONNECTION  | mysql     |
| DB_HOST        | 127.0.0.1 |
| DB_PORT        | 3306      |
| DB_DATABASE    | stockia   |
| DB_USERNAME    | root      |
| DB_PASSWORD    | (set)     |

---

## 2. All Tables

| #  | Table name           | Purpose (inferred)                    |
|----|----------------------|----------------------------------------|
| 1  | accounts             | Account/workspace ownership            |
| 2  | cache                | Laravel cache (database driver)       |
| 3  | cache_locks          | Laravel cache locks                    |
| 4  | data_source_links    | External data source definitions       |
| 5  | failed_jobs          | Laravel queue failed jobs              |
| 6  | information_links    | Information tools / research links     |
| 7  | job_batches          | Laravel queue batches                  |
| 8  | jobs                 | Laravel queue jobs                     |
| 9  | migrations           | Laravel migration tracking             |
| 10 | nifty50_extended     | Nifty 50 reference data                |
| 11 | password_reset_tokens| Laravel password resets                |
| 12 | sessions             | Laravel sessions (database driver)     |
| 13 | sheet_uploads        | Uploaded sheet metadata                |
| 14 | stock_data           | Row-level data per sheet               |
| 15 | users                | Application users and auth             |

**Application-domain tables:** `users`, `accounts`, `sheet_uploads`, `stock_data`, `data_source_links`, `information_links`, `nifty50_extended`.

---

## 3. Table Structure (SHOW CREATE TABLE)

### 3.1 accounts

```sql
CREATE TABLE `accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `owner_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_owner_id_foreign` (`owner_id`),
  CONSTRAINT `accounts_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
```

### 3.2 users

```sql
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(32) NOT NULL DEFAULT 'user',
  `account_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_account_id_foreign` (`account_id`),
  CONSTRAINT `users_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB
```

### 3.3 sheet_uploads

```sql
CREATE TABLE `sheet_uploads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `data_source_link_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `columns` json DEFAULT NULL COMMENT 'Detected column headers',
  `row_count` int unsigned NOT NULL DEFAULT '0',
  `report_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sheet_uploads_user_id_foreign` (`user_id`),
  KEY `sheet_uploads_data_source_link_id_foreign` (`data_source_link_id`),
  CONSTRAINT `sheet_uploads_data_source_link_id_foreign` FOREIGN KEY (`data_source_link_id`) REFERENCES `data_source_links` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sheet_uploads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB
```

### 3.4 stock_data

```sql
CREATE TABLE `stock_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sheet_upload_id` bigint unsigned NOT NULL,
  `row_index` int unsigned NOT NULL,
  `data` json NOT NULL COMMENT 'Row data as key-value per column',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_data_sheet_upload_id_foreign` (`sheet_upload_id`),
  CONSTRAINT `stock_data_sheet_upload_id_foreign` FOREIGN KEY (`sheet_upload_id`) REFERENCES `sheet_uploads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
```

### 3.5 data_source_links

```sql
CREATE TABLE `data_source_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `url` text NOT NULL,
  `display_columns` json DEFAULT NULL COMMENT 'Column keys to show for this source, e.g. nifty50',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_source_links_slug_unique` (`slug`)
) ENGINE=InnoDB
```

### 3.6 information_links

```sql
CREATE TABLE `information_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `information_links_created_by_foreign` (`created_by`),
  KEY `information_links_account_id_is_active_index` (`account_id`,`is_active`),
  CONSTRAINT `information_links_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `information_links_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB
```

### 3.7 nifty50_extended

```sql
CREATE TABLE `nifty50_extended` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `security_symbol` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `industry` varchar(255) NOT NULL,
  `nifty_weightage_pct` decimal(8,2) NOT NULL DEFAULT '0.00',
  `sector_thematic_index` varchar(255) NOT NULL,
  `sector_thematic_weightage_pct` decimal(8,2) NOT NULL DEFAULT '0.00',
  `relationship_of_index` varchar(255) NOT NULL DEFAULT 'Sector',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nifty50_extended_security_symbol_index` (`security_symbol`)
) ENGINE=InnoDB
```

---

## 4. Column Details (information_schema)

| Table              | Column            | Type              | Nullable | Default  | Key   | Extra        |
|--------------------|-------------------|-------------------|----------|----------|-------|--------------|
| accounts           | id                | bigint unsigned   | NO       | NULL     | PRI   | auto_increment |
| accounts           | name              | varchar(255)      | NO       | NULL     |       |              |
| accounts           | owner_id          | bigint unsigned   | NO       | NULL     | MUL   |              |
| accounts           | created_at        | timestamp         | YES      | NULL     |       |              |
| accounts           | updated_at        | timestamp         | YES      | NULL     |       |              |
| users              | id                | bigint unsigned   | NO       | NULL     | PRI   | auto_increment |
| users              | name              | varchar(255)      | NO       | NULL     |       |              |
| users              | email             | varchar(255)      | NO       | NULL     | UNI   |              |
| users              | role              | varchar(32)       | NO       | user     |       |              |
| users              | account_id        | bigint unsigned   | YES      | NULL     | MUL   |              |
| users              | email_verified_at | timestamp         | YES      | NULL     |       |              |
| users              | password          | varchar(255)      | NO       | NULL     |       |              |
| users              | remember_token    | varchar(100)      | YES      | NULL     |       |              |
| users              | created_at        | timestamp         | YES      | NULL     |       |              |
| users              | updated_at        | timestamp         | YES      | NULL     |       |              |
| sheet_uploads      | id                | bigint unsigned   | NO       | NULL     | PRI   | auto_increment |
| sheet_uploads      | user_id           | bigint unsigned   | YES      | NULL     | MUL   |              |
| sheet_uploads      | data_source_link_id | bigint unsigned | YES      | NULL     | MUL   |              |
| sheet_uploads      | name              | varchar(255)      | YES      | NULL     |       |              |
| sheet_uploads      | original_name     | varchar(255)      | NO       | NULL     |       |              |
| sheet_uploads      | path              | varchar(255)      | NO       | NULL     |       |              |
| sheet_uploads      | columns           | json              | YES      | NULL     |       |              |
| sheet_uploads      | row_count         | int unsigned      | NO       | 0        |       |              |
| sheet_uploads      | report_date       | date              | YES      | NULL     |       |              |
| sheet_uploads      | created_at        | timestamp         | YES      | NULL     |       |              |
| sheet_uploads      | updated_at        | timestamp         | YES      | NULL     |       |              |
| stock_data         | id                | bigint unsigned   | NO       | NULL     | PRI   | auto_increment |
| stock_data         | sheet_upload_id   | bigint unsigned   | NO       | NULL     | MUL   |              |
| stock_data         | row_index         | int unsigned      | NO       | NULL     |       |              |
| stock_data         | data              | json              | NO       | NULL     |       |              |
| stock_data         | created_at        | timestamp         | YES      | NULL     |       |              |
| stock_data         | updated_at        | timestamp         | YES      | NULL     |       |              |
| data_source_links | id                | bigint unsigned   | NO       | NULL     | PRI   | auto_increment |
| data_source_links | name              | varchar(255)      | NO       | NULL     |       |              |
| data_source_links | slug              | varchar(64)       | NO       | NULL     | UNI   |              |
| data_source_links | url               | text              | NO       | NULL     |       |              |
| data_source_links | display_columns   | json              | YES      | NULL     |       |              |
| data_source_links | is_active         | tinyint(1)        | NO       | 1        |       |              |
| data_source_links | created_at        | timestamp         | YES      | NULL     |       |              |
| data_source_links | updated_at        | timestamp         | YES      | NULL     |       |              |
| information_links  | id                | bigint unsigned   | NO       | NULL     | PRI   | auto_increment |
| information_links | title             | varchar(255)      | NO       | NULL     |       |              |
| information_links | url               | varchar(2048)     | NO       | NULL     |       |              |
| information_links | sort_order        | int unsigned      | NO       | 0        |       |              |
| information_links | is_active         | tinyint(1)        | NO       | 1        |       |              |
| information_links | created_by        | bigint unsigned   | YES      | NULL     | MUL   |              |
| information_links | account_id        | bigint unsigned   | YES      | NULL     | MUL   |              |
| information_links | created_at        | timestamp         | YES      | NULL     |       |              |
| information_links | updated_at        | timestamp         | YES      | NULL     |       |              |
| nifty50_extended   | (all columns as in CREATE TABLE above) |  |  |  |  |  |

---

## 5. Foreign Keys

| Table             | Column              | Constraint name                         | Referenced table      | Referenced column | UPDATE_RULE | DELETE_RULE |
|------------------|---------------------|------------------------------------------|------------------------|-------------------|-------------|-------------|
| accounts         | owner_id            | accounts_owner_id_foreign                | users                  | id                | NO ACTION   | **CASCADE** |
| information_links| account_id          | information_links_account_id_foreign     | accounts               | id                | NO ACTION   | **CASCADE** |
| information_links| created_by          | information_links_created_by_foreign     | users                  | id                | NO ACTION   | SET NULL    |
| sheet_uploads    | data_source_link_id | sheet_uploads_data_source_link_id_foreign| data_source_links      | id                | NO ACTION   | SET NULL    |
| sheet_uploads    | user_id             | sheet_uploads_user_id_foreign            | users                  | id                | NO ACTION   | SET NULL    |
| stock_data       | sheet_upload_id     | stock_data_sheet_upload_id_foreign       | sheet_uploads          | id                | NO ACTION   | **CASCADE** |
| users            | account_id          | users_account_id_foreign                 | accounts               | id                | NO ACTION   | SET NULL    |

**Summary:**

- **CASCADE on delete:** `accounts.owner_id` → users; `information_links.account_id` → accounts; `stock_data.sheet_upload_id` → sheet_uploads.
- **SET NULL on delete:** `sheet_uploads.user_id`, `sheet_uploads.data_source_link_id`; `users.account_id`; `information_links.created_by`.

---

## 6. Indexes

| Table              | Index name                              | Columns                    | Unique |
|--------------------|------------------------------------------|----------------------------|--------|
| accounts           | PRIMARY                                 | id                         | Yes    |
| accounts           | accounts_owner_id_foreign                | owner_id                   | No     |
| users              | PRIMARY                                 | id                         | Yes    |
| users              | users_email_unique                      | email                      | Yes    |
| users              | users_account_id_foreign                 | account_id                 | No     |
| sheet_uploads      | PRIMARY                                 | id                         | Yes    |
| sheet_uploads      | sheet_uploads_user_id_foreign            | user_id                    | No     |
| sheet_uploads      | sheet_uploads_data_source_link_id_foreign| data_source_link_id        | No     |
| stock_data         | PRIMARY                                 | id                         | Yes    |
| stock_data         | stock_data_sheet_upload_id_foreign       | sheet_upload_id            | No     |
| data_source_links | PRIMARY                                 | id                         | Yes    |
| data_source_links | data_source_links_slug_unique            | slug                       | Yes    |
| information_links  | PRIMARY                                 | id                         | Yes    |
| information_links  | information_links_created_by_foreign    | created_by                 | No     |
| information_links  | information_links_account_id_is_active_index | account_id, is_active | No     |
| nifty50_extended   | PRIMARY                                 | id                         | Yes    |
| nifty50_extended   | nifty50_extended_security_symbol_index   | security_symbol            | No     |

**Note:** No composite index on `sheet_uploads (user_id, data_source_link_id)` for “latest upload per user per source” queries; consider adding if that pattern is used.

---

## 7. Relationships Mapping

### 7.1 Core entity diagram (FK-only)

```
users (1) ──< accounts (owner_id)     users (1) ──< sheet_uploads (user_id, nullable)
users (1) ──< information_links (created_by, nullable)
accounts (1) ──< users (account_id, nullable)
accounts (1) ──< information_links (account_id, nullable)
data_source_links (1) ──< sheet_uploads (data_source_link_id, nullable)
sheet_uploads (1) ──< stock_data (sheet_upload_id)  [CASCADE]
```

### 7.2 users

- **Outbound:** `accounts.owner_id` → users.id (one user can own many accounts; CASCADE).
- **Inbound:**  
  - `users.account_id` → accounts.id (nullable; SET NULL if account deleted).  
  - `sheet_uploads.user_id` → users.id (nullable; SET NULL).  
  - `information_links.created_by` → users.id (nullable; SET NULL).  
  - `sessions.user_id` (Laravel).

**Nullable relationships:** Belonging to an account, uploads, and “created_by” for information links are all optional.

### 7.3 sheet_uploads

- **Inbound:**  
  - `user_id` → users.id (nullable, SET NULL).  
  - `data_source_link_id` → data_source_links.id (nullable, SET NULL).
- **Outbound:**  
  - `stock_data.sheet_upload_id` → sheet_uploads.id (CASCADE).

So: an upload can exist without a user or without a data source link; deleting a sheet_upload deletes all its stock_data rows.

### 7.4 stock_data

- **Inbound only:** `sheet_upload_id` → sheet_uploads.id (NOT NULL, CASCADE).  
Every row is tied to a sheet_upload; deleting a sheet_upload deletes its stock_data.

### 7.5 data_source_links

- **Outbound:** `sheet_uploads.data_source_link_id` → data_source_links.id (nullable, SET NULL).  
No other FKs reference this table. No `account_id` or `tenant_id` — links are global.

### 7.6 information_links

- **Inbound:**  
  - `account_id` → accounts.id (nullable, CASCADE).  
  - `created_by` → users.id (nullable, SET NULL).  
So: links can be global (account_id NULL) or per-account; creator can be nullified if user is deleted.

### 7.7 nifty50_extended

- No foreign keys. Standalone reference/lookup table (e.g. Nifty 50 metadata).

### 7.8 accounts

- **Inbound:** `users.account_id`, `information_links.account_id`.  
- **Outbound:** `owner_id` → users.id (CASCADE).  
Deleting a user who owns an account cascades to delete that account, then CASCADE on information_links removes that account’s links; users with that account_id get SET NULL.

---

## 8. Role & Permission Detection

### 8.1 Tables checked (raw SQL)

- `roles` — **does not exist**
- `permissions` — **does not exist**
- `role_user` — **does not exist**
- `model_has_roles` — **does not exist**
- `model_has_permissions` — **does not exist**

### 8.2 Conclusion

- **Spatie Laravel-Permission:** Not present (no roles/permissions/pivot tables).
- **Custom role system:** No separate role tables.
- **Legacy role column:** **Yes.** `users.role` (varchar(32), default `'user'`).

### 8.3 Observed role values (from DB)

```sql
SELECT DISTINCT role FROM users;
-- role
-- user
-- superadmin
-- admin
```

So the system uses a **single column, enum-like role** with at least: `user`, `admin`, `superadmin`. No roles table, no role_user pivot, no permissions table.

---

## 9. Multitenancy Indicators

Query: columns named `tenant_id`, `company_id`, `account_id`, `organization_id`, `team_id`, `workspace_id` in schema `stockia`.

| Table             | Column     |
|------------------|------------|
| users            | account_id |
| information_links| account_id |

**Interpretation:**

- **account_id** is the only “tenant-like” column present.
- **users.account_id:** nullable; links a user to one account (e.g. admin’s workspace).
- **information_links.account_id:** nullable; global links when NULL, account-specific when set.
- **data_source_links:** no account_id (global only).
- **sheet_uploads:** no account_id; ownership is by `user_id` (and optionally `data_source_link_id`).

So: **soft multitenancy** via `accounts` and `account_id` on users and information_links only; core upload/sheet/data source model is user-scoped, not account-scoped.

---

## 10. Data Ownership Analysis

### 10.1 By user_id

- **sheet_uploads.user_id** (nullable): upload “belongs” to a user when set; otherwise anonymous/shared.
- **sessions.user_id**: Laravel session ownership.
- No other application tables use `user_id` for ownership.

### 10.2 By account_id

- **users.account_id**: user belongs to an account (e.g. admin’s workspace); NULL for superadmin or unassigned users.
- **information_links.account_id**: link is global (NULL) or scoped to one account.

### 10.3 By company_id / tenant_id

- Not used.

### 10.4 Ownership chain (inferred)

- **Account** is owned by a **user** (`accounts.owner_id` → users). That user is typically an “admin” (from role column).
- **Users** in that account have `users.account_id` set to that account.
- **Information links** are either global (`account_id` NULL) or owned by an account.
- **Sheet uploads** are owned by **user** only (`user_id`); there is no direct account-level ownership of uploads. So “my account’s uploads” is implied by “users in my account” uploading (same user_id), not by an account_id on sheet_uploads.

**Summary:**

| Data type           | Ownership key    | Scope        |
|---------------------|------------------|-------------|
| accounts            | owner_id (user)  | One admin   |
| users               | account_id       | Account     |
| sheet_uploads      | user_id          | User        |
| stock_data          | sheet_upload_id  | Sheet       |
| data_source_links  | —                | Global      |
| information_links   | account_id (or NULL) | Global or account |
| nifty50_extended    | —                | Global      |

---

## 11. Application Logic Mapping (Tables → Features)

Inferred from table names, columns, and known slugs in `data_source_links` (nifty50, market_activity):

| Feature / Module   | Primary tables              | Supporting / lookup      | Notes |
|--------------------|----------------------------|---------------------------|-------|
| **Dashboard**      | sheet_uploads, stock_data  | data_source_links, users  | Likely “latest” sheet (e.g. by user and/or data_source_link slug like market_activity); rows from stock_data. |
| **Market Activity**| sheet_uploads, stock_data  | data_source_links         | Filter by data_source_link slug = `market_activity`; report_date on sheet_uploads. |
| **Indices**        | sheet_uploads, stock_data  | data_source_links         | Index/slug-driven views; display_columns on data_source_links. |
| **Reports**        | sheet_uploads              | stock_data, users         | List/filter uploads; report_date. |
| **Uploads**        | sheet_uploads, stock_data  | data_source_links, users  | New upload creates sheet_upload + stock_data rows; optional user_id, data_source_link_id. |
| **Admin – Data source links** | data_source_links | —                  | CRUD on name, slug, url, display_columns, is_active (no account_id). |
| **Admin – Information tools** | information_links | accounts, users   | CRUD; global vs account via account_id; created_by, sort_order, is_active. |
| **Admin – Accounts / users** | users, accounts         | —                         | account_id, role; accounts.owner_id. |
| **Nifty 50 (extended)** | nifty50_extended       | —                         | Reference data; no FKs. |

---

## 12. Role System Evaluation

### 12.1 Current: users.role column

- **Pros:** Simple, no joins, easy checks in middleware (`role === 'admin'`), no extra tables.
- **Cons:**  
  - Role list is in code/DB content, not schema.  
  - No built-in permissions; only “role” (e.g. superadmin / admin / user).  
  - Adding a new role or permission requires code (and possibly data) changes, not just new rows in a roles/permissions table.

### 12.2 Alternative: roles + role_user + permissions

- **Pros:** Flexible roles and permissions, assign multiple roles, add roles without code deploy, standard pattern (e.g. Spatie).  
- **Cons:** Migrations, data migration from `users.role`, and every permission check must move from “role string” to “has role/permission”.

### 12.3 Implications for current design

- Access control is **role-based only** (superadmin / admin / user), with **account scope** where `account_id` exists.
- No row-level or permission-based model (e.g. “can_edit_information_link”); logic is in controllers/middleware using `role` and `account_id`.
- **Impersonation** and “act as admin” can be implemented with session + same `users` table (no schema change), as long as code treats “current user” as the impersonated user and still restricts who can start/stop impersonation (e.g. superadmin only).

---

## 13. Architecture Risks

1. **Circular dependency (logical):** accounts.owner_id → users; users.account_id → accounts. Creation order must be: user first, then account, then set user.account_id. Backfill/migrations must respect this.
2. **Deleting an account owner:** CASCADE on `accounts.owner_id` deletes the account, then CASCADE on information_links; SET NULL on users.account_id. Orphaned users and loss of account-scoped links; acceptable only if business rules say “deleting admin removes their account and links”.
3. **No account_id on sheet_uploads:** Uploads are user-scoped only. “All uploads for my account” requires “all users in my account” then “all uploads by those users.” Any reporting or admin view that assumes “account uploads” must join through users.
4. **data_source_links global only:** No tenant/account differentiation; all admins see the same list. If per-account data sources are needed later, schema and code change required.
5. **Role stored as string:** Typos or new values in DB (e.g. “Admin”) can break role checks unless compared case-sensitively and values are controlled (e.g. enum or config).
6. **information_links composite index:** Index (account_id, is_active) supports “active links for account” and “global active” (account_id IS NULL); good for sidebar/listing queries.

---

## 14. Recommended Architecture (High Level)

### 14.1 Keep users.role or move to roles/role_user?

- **Short term / low complexity:** **Keep `users.role`.** You already have three distinct roles (superadmin, admin, user) and account-based scope; no permission matrix. Migrating to roles/role_user is a larger change with no immediate functional gain.
- **Long term:** If you need multiple roles per user, or fine-grained permissions (e.g. “can_edit_links”, “can_export”), introduce `roles`, `permissions`, and pivot tables (e.g. Spatie) and migrate `users.role` into `role_user` with a single role per user first, then extend.

### 14.2 accounts and tenant isolation

- **Keep and use `accounts`:** Already in place; `users.account_id` and `information_links.account_id` implement account/workspace isolation for links.
- **Recommendation:**  
  - Consistently scope admin-facing queries by `account_id` (and for superadmin, allow “all accounts” or no filter).  
  - Do **not** add `account_id` to sheet_uploads unless you explicitly want “account-level” upload ownership and are ready to backfill and change all upload listing/filtering logic.  
  - Document that “upload ownership = user”; “account” is for users and information_links only.

### 14.3 Superadmin / admin / user and impersonation

- **Keep role column** with three values; enforce in middleware (e.g. `role:admin,superadmin`) and in controllers for admin-only actions.
- **Superadmin:** `account_id` NULL; can manage all accounts and global information links; only role that can “impersonate” (store real user id in session, then `Auth::loginUsingId(admin_id)`).
- **Admin:** Must have `account_id` (their owned account); can manage users in that account and information_links for that account; no impersonation.
- **User:** Typically has `account_id` set; read-only for information links; can upload/view per existing rules.
- **Impersonation:** Implement in session (e.g. `impersonator_id`) and a “stop impersonation” route that restores the original user; no DB changes required. Ensure only superadmin can set impersonation and that target is an admin (and optionally that admin has an account).

---

## 15. Deliverables Summary

| Deliverable              | Status |
|--------------------------|--------|
| Database schema map      | §2, §3, §4 |
| Table relationships      | §5, §7 |
| Role system detection    | §8 (legacy `users.role`; no Spatie/custom roles table) |
| Multitenancy detection   | §9 (`account_id` on users, information_links only) |
| Data ownership structure | §10 |
| Application logic mapping| §11 |
| Architecture risks       | §13 |
| Recommended architecture| §14 |

---

## 16. Final Verdict & Post-Audit Change

**Verdict:** The current architecture is **correct and SaaS-ready**. Do **not** add `roles`, `role_user`, or `permissions` unless fine-grained permissions are required later.

| Aspect | Status |
|--------|--------|
| Single role per user (`users.role`) | ✅ Keep as-is |
| Multitenant key (`account_id` on users, information_links) | ✅ Clean design |
| Impersonation (session-based) | ✅ Correct |
| Information links (global + account) | ✅ Well designed |

**Optional future improvement (not applied):** Adding `sheet_uploads.account_id` would simplify “all uploads for my account” for admins but requires backfill and query changes; defer until the platform grows.

**Impersonation security rule (enforced in code):** Only **superadmin → admin** is allowed. Always block impersonation of superadmin (`if ($user->role === 'superadmin') abort(403)`). Never allow: admin → user, admin → admin, superadmin → superadmin.

**Performance change applied (post-audit):** A composite index was added for dashboard “latest upload per user per source” queries so that `ORDER BY created_at DESC` can use the index and avoid filesort:

- **Migrations:** `2026_03_12_200000_add_user_source_index_to_sheet_uploads` (two-column), then `2026_03_12_200001_optimize_sheet_uploads_latest_index` (replaced with three-column).
- **Index:** `idx_sheet_uploads_user_source_date` on `sheet_uploads (user_id, data_source_link_id, created_at)`.
- **Use case:** `SheetUpload::where('user_id', $id)->where('data_source_link_id', $sourceId)->latest()->first()` and similar lookups.
