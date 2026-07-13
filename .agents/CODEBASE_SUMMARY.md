# RateInfluencers Codebase Handoff

Last inspected: 2026-07-13

This document is intended to give another AI or developer enough context to work on the repository without first reverse-engineering the entire project.

## 1. Product summary

RateInfluencers is an early-stage review platform where visitors can browse influencers, view ratings and comments, and submit a review. It also contains the beginnings of an administrator portal intended to manage influencers and comments.

The public browsing and review-submission path is partially implemented. Administrator login/logout is implemented with a Laravel Sanctum stateful session, but most portal management features are still placeholders or incomplete.

## 2. Repository shape

The repository contains two separate applications:

```text
Rate-Influencers/
├── backend/                  Laravel JSON API and database layer
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   └── Services/
│   ├── bootstrap/app.php     Laravel 11 routing/middleware setup
│   ├── config/               Auth, Sanctum, CORS, DB, sessions, etc.
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   └── tests/
├── frontend/                 Vue single-page application
│   ├── src/
│   │   ├── components/
│   │   ├── layout/
│   │   ├── router/
│   │   ├── stores/
│   │   ├── utils/
│   │   └── views/
│   └── vite.config.js
└── README.md
```

The backend and frontend have independent package manifests and development servers.

## 3. Architecture

```text
Browser
  |
  | Vue Router + Pinia + Axios
  | /api/* and /sanctum/*
  v
Vite development server :3000
  |
  | proxy preserves the request path
  v
Laravel application :8000
  |
  | Controllers -> Services -> Eloquent models
  v
MySQL laravel_db :3306
```

- The Vue application uses Axios with `baseURL = "/api"` for normal API requests.
- Vite proxies both `/api` and `/sanctum` to `http://127.0.0.1:8000` without rewriting either prefix.
- Laravel 11 automatically prefixes routes from `routes/api.php` with `/api`, so preserving `/api` is required by the current configuration.
- Public influencer and review endpoints are unauthenticated.
- Administrator logout is protected by `auth:sanctum`.
- Sanctum is configured for first-party SPA cookie/session authentication through `$middleware->statefulApi()` in `backend/bootstrap/app.php`.
- The `web` session guard uses the `Administrator` model/provider rather than the default `User` model.
- Sessions are stored as files. Cache uses files and queues run synchronously in the local environment.

## 4. Technology stack and installed versions

### Backend

| Technology | Declared constraint | Installed version |
|---|---:|---:|
| PHP | `^8.2` | Use XAMPP PHP 8.2+ |
| Laravel Framework | `^11.9` | `11.24.0` |
| Laravel Sanctum | `^4.3` | `4.3.2` |
| Laravel Tinker | `^2.9` | `2.10.0` |
| PHPUnit | `^11.0.1` | Composer-managed |
| MySQL | N/A | XAMPP instance on port 3306 |

The backend also has its own Vite/JavaScript scaffold (`vite 5.4.21`, `laravel-vite-plugin 1.3.0`, `axios 1.18.1`), but the actual product UI is the standalone `frontend/` application. Laravel's Blade frontend remains the default welcome page.

### Frontend

| Package | Installed version |
|---|---:|
| Vue | `3.4.38` |
| Vite | `4.5.3` |
| `@vitejs/plugin-vue` | `4.6.2` |
| Vue Router | `4.4.3` |
| Pinia | `2.2.2` |
| Axios | `1.7.7` |
| Tailwind CSS | `3.4.10` |
| Flowbite | `2.5.1` |
| SweetAlert2 | `11.14.1` |
| Oh Vue Icons | `1.0.0-rc3` |
| Vue Star Rating | `2.1.0` |
| Vue3 Table Lite | `1.4.3` |
| CryptoJS | `4.2.0` (installed but no longer used by current login code) |

The frontend uses JavaScript and Vue Single-File Components; it is not a TypeScript project.

## 5. Backend routing and API contract

`backend/routes/api.php` currently defines:

