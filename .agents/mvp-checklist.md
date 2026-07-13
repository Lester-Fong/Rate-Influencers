# RateInfluencers MVP Checklist

Last audited: 2026-07-13

## Status legend

- `[x]` Verified complete at the current level of scope.
- `[ ]` Not complete. A note beginning with **Partial** describes reusable work that already exists.

## Current baseline

- [x] Read project guidance and compare the implementation with the target MVP architecture.
- [x] Check repository structure and Git status.
- [x] Run backend tests: 2 tests and 2 assertions pass.
- [x] Run frontend production build: Vite build passes.
- [x] Confirm database connectivity: MySQL is reachable and all 8 migrations have run.
- [x] Confirm Laravel route discovery: 6 non-vendor application routes load.

> Verification limitation: the two passing backend tests are Laravel skeleton examples. Authentication, influencer, review, authorization, and API-contract behavior currently have no automated coverage.

## Phase 1 — Project setup and safety

- [x] Review the current codebase and update this checklist.
- [x] Check Git status and identify uncommitted work.
- [x] Run backend tests.
- [x] Run frontend build.
- [x] Confirm database and migration status.
- [ ] Stabilize the current dirty working tree before feature work (review and intentionally commit/stash existing auth, Sanctum, migration, lockfile, and `.agents` changes).
- [ ] Add meaningful backend feature-test coverage before extending the API.

## Phase 2 — Authentication (recommended first implementation task)

- [ ] Move auth endpoints to the required `/api/v1/auth/*` contract.
  - **Partial:** Working legacy endpoints exist at `/api/login` and `/api/logout`.
- [ ] Add `GET /api/v1/auth/me` behind `auth:sanctum`.
- [ ] Verify login, current-admin, logout, unauthenticated, and invalid-credential behavior with Laravel feature tests.
  - **Partial:** The backend currently verifies the seeded administrator password, creates a session, regenerates the session ID, and invalidates it on logout.
- [ ] Remove the `sessionStorage` `api-token`/`authenticated` marker.
- [ ] Make the Pinia auth store own `admin`, initialization/loading state, `fetchCurrentAdmin()`, login, and logout.
  - **Partial:** The current store initializes CSRF and supports login/logout, but administrator state is memory-only and cannot be restored after refresh.
- [ ] Replace the Vue router guard with a server-session-aware guard that does not call `next()` twice or clear auth on public navigation.
- [ ] Add an accessible logout action to the administrator UI.

### Expected files for the first implementation task

- `backend/routes/api.php`
- `backend/app/Http/Controllers/AdministratorController.php`
- `backend/app/Services/AdminService.php` (only if auth business logic needs adjustment)
- `backend/app/Http/Resources/AdministratorResource.php` (new, if a stable admin response shape is introduced)
- `backend/tests/Feature/AuthenticationTest.php` (new)
- `frontend/src/stores/auth.js`
- `frontend/src/router/index.js`
- `frontend/src/views/auth/LoginView.vue`
- `frontend/src/components/Sidebar.vue` (logout control)

## Phase 3 — Align the database and domain model

- [ ] Choose and document a safe forward-migration strategy from legacy `comments` to MVP `reviews` without editing migrations that have already run.
- [ ] Align `influencers` with the MVP schema: add `bio` and `review_count`; make required/unique fields deliberate.
  - **Partial:** The current table already has name, slug, profile picture, rating, and social links.
- [ ] Align reviews with the MVP schema: `reviewer_name`, `rating`, `review`, and `status` (`pending`, `approved`, `rejected`).
  - **Partial:** The legacy `comments` table has equivalent name/rating/comment data and a boolean `is_approved`, but cannot represent rejected separately from pending.
- [ ] Rename or replace `Comment` domain classes with `Review` classes consistently.
- [ ] Align `Administrator` fillable/casts with the actual `fullname`, email, and password columns.
- [ ] Add database constraints for administrator email and influencer slug uniqueness.
- [ ] Decide whether unused default `User`, password-reset, database-session, and personal-token artifacts should remain.

## Phase 4 — Influencer CRUD

