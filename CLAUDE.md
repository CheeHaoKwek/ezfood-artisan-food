# EzFood — CLAUDE.md

## Project Overview

EzFood is a mobile-first food ordering and subscription platform targeting **factories and condominiums** in Malaysia. Workers and tenants scan a QR code to subscribe and select meals. The system is multi-tenant: each factory or condo has its own QR configuration managed via a CMS.

This is a Laravel project. Stack: **Laravel 13, Blade + Bootstrap 5, MySQL/SQLite**.

---

## Domain Glossary

| Term | Meaning |
|---|---|
| **Outlet** | A factory or condo registered on EzFood |
| **QR Config** | Operation settings tied to an outlet's QR code (days, hours, meal slots, cut-off) |
| **Subscriber** | A worker or tenant who has scanned a QR and has an active weekly subscription |
| **Meal Slot** | A named time slot within a day: `breakfast`, `lunch`, or `dinner` |
| **Weekly Plan** | A subscription cycle — 7 days, tied to outlet's configured operation days |
| **Cut-off Time** | The deadline per meal slot after which no new selections are accepted for that slot |
| **Consolidated Order** | The aggregated meal selections passed to logistics after cut-off |
| **CMS** | The admin-facing interface used by EzFood staff to configure outlets and QR codes |

---

## Business Workflow (5 Steps)

### Step 1 — Outlet QR Setup (CMS)
- EzFood staff configures the outlet in the CMS. No charge to outlet at this stage.
- Configuration collected:
  - **Operation days**: Mon–Fri / Mon–Sat / Mon–Sun
  - **Operation hours**: standard hours or 24-hour
  - **Meals per day**: 1 meal/day or multiple (breakfast + lunch + dinner)
  - **Meal time slots**: which of breakfast / lunch / dinner are active
  - **Cut-off time**: per slot or per day
  - **Delivery location**: address or zone info

### Step 2 — QR Code Placement
- After CMS setup, the outlet admin receives the QR code.
- QR is printed and placed at physical locations (factory wall, condo lobby, canteen, notice board).
- The QR encodes the outlet's unique config identifier.

### Step 3 — Worker / Tenant Scans QR
- Scanning opens the EzFood **mobile web app**.
- System reads the QR config to determine operation days, meal schedule, and subscription rules.
- **Auth flow**:
  - First-time user → sign up
  - Returning user → sign in (session persisted via browser cookie — no re-login needed on same phone/browser)
- **Post-auth routing**:
  - No active subscription → redirect to **subscription flow** (options depend on outlet's config)
  - Active subscription exists → redirect to **menu/meal selection page**

### Step 4 — Meal Selection and Cut-off
- Users select meals based on the outlet's schedule.
- **1 meal/day config**: worker selects one meal per day; must scan again the next day.
- **Multiple meals/day config**: separate selection UI for breakfast, lunch, and dinner.
- After cut-off time, the system **consolidates** all selections for that slot and passes the order data to logistics.

### Step 5 — Meal Delivery
- Logistics team prepares and dispatches meals based on consolidated orders.
- Workers / tenants receive meals per their selected schedule and delivery arrangement.

---

## Key Business Rules

- The weekly meal plan structure is the **default subscription unit**.
- Default: weekly subscription, 1 meal per day.
- Some outlets may configure multiple meals per day — the subscription flow and selection UI must adapt dynamically to the outlet's QR config.
- Cut-off enforcement is **per meal slot**, not just per day.
- A user with an active subscription should **never** be shown the subscription flow again.
- Login session must persist via cookie — minimise re-auth friction on mobile.
- QR config is the **source of truth** for what a subscriber can see and select.

---

## Architecture Notes

- **Multi-tenant via QR config**: all user-facing pages are scoped to an outlet's config loaded from the QR identifier.
- **CMS is separate** from the mobile web app — admin routes are behind auth middleware.
- Subscription state determines routing on every QR scan.
- Cut-off logic must be timezone-aware (use Carbon with the outlet's configured timezone).

---

## Common Artisan Commands

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
php artisan make:model ModelName -mcr   # model + migration + controller + resource
php artisan make:seeder SeederName
php artisan schedule:work               # run scheduler locally
php artisan route:list                  # inspect all routes
```

---

## Conventions

- Controllers: one per domain area (`OutletController`, `SubscriptionController`, `MealController`, `OrderController`)
- Blade views: `resources/views/cms/` for admin, `resources/views/app/` for mobile web
- All time comparisons use **Carbon** with the outlet's configured timezone
- Bootstrap 5 for all UI — no Tailwind
- Keep business logic in **Service classes**, not controllers
- Mobile web views must be responsive — workers scan on phones