| Method | URL | Auth | Handler | Behavior |
|---|---|---|---|---|
| `GET` | `/api` | Public | `InfluencerController@index` | Lists all influencers as a Laravel resource collection. |
| `GET` | `/api/{slug}` | Public | `InfluencerController@show` | Gets one influencer with comments and five random alternatives. |
| `POST` | `/api/{slug}` | Public | `CommentController@store` | Adds an unapproved review/comment for the influencer. |
| `POST` | `/api/login` | Public | `AdministratorController@login` | Validates credentials, creates a server session, and returns the administrator. |
| `POST` | `/api/logout` | Sanctum | `AdministratorController@logout` | Logs out, invalidates the session, and rotates the CSRF token. |
| `GET` | `/sanctum/csrf-cookie` | Public | Sanctum | Initializes the XSRF/session cookies for SPA authentication. |
| `GET` | `/` | Public | Closure | Returns Laravel's default Blade welcome page. |

There are no backend endpoints yet for creating, updating, or deleting influencers; listing/moderating comments; retrieving the current administrator; or password recovery.

### Important response shapes

List influencers:

```json
{
  "data": [
    {
      "id": 1,
      "name": "...",
      "slug": "...",
      "rating": 4.5,
      "profile_picture": "...",
      "facebook_link": "...",
      "youtube_link": "...",
      "tiktok_link": "...",
      "instagram_link": "...",
      "created_at": "Sep 25, 2024",
      "updated_at": "Sep 25, 2024",
      "comments": []
    }
  ]
}
```

Show influencer:

```json
{
  "error": false,
  "influencer": { "...": "InfluencerResource fields" },
  "other_influencers": { "data": [] }
}
```

Successful review submission returns HTTP 201:

```json
{
  "error": false,
  "message": "Comment has been saved."
}
```

Successful login returns:

