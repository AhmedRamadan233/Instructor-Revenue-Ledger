# Architecture — Instructor Revenue Ledger

This document records the money-flow decisions that matter for correctness under failure, retries, and mid-term exits.

## Money flow (happy path)

```
Subscribe (prepaid) → Watch sessions (seconds)
  → Process calendar month → Revenue allocations (by watch share)
  → Teacher ledger Earning → Request payout → Provider / Mark paid
```

- Platform cut is snapshotted on subscribe (`platform_percentage` / `platform_amount`).
- Remaining amount is `teacher_pool_amount`, split evenly across `duration_months`.
- Teachers never hold a denormalized balance column; available balance is derived from ledger + reserved payouts (`TeacherBalance`).

## Allocation rules

1. **Unit of settlement:** one full calendar month (`revenue_periods.period_start` / `period_end`).
2. **Who is considered:** subscriptions Active / Expired / Cancelled / Refunded whose window overlaps the month.
3. **Pool for the month:** `(teacher_pool_amount / duration_months) + pool_carry_amount`.
4. **Split:** proportional to `watch_seconds` per teacher in that month (sessions tied to the subscription).
5. **No watch:** the month pool is carried forward on the subscription (`pool_carry_amount`); it is not zeroed and not given to the platform.
6. **Idempotent close:** a `Processed` period cannot be closed again unless **force** (demo re-run), which rebuilds allocations/earnings for that month only.

## Mid-term refund vs cancel

| Action | Access | Money |
|--------|--------|-------|
| **Cancel** | Ends immediately | No cash back. Overlapping open months can still settle watches up to `ends_at`. |
| **Refund mid-term** | Ends immediately | Refunds prepaid share of months **not yet** in a Processed revenue period. Retains share of Processed months. Clears `pool_carry_amount`. Writes a `subscription_payments` row (`provider = manual_refund`). **No teacher ledger clawback** for already-settled earnings. |

Rationale: once a month is locked and teachers may have requested payouts, clawing back creates cascading failure. Unused prepaid months that were never closed are still the platform’s liability to the student.

Formula:

```
refund_amount = subscription.amount * (duration_months - processed_overlapping_months) / duration_months
```

## Payout provider, timeout, and idempotency

### Binding

- Contract: `App\Payments\Contracts\PaymentProvider`
- Default mock: `App\Payments\MockPaymentProvider` (`config/payouts.php`, `PAYOUT_PROVIDER=mock`)
- Outcomes: `succeeded` · `failed` (permanent) · `timeout_after_success` (funds moved, `pay()` throws; later `status()` confirms)

### Processing pipeline

1. Teacher creates payout → status `Pending`, unique `idempotency_key` (`payout-{id}`).
2. `php artisan payouts:process` dispatches `ProcessPayoutJob` (unique / without overlapping).
3. `ProcessPayoutWithProvider`:
   - Claims row → `Processing`
   - Calls `pay(idempotencyKey, …)`
   - On success → ledger `Payout` + status `Paid` + `provider_reference`
   - On permanent fail → `Failed` (no ledger debit)
   - On timeout/uncertainty → reconcile via `status(provider_reference)` before deciding

### Idempotency guarantees

| Layer | Guard |
|-------|--------|
| DB | Unique `payouts.idempotency_key` |
| Claim | Only `Pending` (or retryable) moves to `Processing`; `Paid` is a no-op |
| Provider mock | Same idempotency key returns the original transfer |
| Job | Laravel unique job / command overlap protection |
| Balance | `TeacherBalance` reserves `Pending` + `Processing` so a second request cannot overdraw |

Re-running `payouts:process` or retrying the job must not create a second ledger payout for the same row.

### Manual Mark Paid

Manager UI `MarkPayoutPaid` still exists for ops. It does **not** call the provider (no `provider_reference` unless set elsewhere). Automated path is the source of truth for Challenge failure scenarios.

## Scale notes (known path, not load-tested)

Target story: ~500k subscriptions, tens of millions of consumption/ledger rows.

Intended production shape (not implemented as load proof):

- Close months in chunks (subscription id ranges) inside the existing DB transaction boundaries or with per-chunk transactions + period lock.
- `payouts:process` already fans out one job per payout; Horizon / multiple workers are the horizontal scale lever.
- Indexes already favor period + subscription + teacher lookups; add covering indexes only after measuring.

## Stack deviations from the Challenge brief

| Brief | This repo | Notes |
|-------|-----------|--------|
| Laravel 11 | Laravel 13 | Same patterns |
| Livewire v3 | Livewire v4 | Student / Teacher UI |
| Filament v3 | Filament **v5** Manager panel (`/dashboard`) | Teacher view shows balance + ledger + payouts |
| Pest | PHPUnit Feature tests | Equivalent coverage; Pest optional later |
| MySQL | MySQL locally + SQLite in tests | |

## Limitations

- Student payment provider is manual (`SubscribeStudentToPlanOption` marks Paid); not a real gateway.
- Mid-term refund does not push money to an external student PSP — it records the refund obligation in `subscription_payments`.
- `LedgerEntryType::Refund` exists for future teacher adjustments; mid-term student refund intentionally does not write teacher refunds.
- Heartbeat / watch timing still has demo simplifications (see `STATUS.md` improvements).
- No proven load test at Challenge scale numbers.

## Key entry points

| Concern | Class / command |
|---------|-----------------|
| Monthly close | `App\Actions\Revenue\ProcessRevenuePeriod` · `revenue:process` |
| Mid-term refund | `App\Actions\Subscriptions\RefundMidTermSubscription` |
| Request payout | `App\Actions\Payouts\RequestPayout` |
| Provider payout | `App\Actions\Payouts\ProcessPayoutWithProvider` · `payouts:process` |
| Balance | `App\Support\TeacherBalance` |
