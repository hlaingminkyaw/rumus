<?php

return [
    /* Routes are registered automatically with the web and auth middleware. */
    'route_prefix' => 'reports',
    'middleware' => ['web', 'auth'],

    /* Change to the host layout (for example, `layouts.app`) to use its sidebar. */
    'layout' => 'composer-rumus::layouts.report',
    'layout_section' => 'content',

    /* Labels used by the sidebar partial. Set `sidebar.enabled` to false to hide it. */
    'sidebar' => [
        'enabled' => true,
        'title' => 'Reports',
        'invoice_label' => 'Invoice Report',
        'cash_label' => 'Invoice Cash Report',
    ],

    /*
     * Point these to models in the application that installs this package.
     * They must be Eloquent models using the columns listed below. Every value
     * is required: the reports fail with an explanatory message if one is wrong.
     */
    'models' => [
        'invoice' => 'App\\Models\\Invoice',
        'payment' => 'App\\Models\\MakePayment',
        'expense' => 'App\\Models\\Expense',
        'warehouse' => 'App\\Models\\Warehouse',
    ],

    /*
     * Relation method names on the installed application's models. Set a value
     * to null when the application has no such relation; the reports then skip
     * it instead of failing, and fall back to the payment_method and expense
     * name columns for grouping.
     */
    'relations' => [
        'payment_invoice' => 'invoice',
        'payment_transaction' => 'transaction',
        'payment_customer' => 'invoice.customer',
        'expense_transaction' => 'transaction',
        'expense_warehouse' => 'warehouse',

        /* Relations eager loaded for each row of the invoice report. */
        'invoice_with' => ['customer', 'creator', 'warehouse'],
    ],

    /* Change these only if the host application uses different database columns. */
    'columns' => [
        'invoice_created_at' => 'created_at',
        'invoice_status' => 'status',
        'invoice_balance_due' => 'balance_due',
        'invoice_branch' => 'branch',
        'invoice_total' => 'total',
        'payment_date' => 'payment_date',
        'payment_amount' => 'amount',
        'payment_method' => 'payment_method',
        'expense_date' => 'date',
        'expense_amount' => 'amount_mmk',
    ],

    'invoice_status_value' => 'invoice',
    'invoice_balance_due_value' => 'Invoice',

    /* Admin users bypass branch restrictions. Other users use JSON IDs in this field. */
    'admin_flag_field' => 'is_admin',
    'admin_flag_value' => '1',
    'permission_field' => 'level',
    'admin_permission_value' => 'Admin',
];
