# Composer Rumus report flows

The package adds two authenticated report flows to the host Laravel application:

- `/reports/invoice`: invoices created in the selected date range, filtered to valid invoice records and the signed-in user’s permitted branches.
- `/reports/invoice-cash`: payment entries received in the selected date range, grouped as Cash, Kpay, MMQR, Credit, or Mobile Banking; it also shows expense and profit summaries.

Invoice totals use the invoice table. Cash totals use payment rows, so split payments appear as separate rows. Profit equals payment income minus expenses mapped to the same payment bucket.

See the package README for host-model, relation, and column requirements.
