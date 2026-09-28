# Evalia Website Production Action Audit Checklist

Generated: 2026-08-26

## Current Audit Status

- [x] Laravel route map generated: 118 routes.
- [x] Database migration status checked: all migrations are applied locally.
- [x] Frontend production build checked: `npm run build` succeeds.
- [x] Composer metadata checked: valid with package version pinning warnings.
- [ ] Automated test suite is production-ready: currently blocked by a failing/stale homepage redirect test.
- [ ] Production environment is ready: blocked until secrets are rotated and production `.env` is configured.
- [ ] Deployment target is confirmed: server/hosting credentials and production domain were not provided.

## Production Blockers

- [ ] Rotate all local credentials before deployment: AWS, Hamsa, and OpenAI keys were present in local `.env`.
- [ ] Set production environment values: `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, production DB, mail, queue, cache, and filesystem settings.
- [ ] Fix or update `tests/Feature/ExampleTest.php`: `/` currently returns the landing page with HTTP 200, but the test expects redirect to login.
- [ ] Remove tracked user/audio artifacts from `public/uploads/voice-pints` and move runtime uploads to storage/S3 only.
- [ ] Remove or rename `app/Http/Controllers/VoicePintController copy.php`; duplicate production code should not ship.
- [ ] Review `/clear-cache`: it runs multiple Artisan commands over HTTP. Keep it admin-only, log usage, and consider removing it from production routes.
- [ ] Replace `Route::any('/user/group_data/delete/{id}')` with a proper `DELETE` route and CSRF-protected form/action.

## Authentication And Session Actions

- [ ] Public landing page `/` loads for guests.
- [ ] Login page `/login` renders.
- [ ] Login with valid credentials succeeds and redirects to the correct dashboard.
- [ ] Login with invalid credentials fails with a clear message.
- [ ] Protected routes redirect unauthenticated users to login.
- [ ] Logout invalidates the session and redirects safely.
- [ ] Session timeout behavior is acceptable.
- [ ] Role/permission middleware blocks unauthorized users.

## Dashboard, Profile, Support, Subscription

- [ ] `user-dashboard` loads correct product/dashboard data.
- [ ] `set-active-product` changes active product and persists correctly.
- [ ] `user-profile` displays current user details.
- [ ] `user-profile/update` validates and saves profile changes.
- [ ] `user/subscription` renders current plan/subscription state.
- [ ] `performance-badges` renders user performance metrics.
- [ ] `user/support` renders support content/contact paths.

## User Management

- [ ] Users list loads with expected filters/pagination.
- [ ] Create user form loads.
- [ ] Create user validates required fields, email uniqueness, role, company, and evaluation role.
- [ ] User details page loads.
- [ ] Edit user form loads current values.
- [ ] Update user validates and persists changes.
- [ ] Delete user deactivates/removes according to business rule.
- [ ] Activate user restores access.
- [ ] Change password form loads.
- [ ] Change password validates confirmation and stores hashed password.
- [ ] `api/get-evaluation-roles` returns roles scoped to selected company/user context.

## Role And Permission Management

- [ ] Roles list loads.
- [ ] Create role form loads.
- [ ] Store role validates name and permissions.
- [ ] Role details page displays assigned permissions.
- [ ] Edit role form loads current permissions.
- [ ] Update role changes permissions correctly.
- [ ] Delete role is blocked when unsafe or succeeds when allowed.
- [ ] `roles/api/permissions` returns expected permission groups.

## Company Management

- [ ] Company list loads.
- [ ] Create company form loads.
- [ ] Store company validates required fields and settings.
- [ ] Company details page loads.
- [ ] Edit company form loads current values.
- [ ] Update company persists configuration, risk settings, and extraction settings.
- [ ] Delete company handles dependent users/tasks safely.
- [ ] Company evaluation/re-evaluation action starts the expected task flow.

## Group Data

- [ ] Group list loads.
- [ ] Create group page loads.
- [ ] Store group validates and persists.
- [ ] Group details/edit page loads.
- [ ] Update group persists changes.
- [ ] Delete group requires an intentional delete method and respects permissions.

## Evaluation Roles

- [ ] Evaluation roles list loads.
- [ ] Create evaluation role page loads.
- [ ] Store validates evaluation criteria, language/linguistic fields, and company scope.
- [ ] Edit evaluation role page loads.
- [ ] Update persists all evaluation settings.
- [ ] Delete handles users/tasks already assigned to the role.

## Task And Evaluation Workflow

- [ ] Task upload form accepts valid audio files and rejects invalid files.
- [ ] Task upload stores audio to the configured production disk/S3.
- [ ] Temporary media URL is generated and accessible by Hamsa.
- [ ] Hamsa transcription job is created.
- [ ] Task status polling updates pending/processing/completed/failed states.
- [ ] Task details page renders transcript, scores, metrics, risks, and extracted data.
- [ ] Task delete action respects permission and dependency rules.
- [ ] Task list by company loads and filters correctly.
- [ ] Manual re-evaluation action works.
- [ ] Agent-wise extraction report loads correct data.
- [ ] `api/hamsa/job/{jobId}` returns expected JSON.
- [ ] `api/task/{taskId}/hamsa-job` fetches and saves Hamsa results.

## Knowledge Base

- [ ] Knowledge base list loads.
- [ ] Create knowledge item page loads.
- [ ] Store validates title/content/file/keywords as applicable.
- [ ] Details page renders content and metadata.
- [ ] Edit page loads current data.
- [ ] Update persists changes.
- [ ] Delete removes the correct item and associated files/indexes.
- [ ] Search test returns relevant results.
- [ ] Simulator page loads.
- [ ] Simulator run produces expected responses and handles empty/no-match cases.

## Agents And Performance

- [ ] Agents dashboard loads.
- [ ] Agents list loads.
- [ ] Agent details page loads.
- [ ] Agent performance history loads.
- [ ] Agent performance data endpoint returns chart-ready JSON.
- [ ] Agent detail shortcut route works.
- [ ] Agent dashboard/coaching/supervisor dashboard views resolve to real templates.

## Hamsa Integration

- [ ] Hamsa dashboard loads with account/project data.
- [ ] Transcribe page loads.
- [ ] Transcribe submit creates a job.
- [ ] Transcription job detail/status endpoint works.
- [ ] TTS page loads.
- [ ] TTS submit creates a job.
- [ ] TTS job detail/status endpoint works.
- [ ] Translate page loads.
- [ ] Translate submit returns translated content.
- [ ] Speech-to-speech page loads.
- [ ] Speech-to-speech submit creates a job.
- [ ] STS job endpoint works.
- [ ] AI generation page loads.
- [ ] AI generation submit returns content.
- [ ] Voice agents list loads.
- [ ] Voice agent creation validates and creates agent.
- [ ] Voice agent detail endpoint works.
- [ ] Voice agent clone action works or is disabled if not production-ready.
- [ ] Conversations list loads.
- [ ] Start conversation works.
- [ ] Conversation detail endpoint works.
- [ ] Jobs list loads.
- [ ] Job detail endpoint works.
- [ ] Usage page loads.
- [ ] Project settings page loads.
- [ ] Hamsa API failures show safe user-facing errors and log details server-side.

## Voice Pint / Voice Print

- [ ] Voice-pint index loads.
- [ ] Upload validates file type, size, duration, and format.
- [ ] Uploaded audio is processed successfully.
- [ ] Processed voice print is stored in the intended disk/location.
- [ ] Transcription lookup from DB or Hamsa works.
- [ ] Voice matching/identification returns expected result.
- [ ] Stream endpoint plays authorized files only.
- [ ] Delete endpoint deletes only authorized filenames.
- [ ] Clear-all endpoint is restricted and cannot erase unrelated files.
- [ ] Public upload directory does not contain tracked or sensitive runtime files.

## Telephony Accounts

- [ ] Telephony accounts list loads.
- [ ] Create telephony account page loads.
- [ ] Store validates provider/account fields and credentials.
- [ ] Update persists credential/config changes.
- [ ] Delete removes or disables account safely.
- [ ] Production secrets for telephony are stored outside the repo.

## Frontend And Browser QA

- [ ] Landing page desktop layout.
- [ ] Landing page mobile layout.
- [ ] Login desktop/mobile layout.
- [ ] Authenticated layout: sidebar, topbar, footer.
- [ ] Tables: pagination, search, empty state, loading state.
- [ ] Forms: validation messages, old input, success/error flashes.
- [ ] Modals/dropdowns/buttons work across desktop/mobile.
- [ ] Audio upload/progress states are clear.
- [ ] Charts render with real and empty data.
- [ ] No console errors on main flows.
- [ ] No broken CSS/JS/image/font assets.

## Security Checklist

- [ ] `APP_DEBUG=false` in production.
- [ ] Production secrets are not committed or copied into public directories.
- [ ] Rotate any credentials exposed in local files or logs.
- [ ] Run `php artisan key:generate` only for new environments, never overwrite an active production key casually.
- [ ] All mutating actions use POST/PUT/PATCH/DELETE with CSRF protection.
- [ ] All destructive actions require permissions and ownership checks.
- [ ] Uploaded filenames are sanitized and path traversal is blocked.
- [ ] File uploads validate MIME type and size.
- [ ] S3 bucket permissions are private by default.
- [ ] Temporary URLs expire quickly.
- [ ] Logs do not contain API keys, signed URLs, or sensitive transcripts.
- [ ] Admin-only maintenance routes are removed or tightly protected.
- [ ] HTTPS is enforced.
- [ ] Cookies use secure production settings.
- [ ] Database user has least privilege.

## Deployment Checklist

- [ ] Confirm production host, domain, database, queue worker, storage, and mail provider.
- [ ] Pull the intended release branch/tag onto the server.
- [ ] Install PHP dependencies: `composer install --no-dev --optimize-autoloader`.
- [ ] Install/build assets locally or in CI: `npm ci && npm run build`.
- [ ] Upload/build `public/build` artifacts.
- [ ] Configure production `.env`.
- [ ] Run `php artisan migrate --force`.
- [ ] Run `php artisan storage:link`.
- [ ] Run `php artisan config:cache`.
- [ ] Run `php artisan route:cache`.
- [ ] Run `php artisan view:cache`.
- [ ] Start/restart queue worker: `php artisan queue:work` under Supervisor/systemd.
- [ ] Restart PHP-FPM/Apache as appropriate.
- [ ] Point web root to `public`.
- [ ] Verify `/up` returns healthy.
- [ ] Verify login and the top 5 business-critical flows in production.
- [ ] Monitor logs, queue failures, Hamsa/API errors, and upload failures after release.

## Commands Already Run Locally

- `php artisan route:list` passed and found 118 routes.
- `php artisan migrate:status` passed; all migrations are applied locally.
- `composer validate --no-check-publish` passed with version constraint warnings.
- `npm run build` passed with Sass deprecation warnings.
- `php artisan test` failed because the homepage redirect test no longer matches current route behavior.