- [ ] Add versioned admin influencer routes under `/api/v1/admin/influencers` and protect them with `auth:sanctum`.
- [ ] Add create/update validation using Form Requests.
- [ ] Put create/update/delete business logic in an influencer service.
- [ ] Add backend feature tests for authorization, validation, create, update, and delete.
- [ ] Build the admin influencer table against the admin API.
  - **Partial:** A table already renders public influencer data, but sorting points to `example.com` and there are no edit/delete operations.
- [ ] Build a working create/edit influencer form.
  - **Partial:** `InfluencerModal.vue` contains form markup but currently references undefined review variables and submits through the comment action.
- [ ] Add delete confirmation and useful loading/error/empty states.

## Phase 5 — Public influencer pages

- [ ] Move public routes/stores to `/api/v1/influencers`.
  - **Partial:** Legacy list/detail endpoints work at `/api` and `/api/{slug}`.
- [x] Render a public influencer grid from API data.
  - Follow-up required: replace hard-coded images/rating counts and improve empty/error states.
- [ ] Add influencer search.
- [ ] Finish the influencer detail page using API fields only.
  - **Partial:** The page loads an influencer, comments, and other influencers, but profile/social links and counts are hard-coded and the nested `other_influencers` response shape may not match the UI.
- [ ] Return and display approved-review average and approved review count.
- [ ] Remove the out-of-scope client-only helpful/like interaction.
- [ ] Finish the About page (currently placeholder text).

## Phase 6 — Reviews

- [ ] Add versioned public review routes under `/api/v1/influencers/{slug}/reviews`.
- [ ] Submit a review with `pending` status.
  - **Partial:** The current legacy POST `/api/{slug}` creates a comment with `is_approved = false`.
- [ ] Validate rating as a 1–5 value and align frontend/backend handling of whole versus half stars.
- [ ] Return approved reviews only from every public endpoint.
  - **Broken now:** `InfluencerResource` exposes all related comments, including unapproved submissions.
- [ ] Add versioned protected admin review listing, approve, and reject endpoints.
- [ ] Build the admin review moderation page.
  - **Partial:** The route and page shell exist, but the page contains only a heading.
- [ ] Recalculate `rating` and `review_count` from approved reviews after approve/reject changes.
- [ ] Add backend feature tests for pending submission, public filtering, approve/reject, and rating recalculation.

## Phase 7 — Polish and final verification

- [ ] Replace hard-coded influencer data, links, images, rating counts, and review counts.
- [ ] Add consistent loading, error, and empty states.
- [ ] Fix login error/loading paths and malformed nested form markup.
- [ ] Fix text-encoding/mojibake in public copy and architecture documents.
- [ ] Improve mobile layout and accessibility.
- [ ] Add local influencer/review seed data.
- [ ] Run the complete backend test suite.
- [ ] Run `php artisan route:list` and `php artisan migrate:status`.
- [ ] Run the frontend production build and inspect browser console/network behavior.
- [ ] Final cleanup of unused imports, dependencies, default scaffolding, and dead code.

## Current risks and blockers

1. **Dirty working tree:** existing uncommitted changes span auth, Sanctum, migrations, Composer/npm locks, frontend stores, Vite config, and the untracked `.agents` guidance. Preserve and review them before implementation.
2. **Applied legacy schema:** all current migrations have run, so MVP schema alignment must use new forward migrations; editing old migration files would be unsafe for an existing database.
3. **Public data leak:** pending comments are currently included in public influencer resources, violating the core architecture rule.
4. **No meaningful regression tests:** current green tests only prove that Laravel boots.
5. **Client auth is not authoritative:** portal access is based on a mutable `sessionStorage` marker and is not restored/validated against Laravel.
6. **Generic dynamic routes:** `/api/{slug}` conflicts with the required versioned, explicit contract and will make future route additions risky.
7. **Schema vocabulary mismatch:** current `Comment`/`comments`/`is_approved` code does not support the planned `Review`/`reviews`/three-state moderation model.
8. **Admin portal scaffolding is misleading:** influencer creation and review moderation appear in the UI but are not functional end to end.

