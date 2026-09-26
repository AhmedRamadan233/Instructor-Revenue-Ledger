# AI usage

How AI assistants (Cursor / Composer with Laravel Boost guidelines) were used while building this Challenge submission.

## What AI did

- **Scaffolding:** migrations, enums, models, factories, repository interfaces/implementations, and Livewire page shells following existing project conventions.
- **Money-core actions:** `ProcessRevenuePeriod`, subscribe/cancel/expire, payout request/mark-paid, and later `ProcessPayoutWithProvider` + mock provider + queued jobs guided by the Challenge failure/idempotency requirements.
- **Mid-term refund:** `RefundMidTermSubscription` plus Feature tests and student UI affordance (Refund vs Cancel).
- **Tests:** PHPUnit Feature coverage for allocation math, money-flow end-to-end, payout provider failure modes, and refunds.
- **Docs:** `docs/ARCHITECTURE.md`, this file, and the delivery-oriented README — drafted from `STATUS.md` and the implemented code paths.
- **Arabic delivery aids:** `سيناريو-الفيديو.md` / `سيناريو-الكود.md` rewritten as walkthrough stories for the review video.

## What humans owned

- Product rules: prepaid plans, monthly watch-weighted allocation, carry-forward, no denormalized teacher balance, refund retains Processed months (no clawback).
- Priority order vs the Challenge brief (P0 provider/jobs/idempotency before P1 docs/refund).
- Demo/ops choices: force re-process for demos, manual Mark Paid alongside automated `payouts:process`.
- Review of AI-proposed edge cases (timeout-after-success, double job, wrong-month process) and acceptance of the final behavior.

## How we kept AI useful without losing correctness

1. Prefer Actions + repositories already in the tree over new ad-hoc services.
2. Encode money rules in Feature tests before treating a flow as “done”.
3. Document irreversible decisions (refund vs clawback, provider timeout reconcile) in `ARCHITECTURE.md` so video/review answers stay consistent.
4. Do not invent Filament/Pest solely because the brief names them — call the deviation out and keep Livewire/PHPUnit working coverage.

## Tools / context the agent relied on

- Laravel Boost project guidelines (`AGENTS.md`) and local skills (`laravel-best-practices`, `testing-best-practices`).
- Existing tests as the source of truth for allocation and balance math.
- `STATUS.md` as the gap checklist against the Challenge brief.

## Honest limits

AI accelerated structure and boilerplate; correctness of money under retries still required human prioritization (P0 first) and test-gated changes. Any remaining brief gaps (Filament page, Pest migration, delivery video) are tracked in `STATUS.md`, not papered over in this file.
