<?php

return [
    /* Routes are registered automatically with the web and auth middleware. */
    'route_prefix' => 'reports',
    'middleware' => ['web', 'auth'],

    /*
     * Point these to models in the application that installs this package.
     * They must be Eloquent models using the columns listed below.
     */
    'models' => [
        'invoice' => App\Models\Invoice::class,
        'payment' => App\Models\MakePayment::class,
        'expense' => App\Models\Expense::class,
        'warehouse' => App\Models\Warehouse::class,
    ],

    /* Relation method names on the installed application's models. */
    'relations' => [
        'payment_invoice' => 'invoice',
        'payment_transaction' => 'transaction',
        'payment_customer' => 'invoice.customer',
        'expense_transaction' => 'transaction',
        'expense_warehouse' => 'warehouse',
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