```json
{
  "admin": {
    "id": 1,
    "email": "arthur.white@example.net",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

Invalid login returns HTTP 422 with `message` and `errors.email`/`errors.password` arrays.

## 6. Backend layers

### Controllers

- `AdministratorController`: login response/error handling, session regeneration, and logout/session invalidation.
- `InfluencerController`: list and detail endpoints. Detail delegates lookup and recommendations to `InfluencerService`.
- `CommentController`: resolves the influencer by slug, validates the request, and delegates creation to `CommentService`.

### Services

- `AdminService`: queries `Administrator`, verifies the password with `Hash::check`, and logs the model into the `web` guard.
- `InfluencerService`: loads an influencer and its comments by slug, then chooses five random other influencers.
- `CommentService`: creates a comment with `is_approved = false`.

### Form requests

- `AdminRequest`: requires a valid email and a password.
- `CommentRequest`: requires a string name, integer influencer rating, and string comment.

There is currently no minimum/maximum validation for ratings and no explicit maximum validation matching the database's name/comment column lengths.

### JSON resources

- `InfluencerResource` formats influencer fields and dates and embeds `CommentResourceCollection`.
- `CommentResource` exposes the comment text, author name, influencer rating, like count (`comment_rating`), and approval flag.

## 7. Database model

All current migrations have run in the local database.

### Domain tables

`administrators`

- `id`
- nullable `fullname`
- nullable `password`
- nullable `email`
- nullable `profile_picture`
- nullable `mobile`
- timestamps

`influencers`

- `id`
- nullable `name`
- nullable numeric `rating`
- nullable `profile_picture`
- nullable Facebook, YouTube, TikTok, and Instagram links
- nullable `slug`
- timestamps

`comments`

- `id`
- nullable `name` (maximum 50 characters)
- nullable integer `influencer_rating`
- nullable `comment` (maximum 500 characters)
- nullable integer `comment_rating` used as a like count
- boolean `is_approved`, default false
- `influencer_id`, foreign key to `influencers.id`
- timestamps

Relationship:

```text
Influencer 1 ---- * Comment
```

Deleting an influencer cascades to its comments. Updating an influencer primary key is restricted.

### Framework/default tables

- `users`, `password_reset_tokens`, and `sessions` exist from Laravel's default migration. The application currently authenticates administrators, not `User` records.
- `cache`, `cache_locks`, `jobs`, `job_batches`, and `failed_jobs` exist even though the local environment uses file cache and synchronous queues.
- `personal_access_tokens` exists because Sanctum is installed. Current login uses a stateful session and does not create a personal access token.

### Models

- `Administrator` extends `Authenticatable` and uses `HasApiTokens`, `HasFactory`, and `Notifiable`.
- `Influencer` has many comments.
- `Comment` belongs to an influencer.
- The default `User` model still exists but is not the model used by the `web` guard.

### Seeder

`DatabaseSeeder` idempotently creates the administrator only if the email does not already exist:

- Email: `arthur.white@example.net`
- Password: `Test_123`
- Password storage: `Hash::make(...)`

The seeder does not create sample influencers or comments.

## 8. Authentication flow

The intended current flow is:

1. The login page calls `GET /sanctum/csrf-cookie` with an Axios `baseURL: ""` override so it bypasses the normal `/api` base URL.
2. Vite proxies `/sanctum/*` unchanged to Laravel.
3. Axios sends `POST /api/login`, including cookies and the XSRF header.
4. Laravel's stateful API middleware starts the session.
5. `AdminService` verifies the administrator password and logs the model into the `web` guard.
6. Laravel regenerates the session ID and returns the administrator record.
7. The frontend stores the literal marker `authenticated` under the misleading key `api-token` in `sessionStorage`; this is not an API token.
8. `POST /api/logout` requires the Sanctum-authenticated session, logs out, invalidates the session, and regenerates the CSRF token.

Axios global defaults in `frontend/src/stores/auth.js` are:

```js
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "/api";
```

Local Sanctum stateful domains include `localhost:3000`, `127.0.0.1:3000`, and `127.0.0.1:8000`.

## 9. Frontend organization and behavior

### Application bootstrap

`frontend/src/main.js` installs Pinia and Vue Router and globally registers:

- `star-rating`
- `v-icon`

`App.vue` selects one of three route-driven layouts:

- `Front.vue`: public site shell.
- `Auth.vue`: split-screen login shell.
- `Portal.vue`: sidebar-based administrator shell.

### Router

| URL | Route name | Layout | Intended status |
|---|---|---|---|
| `/` | `home` | front | Implemented influencer grid. |
| `/:influencerSlug` | `influencer` | front | Partially implemented influencer/review page. |
| `/about` | `about` | front | Placeholder. |
| `/login` | `login` | auth | Implemented admin login UI. |
| `/admin/dashboard` | `dashboard` | portal | Placeholder heading. |
| `/admin/influencer` | `influencerPortal` | portal | Read-only table works; creation is broken/incomplete. |
| `/admin/comments` | `commentsPortal` | portal | Placeholder heading. |

The route guard checks only for the `sessionStorage` marker. It does not ask Laravel whether the session is still valid.

### Pinia stores

`auth.js`

- Initializes the CSRF cookie.
- Logs in and stores the returned administrator in memory.
- Logs out and clears the in-memory administrator.
- Configures global Axios base URL/cookie/XSRF behavior.

`influencer.js`

- Lists influencers.
- Loads one influencer by slug.
- Submits a review/comment by slug.

### Public UI

- Home page fetches all influencers and displays cards.
- Clicking a card navigates to the slug route.
- Influencer detail displays ratings, comments, random alternative influencers, and a review modal.
- Review modal performs client-side required-field validation and submits the review.
- SweetAlert2 shows review submission and detail-load results.
- A comment's heart/like control updates only local component state; it is not persisted.

### Administrator UI

- Login form is prefilled with the seeded local administrator credentials.
- Sidebar links to Dashboard, Influencers, and Comments.
- Influencer portal lists existing influencer data in `vue3-table-lite`.
- Dashboard and Comments pages are placeholders.
- Influencer creation modal is unfinished and currently references undefined review variables and calls the comment-submission action. There is no matching backend create-influencer endpoint.

## 10. Current local configuration

The ignored `backend/.env` currently uses:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Manila
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000,127.0.0.1:8000
```

`APP_KEY` is configured but intentionally not reproduced here.

## 11. Development commands

On this Windows/XAMPP workstation, PHP is not globally available on `PATH`, so use the full executable path.

Backend:

```powershell
cd backend
C:\xampp\php\php.exe artisan migrate --seed
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```

Frontend:

```powershell
cd frontend
& 'C:\Program Files\nodejs\npm.cmd' install
& 'C:\Program Files\nodejs\npm.cmd' run dev
```

The Vite frontend is configured for port 3000.

Useful verification commands:

```powershell
C:\xampp\php\php.exe artisan test
C:\xampp\php\php.exe artisan route:list
& 'C:\Program Files\nodejs\npm.cmd' run build
```

## 12. Test and runtime status at inspection time

- MySQL is reachable and all eight migrations report `Ran`.
- Laravel route discovery succeeds and reports six application routes, excluding vendor routes.
- `php artisan test` passes: two tests, two assertions.
- Both tests are Laravel skeleton examples; there is no meaningful domain or authentication coverage yet.
- A frontend production build passed after the recent proxy/auth changes.

## 13. Known gaps, inconsistencies, and risks

Another AI should treat these as open work, not established behavior:

1. **No real administrator authorization check in the SPA router.** The client trusts a `sessionStorage` string rather than calling a protected `/api/me` endpoint. A stale marker can expose portal screens, though protected backend routes must still enforce auth.
2. **The router guard can call `next()` twice.** When redirecting unauthenticated users, it still reaches the unconditional final `next()`.
3. **The router removes the auth marker on public routes.** Navigating to any non-protected route clears `api-token`, even if the Laravel session remains valid.
4. **No current-user endpoint.** There is no way for the SPA to restore `admin_record` after refresh or confirm session validity.
5. **Portal CRUD is absent.** No create/update/delete influencer endpoints and no comment moderation endpoints exist.
6. **Influencer creation modal is broken.** It references undefined `rating`, `comment`, and error refs and invokes `addInfluencerComment()` rather than an influencer-create action.
7. **Unapproved comments are exposed publicly.** `InfluencerResource` returns all related comments without filtering `is_approved`.
8. **Ratings are not aggregated.** Review submission does not update `influencers.rating`; ratings are simply stored independently on comments and influencers.
9. **No review rating bounds.** The backend accepts any integer, while the frontend star component allows half-star values; an integer validation rule may reject half-star ratings.
10. **Possible response-shape mismatch.** `other_influencers` is a nested Laravel resource collection and may serialize with a `data` wrapper, while the Vue detail page iterates it as if it were a direct array.
11. **N+1 query risk on influencer listing.** `index()` retrieves influencers without eager-loading comments, but every `InfluencerResource` accesses `$this->comments`.
12. **Hard-coded presentation data.** Public profile pictures, social links, review counts, and rating counts are hard-coded in components instead of using API fields.
13. **Likes are client-only.** The heart button changes a local count and has no API persistence.
14. **About, dashboard, and comments portal are placeholders.** They contain little or no behavior.
15. **Administrator schema/model mismatch.** The table uses `fullname`, but the model lists `name` as fillable. Administrator email is nullable and not unique, and the model has no password hash cast.
16. **Unused default auth artifacts.** The `User` model, user password broker, sessions table, and personal access token table remain even though current authentication uses administrator file sessions.
17. **CORS is permissive/inconsistent for direct cross-origin use.** It combines wildcard origins with credential support. Development normally avoids this through Vite's same-origin proxy.
18. **Error/loading-state weaknesses.** Some frontend failure paths assume specific error arrays or fail to reset loading state.
19. **No domain fixtures.** The seeder creates only an administrator, so a fresh database displays no influencers until records are added manually.
20. **Text encoding issue.** The home-page quotation currently displays mojibake characters in source/output.

## 14. Suggested next implementation order

1. Add `GET /api/me`, repair the Vue route guard, and verify login restoration/logout with feature tests.
2. Add backend feature tests for CSRF/session login, logout, invalid credentials, influencer listing/detail, and review submission.
3. Filter public comments to approved records and build administrator comment moderation endpoints/UI.
4. Implement influencer CRUD endpoints, validation, Pinia actions, and repair `InfluencerModal.vue`.
5. Define rating rules and aggregation behavior, including half-star versus integer storage.
6. Replace hard-coded profile/social/count values with API fields and add image handling.
7. Add influencer/comment seed data for local development.
8. Remove or deliberately retain unused token/default-user infrastructure after the authentication design is finalized.

## 15. Working-tree warning

At inspection time, the Git working tree contains uncommitted changes related to authentication, Sanctum installation/configuration, migrations, dependency locks, and frontend proxy/store updates. Do not reset or overwrite them without reviewing `git diff` and confirming ownership. In particular, an older personal-access-token migration is deleted and a newly timestamped replacement migration is untracked.

