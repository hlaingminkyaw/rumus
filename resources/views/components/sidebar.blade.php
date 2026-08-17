{{--
    Add this once to the host application's sidebar:
    @include('composer-rumus::components.sidebar')

    The surrounding <li>, <ul>, classes, and icons deliberately belong to the
    host application so this works with Bootstrap, Tailwind, AdminLTE, etc.
--}}
@if (config('composer-rumus.sidebar.enabled', true))
    <li class="composer-rumus-sidebar-item">
        <span class="composer-rumus-sidebar-title">{{ config('composer-rumus.sidebar.title', 'Reports') }}</span>
        <ul class="composer-rumus-sidebar-menu">
            <li>
                <a href="{{ route('composer-rumus.invoice.index') }}"
                   @class(['active' => request()->routeIs('composer-rumus.invoice.*')])>
                    {{ config('composer-rumus.sidebar.invoice_label', 'Invoice Report') }}
                </a>
            </li>
            <li>
                <a href="{{ route('composer-rumus.cash.index') }}"
                   @class(['active' => request()->routeIs('composer-rumus.cash.*')])>
                    {{ config('composer-rumus.sidebar.cash_label', 'Invoice Cash Report') }}
                </a>
            </li>
        </ul>
    </li>
@endif
