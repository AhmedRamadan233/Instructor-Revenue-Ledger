# Instructor Revenue Ledger

Prepaid student subscriptions → real watch-time → monthly teacher revenue allocation → ledger → payouts (manual approval **and** mock payment provider + queued jobs).

Challenge delivery docs:

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — allocation, refund, idempotency, provider timeout, scale, limitations
- [`docs/AI_USAGE.md`](docs/AI_USAGE.md) — how AI was used
- [`STATUS.md`](STATUS.md) — done vs remaining vs Challenge brief

## Assumptions

1. Students pay the full plan up front (manual payment row; no real student PSP).
2. Access is platform-wide for Published courses while the subscription is Active (not course-by-course SKUs).
3. Teacher earnings are derived from `teacher_ledger_entries` + reserved payouts — not a `teachers.balance` column.
4. A calendar month is closed once; re-run only via explicit **force** (demo).
5. Mid-term **Refund** returns unused (not-yet-Processed) prepaid months; Processed months stay with platform/teachers.
6. **Cancel** ends access with no cash back.
7. Automated payouts use `MockPaymentProvider` (`PAYOUT_PROVIDER=mock`). Manager “Mark paid” remains for ops demos.
8. UI is Livewire (not Filament yet). Tests are PHPUnit Feature tests (not Pest yet).

## Stack

| Piece | Version / choice |
|-------|------------------|
| PHP | 8.3+ |
| Laravel | 13 |
| Livewire | 4 |
| DB | MySQL (Laragon) locally; SQLite in PHPUnit |
| Front assets | Vite + Bootstrap (existing layouts) |

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure DB in `.env`, then:

```bash
php artisan migrate --seed
npm install
npm run build
```

Serve (Laragon vhost or):

```bash
php artisan serve
```

Optional queue worker (required for `payouts:process` jobs):

```bash
php artisan queue:work
```

### Demo accounts (from seeder)

Password for all: `password`

| Role | Email |
|------|-------|
| Manager | `manager@example.com` |
| Teacher | `teacher1@example.com` |
| Student | `student1@example.com` |

### Useful env

```env
PAYOUT_PROVIDER=mock
# Optional: force mock outcome — succeeded | failed | timeout_after_success
PAYOUT_MOCK_OUTCOME=
```

## Common flows

1. Student → Plans → Subscribe → Watch courses  
2. Manager → Revenue → Process month (or wait for `revenue:process`)  
3. Teacher → Ledger / request payout  
4. Either:
   - Manager → Payouts → Mark paid, **or**
   - `php artisan payouts:process` (+ queue worker) through the mock provider  
5. Student → Subscriptions → **Refund** (partial prepaid) or **Cancel** (no refund)

Scheduled (see `routes/console.php` / bootstrap schedule):

- `subscriptions:expire` daily  
- `revenue:process` monthly  

## Tests

```bash
php artisan test --compact
```

Narrow examples:

```bash
php artisan test --compact tests/Feature/PayoutProviderFlowTest.php
php artisan test --compact tests/Feature/RefundMidTermSubscriptionTest.php
php artisan test --compact tests/Feature/MoneyFlowEndToEndTest.php
```

## Project map (short)

| Path | Role |
|------|------|
| `app/Actions/…` | Business operations (subscribe, process month, payout, refund) |
| `app/Payments/…` | Provider contract + mock |
| `app/Jobs/ProcessPayoutJob.php` | Queued payout |
| `app/Support/TeacherBalance.php` | Available / reserved balance |
| `app/Livewire/…` | Manager / Teacher / Student UI |
| `tests/Feature/…` | Money + failure coverage |

## License

MIT (Laravel base). Challenge submission documentation is in `docs/`.
