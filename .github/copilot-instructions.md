# Copilot Instructions for Simple Akunting v5

## Project Overview
- **Domain:** Integrated accounting system for multi-business (Trading, Manufacturing, Agriculture/PSAK 69, Cooperative/Loan & Savings)
- **Framework:** Laravel 12.x (PHP 8.3+), MySQL 8.0+, Bootstrap 5.3, Vite
- **Modes:** Supports both single-tenant and multi-tenant (subdomain, DB-per-tenant) via Stancl Tenancy

## Key Architecture & Patterns
- **Controllers:** All business logic in `app/Http/Controllers/` (e.g., `PenjualanController`, `ApprovalController`)
- **Models:** Eloquent models in `app/Models/` (e.g., `Akun`, `Pinjaman`, `Persediaan`)
- **Services:** Domain logic helpers in `app/Services/` (e.g., `PinjamanCalculator` for loan calculations)
- **Traits:** Shared logic in `app/Traits/` (e.g., `CheckSaldoTrait` for account balance checks)
- **Providers:** Custom providers in `app/Providers/` (notably `TenancyServiceProvider` for multi-tenancy events)
- **Views:** Blade templates in `resources/views/` (grouped by domain: `accounting/`, `manufacturing/`, etc.)
- **Routes:**
  - `routes/web.php`: Central/single-tenant routes
  - `routes/tenant.php`: Tenant-specific routes (auto-loaded in multi-tenant mode)
- **Database:**
  - Migrations in `database/migrations/` (with `tenant/` subfolder for tenant DB schema)
  - Seeders in `database/seeders/` (notably `CoaTemplateSeeder` for COA templates)

## Multi-Tenancy
- Controlled by `config/app.php` (`tenancy_enabled` flag, set via `.env`)
- When enabled, tenant routes are loaded and DB isolation is enforced
- Tenant lifecycle events (create, migrate, seed, delete) are handled in `TenancyServiceProvider`

## Approval Workflow
- Approval logic for modules (e.g., loans) in `ApprovalController` and `ApprovalHistory` model
- Status transitions: `pending_approval` → `approved`/`rejected` (see `ApprovalController`)

## Developer Workflows
- **Install:** `composer install` + `npm install`
- **Env setup:** Copy `.env.example` to `.env`, set DB and tenancy config
- **Migrate:** `php artisan migrate` (central), tenant DBs auto-migrated on creation
- **Seed:** `php artisan db:seed --class=CoaTemplateSeeder` (COA per business type)
- **Build assets:** `npm run build` (or `npm run dev` for hot reload)
- **Run:** `php artisan serve`
- **Testing:** Use `phpunit` (see `tests/`)

## Project-Specific Conventions
- **COA (Chart of Accounts):** Seeded per business type, see `CoaTemplateSeeder`
- **Role-based access:** Route groups use `role:` middleware for access control
- **Auto-journal:** Transactions (sales, purchases, etc.) auto-generate journal entries
- **Approval:** All loan disbursements require approval (see `ApprovalController`)
- **Data isolation:** All tenant data is isolated at DB level in multi-tenant mode

## Integration Points
- **Stancl Tenancy:** For multi-tenant DB and subdomain routing
- **Vite:** For asset bundling (see `vite.config.js`)
- **Mail/Queue:** Configurable via `.env` and `config/`

## Examples
- To add a new business module: create Controller, Model, migration, Blade views, and register routes in `tenant.php`
- To extend approval: update `ApprovalController` and `ApprovalHistory` logic

## References
- See `README.md` for business flows and setup
- See `config/tenancy.php` and `TenancyServiceProvider` for tenancy logic
- See `database/seeders/CoaTemplateSeeder.php` for COA structure

---
For questions, contact the maintainers listed in `README.md`.
