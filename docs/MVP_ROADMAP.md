# Stockia Phase 1 MVP — UAT Checklist

Use this checklist for Mohit Sir acceptance testing after deployment.

## Prerequisites

- [ ] `php artisan migrate` completed
- [ ] `php artisan storage:link` run (uploads served from `/storage`)
- [ ] Admin account logged in (`admin` or `superadmin` role)
- [ ] Student account logged in (`user` role)

---

## 1. Live Classes

| Step | Action | Expected |
|------|--------|----------|
| 1.1 | Admin → Live Classes → schedule class with Zoom/Meet URL | Class saved, appears in admin table |
| 1.2 | Set scheduled date to today | — |
| 1.3 | Student → Dashboard | "Today's Live Class" widget shows title + Join button |
| 1.4 | Student → Live Classes | Upcoming class listed with Join meeting link |
| 1.5 | Click Join | Opens meeting URL in new tab |

---

## 2. Announcements

| Step | Action | Expected |
|------|--------|----------|
| 2.1 | Admin → Announcements → create announcement, pin it | Saved and visible in admin list |
| 2.2 | Student → Dashboard | Pinned announcement in widget |
| 2.3 | Student → Announcements | Full list with pinned badge first |

---

## 3. Market Research Hub

| Step | Action | Expected |
|------|--------|----------|
| 3.1 | Student → Research Hub → upload PDF/Excel (FII category) | Status `pending` in "My uploads" |
| 3.2 | Log in as another student | Pending upload from step 3.1 **not** visible in approved list |
| 3.3 | Admin → Research Moderation → Approve upload | Status changes to approved |
| 3.4 | Student → Research Hub | Approved file visible with Download link |
| 3.5 | Admin → Reject another upload with reason | Status rejected; not in public list |

---

## 4. Trading Calendar

| Step | Action | Expected |
|------|--------|----------|
| 4.1 | Admin → Trading Calendar → add expiry/RBI event | Event saved |
| 4.2 | Student → Trading Calendar | Event listed by month |

---

## 5. Charts Repository

| Step | Action | Expected |
|------|--------|----------|
| 5.1 | Admin → Charts → upload PNG or PDF | Chart appears in admin gallery |
| 5.2 | Student → Charts | Chart visible with Download |
| 5.3 | Filter by category (if set) | List filters correctly |

---

## 6. Existing Features (regression)

| Step | Action | Expected |
|------|--------|----------|
| 6.1 | Student → Trading Learning | Batches, lectures, videos still work |
| 6.2 | Admin → Trading Learning | Batch/module/lecture/video/document CRUD unchanged |
| 6.3 | Dashboard market sidebar | Status, latest upload, sentiment still display |

---

## Sign-off

| Role | Name | Date | Pass / Fail |
|------|------|------|-------------|
| Product (Mohit Sir) | | | |
| CTO | | | |
| QA | | | |

---

## Phase 2 (out of scope for this release)

- Discussion forum, lecture comments
- Live class attendance tracking
- Email / WhatsApp / push notifications
- Paid membership, quizzes, certificates
- Watchlist, trading journal
