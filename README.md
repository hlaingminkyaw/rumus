# Composer Rumus

`composer-rumus` is a Laravel reporting add-on that installs an Invoice Report and an Invoice Cash Report into an existing POS or invoicing application.

It **does not create an invoice system or database schema**. The host application must already provide its invoice, payment, expense, warehouse, customer, transaction, and user models/tables. This design avoids duplicating business data and lets the package work with the existing application.

## Install from Packagist

After publishing this repository to Packagist, install it in a Laravel project:

```bash
composer require kyawgyi/rumus --prefer-dist
php artisan vendor:publish --tag=composer-rumus-config
```

### Install footprint

The package requires only `php` and `illuminate/support`, which every Laravel
application already has. It declares **no development dependencies**, and the
`.gitattributes` file keeps `tests/`, `docs/`, and CI files out of the Packagist
archive, so `composer require kyawgyi/rumus` downloads this package alone and no
extra vendor packages.

### Troubleshooting `HTTP/2 429` download errors

An install that stops with lines such as:

```
Failed to download symfony/polyfill-mbstring from dist: ... (HTTP/2 429)
Source fallback is disabled. Not trying alternative sources.
```

is GitHub rate limiting anonymous downloads from `codeload.github.com`. Those
packages (PHPUnit, Symfony polyfills, spatie/ignition, and so on) belong to the
host application, not to this package, so nothing here can skip them. Fix it in
the host project with one of these:

```bash
# 1. Authenticate Composer once. This removes the anonymous rate limit.
composer config --global github-oauth.github.com <your-github-token>

# 2. Install runtime packages only, from dist archives.
composer install --no-dev --prefer-dist

# 3. Retry after a rate-limit window, reusing anything already downloaded.
composer install --prefer-dist
```

Avoid `--prefer-source` here: it clones every package over Git and multiplies the
requests that trigger the 429 response.

Laravel package discovery registers the routes and views automatically. Clear cached configuration after editing the published configuration:

```bash
php artisan optimize:clear
```

The default URLs are:

- `/reports/invoice`
- `/reports/invoice-cash`

Change the prefix, middleware, host model classes, relationship method names, database columns, and permission fields in `config/composer-rumus.php`.

## Add the reports to the host layout/sidebar

The package discovers itself after `composer require kyawgyi/rumus`, so its routes are available immediately. To show the report pages inside the host layout (including its existing navbar/sidebar), set the layout and section in the published config:

```php
// config/composer-rumus.php
'layout' => 'layouts.app', // replace with the host application's layout view
'layout_section' => 'content',
```

A Composer package cannot automatically modify a host application's sidebar because each application uses a different layout and HTML structure. Add this **once** in the host application's existing sidebar Blade file (for example, `resources/views/layouts/sidebar.blade.php`):

```blade
@include('composer-rumus::components.sidebar')
```

The partial creates links using the package route names, so it continues to work when `route_prefix` is changed. It also adds the `active` class to the current report link. Style the four `composer-rumus-sidebar-*` classes in the host application's CSS, or copy the two links into the existing menu format:

```blade
<li class="{{ request()->routeIs('composer-rumus.invoice.*') ? 'active' : '' }}">
    <a href="{{ route('composer-rumus.invoice.index') }}">Invoice Report</a>
</li>
<li class="{{ request()->routeIs('composer-rumus.cash.*') ? 'active' : '' }}">
    <a href="{{ route('composer-rumus.cash.index') }}">Invoice Cash Report</a>
</li>
```

Change the menu labels or disable the supplied partial in the published `composer-rumus.php` configuration. To customize report or sidebar markup, publish the views and edit the copies in the host project:

```bash
php artisan vendor:publish --tag=composer-rumus-views
```

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
- `docs/REPORT_FLOWS.md`: functional report-flow summary (excluded from the installed archive).
- `.gitattributes`: keeps development files out of the Composer download.
