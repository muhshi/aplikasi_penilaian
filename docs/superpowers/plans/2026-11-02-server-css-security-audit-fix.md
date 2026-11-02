# Server CSS Fix, Security Hardening & Application Optimization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Resolve server CSS loading failure (Caddyfile and asset build synchronization), fix critical path traversal vulnerability in file preview, eliminate duplicate model casts and double-hashing, optimize Filament v5 theme registration, and successfully deploy to server via SSH.

**Architecture:** 
1. Fix FrankenPHP/Caddyfile static routing so `/build/*` assets are served directly with proper MIME types.
2. Secure `/preview/{path}` route with authentication check and strict `realpath` directory containment against `storage/app/public`.
3. Normalize Filament theme loading into a single consolidated CSS entry point (`theme.css` importing `custom.css`) in `AdminPanelProvider`.
4. Fix `User` model casts in Laravel 13 style (single `casts()` method returning all cast types) and fix `SsoController` password hashing.
5. Update `deploy.sh` pipeline to ensure composer dependencies are installed before Vite build and file permissions are properly granted.
6. Verify locally, update CHANGELOG, commit, push, and execute remote deployment via SSH to `bpsdemak@10.133.21.24`.

**Tech Stack:** Laravel 13, Filament v5, FrankenPHP + Caddy, Tailwind CSS v4, Vite 7, Docker, Bash/SSH.

**Spec:** Master Audit Findings from Phase 1-7.

## Global Constraints
- Do not drop, truncate, or delete any database tables or records.
- Preserve Filament v5 and Livewire v3 conventions (e.g. `joaopaulolndev/filament-pdf-viewer:^3.0`, `Filament\Actions`, `Filament\Schemas\Components`).
- After successful verification: update Changelog in `README.md`, `git add .`, `git commit -m "..."`, and `git push`.

## Review Focus
1. Route `/preview/{path}` with path traversal attempts (e.g. `../../.env` or absolute paths) must return 403 or 404, never contents outside `storage/app/public`.
2. Static CSS requests `/build/assets/*.css` must return HTTP 200 with `Content-Type: text/css`, not HTML 404 from PHP router.
3. User model `must_change_password` and `is_active` must be properly cast to boolean.
4. SSO user creation must not double-hash passwords.
5. Deploy script on the remote server (`10.133.21.24`) must complete with all containers running and healthy.

---

### Task 1: Security Hardening on File Preview Route
**Files:**
- Modify: `routes/web.php:10-27`
- Test: Verify with `curl` or test script that `../` traversal is blocked and unauthenticated requests are prevented or strictly verified.

- [ ] **Step 1:** In `routes/web.php`, wrap `/preview/{path}` with `auth` middleware (or allow signed access if needed for public, but ensure authentication since it's an internal admin system).
- [ ] **Step 2:** Sanitize `$path` using `str_replace(['../', '..\\'], '', $path)`, resolve `realpath()`, and assert that the realpath starts with `realpath(storage_path('app/public'))`. If outside, abort with 403.
- [ ] **Step 3:** Test path traversal attempt locally and verify it returns 403/404.

---

### Task 2: Codebase Bug Fixes (User Model Casts & SSO Password)
**Files:**
- Modify: `app/Models/User.php:31-49`
- Modify: `app/Http/Controllers/Auth/SsoController.php:48`
- Modify: `resources/css/filament/admin/theme.css:1-15`
- Modify: `app/Providers/Filament/AdminPanelProvider.php:52`

- [ ] **Step 1:** In `app/Models/User.php`, delete redundant `protected $casts = [...]` property and unify all casts inside `protected function casts(): array` (`is_active` => boolean, `must_change_password` => boolean, `email_verified_at` => datetime, `password` => hashed).
- [ ] **Step 2:** In `app/Http/Controllers/Auth/SsoController.php:48`, change `bcrypt(\Illuminate\Support\Str::random(16))` to `\Illuminate\Support\Str::random(32)` so Eloquent's `hashed` cast handles hashing once without double-hashing.
- [ ] **Step 3:** In `resources/css/filament/admin/theme.css`, add `@import './custom.css';` at top.
- [ ] **Step 4:** In `app/Providers/Filament/AdminPanelProvider.php:52`, change `viteTheme(['resources/css/filament/admin/theme.css', 'resources/css/filament/admin/custom.css'])` to `viteTheme('resources/css/filament/admin/theme.css')`.
- [ ] **Step 5:** In `vite.config.js`, keep or simplify inputs so `theme.css` is built cleanly.

---

### Task 3: Fix Caddyfile Configuration for FrankenPHP Static Asset Serving
**Files:**
- Modify: `Caddyfile`

- [ ] **Step 1:** Remove invalid `order cgi-php before file_server` from `Caddyfile`.
- [ ] **Step 2:** In `Caddyfile`, add explicit handling for static extensions (`@static path *.css *.js *.svg *.png *.jpg *.jpeg *.ico *.woff *.woff2 *.webp`) with `file_server`, before fallback to `php_server`.
- [ ] **Step 3:** Ensure `@livewire` rewrite remains intact.

---

### Task 4: Fix Deployment Script & Asset Pipeline
**Files:**
- Modify: `deploy.sh`

- [ ] **Step 1:** In `deploy.sh`, ensure `composer install` is run in container or host before Vite build if needed, or ensure the build container runs `npm install && npm run build` and sets permissive permissions (`chmod -R 755 public/build`).
- [ ] **Step 2:** Ensure assets are built and `manifest.json` matches generated asset hashes.
- [ ] **Step 3:** Run local build test: `npm run build` to confirm zero Vite build errors.

---

### Task 5: Local Verification & Git Commit & Push
**Files:**
- Modify: `README.md` (Update Changelog)

- [ ] **Step 1:** Run local verification tests: `php artisan route:list`, `npm run build`.
- [ ] **Step 2:** Update `README.md` Changelog with today's date (`2026-11-02`) and bullet points for all security, bugfix, and server optimization changes.
- [ ] **Step 3:** Run `git add .`, `git commit -m "fix: resolve server css loading, patch preview traversal, fix user casts and deploy pipeline"`, and `git push origin master`.

---

### Task 6: Remote Deployment via SSH & Verification
**Files:**
- Server: `bpsdemak@10.133.21.24:~/apps/aplikasi_penilaian`

- [ ] **Step 1:** SSH into `bpsdemak@10.133.21.24` and run the updated `deploy.sh`.
- [ ] **Step 2:** Verify container status: `docker ps`.
- [ ] **Step 3:** Test HTTP response of CSS asset on `http://localhost:8008/build/assets/theme-...css` to verify it returns `HTTP/1.1 200 OK` with `Content-Type: text/css`.
- [ ] **Step 4:** Test `/admin/login` page to ensure Filament theme renders properly without missing CSS.
