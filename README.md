# Composer Rumus

`composer-rumus` is a Laravel reporting add-on that installs an Invoice Report and an Invoice Cash Report into an existing POS or invoicing application.

It **does not create an invoice system or database schema**. The host application must already provide its invoice, payment, expense, warehouse, customer, transaction, and user models/tables. This design avoids duplicating business data and lets the package work with the existing application.

## Install from Packagist

After publishing this repository to Packagist, install it in a Laravel project:

```bash
composer require your-vendor/composer-rumus
php artisan vendor:publish --tag=composer-rumus-config
```

Laravel package discovery registers the routes and views automatically. Clear cached configuration after editing the published configuration:

```bash
php artisan optimize:clear
```

The default URLs are:

- `/reports/invoice`
- `/reports/invoice-cash`

Change the prefix, middleware, host model classes, relationship method names, database columns, and permission fields in `config/composer-rumus.php`.

## Host application contract

The default configuration matches this repository. A host application must supply:

| Model | Required fields / relations |
| --- | --- |
| Invoice | `created_at`, `status`, `balance_due`, `branch`, `total`, `invoice_no`; `customer`, `creator`, `warehouse` relations |
| Payment | `invoice_id`, `payment_date`, `amount`, `payment_method`; `invoice`, `transaction` relations |
| Expense | `date`, `amount_mmk`, `name`; `transaction`, `warehouse` relations |
| Warehouse | `id`, `name` |
| User | `is_admin` and `level` (JSON list of permitted warehouse IDs for non-admin users) |

The package needs a standard `web` and `auth` middleware setup. If your field or relation names differ, update the configuration before using the pages.

## Release to Packagist

1. Move the `composer_rumus` directory into its own Git repository named `composer-rumus`.
2. Replace `your-vendor` in `composer.json` with your Packagist/GitHub vendor name.
3. Add an MIT `LICENSE` file, commit, and push to public GitHub.
4. Create and push a semantic version tag, for example `v1.0.0`.
5. Submit the repository URL at https://packagist.org/packages/submit.

Use a Packagist webhook after the first submission so every new Git tag becomes available to Composer automatically.

## Package contents

- `src/ComposerRumusServiceProvider.php`: automatic Laravel registration.
- `routes/web.php`: authenticated report routes.
- `src/Http/Controllers`: invoice, cash, and expense-detail queries.
- `resources/views/reports`: portable report pages with date filters and summaries.
- `config/composer-rumus.php`: host-application integration contract.
- `docs/REPORT_FLOWS.md`: functional report-flow summary.
