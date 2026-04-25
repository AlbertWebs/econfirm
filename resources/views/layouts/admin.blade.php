<!DOCTYPE html>
<html lang="en" class="admin-shell h-full max-h-[100dvh] overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — e-confirm</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/js/admin.js'])
    <style>
        /* Before Alpine hydrates: hide drawer on small screens only; md+ shows nav by default. */
        @media (max-width: 767.98px) {
            aside[aria-label='Admin navigation']:not(.admin-nav-drawer-open) {
                transform: translateX(-100%);
            }
        }
    </style>
    @stack('head')
</head>
<body class="flex h-full min-h-0 max-h-[100dvh] flex-col overflow-hidden bg-slate-50 font-sans text-slate-800 antialiased" x-data="adminShell()" @keydown.escape.window="closeSidebar()">
    {{-- Backdrop when menu drawer is open (all screen sizes) --}}
    <div
        x-show="sidebarOpen && isMobileNav"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm"
        @click="closeSidebar()"
        x-cloak
    ></div>

    <div class="flex h-full min-h-0 w-full min-w-0 flex-1 flex-row overflow-hidden">
        @php
            // Same routes & order; grouped for scanability (sidebar width stays w-64).
            $navGroups = [
                [
                    'heading' => null,
                    'items' => [
                        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                    ],
                ],
                [
                    'heading' => 'Escrow & payments',
                    'items' => [
                        ['route' => 'admin.transactions.index', 'pattern' => 'admin.transactions.*', 'label' => 'Escrow transactions', 'icon' => 'lock'],
                        ['route' => 'admin.business.index', 'pattern' => 'admin.business.*', 'label' => 'Business', 'icon' => 'chart-bar'],
                        ['route' => 'admin.velipay-payments.index', 'pattern' => 'admin.velipay-payments.*', 'label' => 'VeliPay communications', 'icon' => 'wallet'],
                    ],
                ],
                [
                    'heading' => 'People & cases',
                    'items' => [
                        ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Users', 'icon' => 'users'],
                        ['route' => 'admin.live-chats.index', 'pattern' => 'admin.live-chats.*', 'label' => 'Live chats', 'icon' => 'chat'],
                        ['route' => 'admin.disputes.index', 'pattern' => 'admin.disputes.*', 'label' => 'Raised disputes', 'icon' => 'exclamation-triangle'],
                        ['route' => 'admin.contact.index', 'pattern' => 'admin.contact.*', 'label' => 'Contact inbox', 'icon' => 'inbox'],
                        ['route' => 'admin.stk-contacts.index', 'pattern' => 'admin.stk-contacts.*', 'label' => 'STK contacts', 'icon' => 'users'],
                        ['route' => 'admin.scam-reports.index', 'pattern' => 'admin.scam-reports.*', 'label' => 'Scam reports', 'icon' => 'shield-exclamation'],
                    ],
                ],
                [
                    'heading' => 'Content',
                    'items' => [
                        ['route' => 'admin.support-help-items.index', 'pattern' => 'admin.support-help-items.*', 'label' => 'Support & help', 'icon' => 'lifebuoy'],
                        ['route' => 'admin.legal-pages.index', 'pattern' => 'admin.legal-pages.*', 'label' => 'Legal pages', 'icon' => 'book-open'],
                        ['route' => 'admin.pages.index', 'pattern' => 'admin.pages.*', 'label' => 'Pages (CMS)', 'icon' => 'document'],
                        ['route' => 'admin.blogs.index', 'pattern' => 'admin.blogs.*', 'label' => 'Insights / Blog', 'icon' => 'document'],
                    ],
                ],
                [
                    'heading' => 'System',
                    'items' => [
                        ['route' => 'admin.security.index', 'pattern' => 'admin.security.*', 'label' => 'Security', 'icon' => 'shield'],
                        ['route' => 'admin.api-access.index', 'pattern' => 'admin.api-access.*', 'label' => 'API & developers', 'icon' => 'code'],
                        ['route' => 'admin.activity-logs.index', 'pattern' => 'admin.activity-logs.*', 'label' => 'Activity log', 'icon' => 'clock'],
                        ['route' => 'admin.sms-logs.index', 'pattern' => 'admin.sms-logs.*', 'label' => 'SMS logs', 'icon' => 'chat'],
                        ['route' => 'admin.site-settings.edit', 'pattern' => 'admin.site-settings.*', 'label' => 'Site settings', 'icon' => 'cog'],
                    ],
                ],
            ];
        @endphp

        <aside
            class="fixed inset-y-0 left-0 z-50 flex h-full max-h-[100dvh] min-h-0 w-64 shrink-0 flex-col border-r border-slate-700/60 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-slate-200 shadow-2xl shadow-black/30 ring-1 ring-inset ring-white/[0.06] transition-transform duration-200 ease-out md:static md:inset-auto md:z-20 md:max-h-none md:shadow-none md:ring-0"
            :class="{
                'admin-nav-drawer-open': sidebarOpen,
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'md:hidden': !sidebarOpen,
            }"
            aria-label="Admin navigation"
            aria-hidden="{{ 'true' }}"
            x-bind:aria-hidden="(!sidebarOpen).toString()"
        >
            <div class="flex h-14 shrink-0 items-center gap-2.5 border-b border-slate-700/50 bg-slate-900/90 px-3 md:h-16 md:px-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 ring-1 ring-emerald-400/25">
                    <x-admin.icon name="shield" class="h-5 w-5 text-emerald-400" />
                </span>
                <div class="min-w-0">
                    <span class="block truncate text-sm font-semibold tracking-tight text-white">e-confirm</span>
                </div>
                <div class="ml-auto flex shrink-0 items-center gap-0.5">
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/60 active:scale-95"
                        x-show="sidebarOpen && !isMobileNav"
                        x-cloak
                        @click="sidebarOpen = false"
                        aria-label="Hide sidebar"
                    >
                        <x-admin.icon name="chevron-left" class="h-5 w-5" />
                    </button>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/60 active:scale-95 md:hidden"
                        @click="closeSidebar()"
                        aria-label="Close menu"
                    >
                        <x-admin.icon name="x" class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <nav class="scrollbar-y-hidden flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto overscroll-y-contain px-2 py-3" aria-label="Primary">
                @foreach ($navGroups as $group)
                    @if (! empty($group['heading']))
                        <p class="px-2 pb-1 pt-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 first:pt-0">{{ $group['heading'] }}</p>
                    @endif
                    <ul class="space-y-0.5" role="list">
                        @foreach ($group['items'] as $item)
                            @php
                                $active = request()->routeIs($item['pattern']);
                            @endphp
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    @click="closeSidebar()"
                                    class="group flex min-w-0 items-center gap-2.5 rounded-lg py-2 pl-2 pr-2 text-sm outline-none transition duration-150 ease-out focus-visible:ring-2 focus-visible:ring-emerald-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
                                    @class([
                                        'bg-emerald-500/[0.14] font-medium text-white shadow-sm ring-1 ring-emerald-400/25' => $active,
                                        'text-slate-400 hover:bg-white/[0.06] hover:text-slate-100' => ! $active,
                                    ])
                                    @if ($active) aria-current="page" @endif
                                >
                                    <span
                                        @class([
                                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-md transition-colors',
                                            'bg-emerald-500/20 text-emerald-300' => $active,
                                            'bg-slate-800/60 text-slate-500 group-hover:bg-slate-800 group-hover:text-slate-300' => ! $active,
                                        ])
                                    >
                                        <x-admin.icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                                    </span>
                                    <span class="min-w-0 flex-1 truncate leading-snug">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </nav>

            <div class="mt-auto shrink-0 border-t border-slate-700/50 bg-slate-950/40 px-2 pb-3 pt-2">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="group flex w-full min-w-0 items-center gap-2.5 rounded-lg border border-transparent px-2 py-2.5 text-left text-sm font-medium text-slate-400 outline-none transition duration-150 hover:border-red-500/20 hover:bg-red-950/25 hover:text-red-200 focus-visible:ring-2 focus-visible:ring-red-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 active:scale-[0.99]"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-slate-800/70 text-slate-500 transition group-hover:bg-red-950/40 group-hover:text-red-300">
                            <x-admin.icon name="logout" class="h-4 w-4 shrink-0" />
                        </span>
                        <span class="truncate">Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        @php
            $adminAuth = Auth::guard('admin')->user();
            $adminInitials = strtoupper(
                collect(preg_split('/\s+/', trim((string) $adminAuth->name), -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn (string $w) => mb_substr($w, 0, 1))
                    ->take(2)
                    ->implode('') ?: '?'
            );
            $adminHeaderMenu = [
                'Operations' => [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                    ['route' => 'admin.transactions.index', 'label' => 'Escrow transactions', 'icon' => 'lock'],
                    ['route' => 'admin.transactions.export', 'label' => 'Export escrows (CSV)', 'icon' => 'arrow-down-tray'],
                    ['route' => 'admin.business.index', 'label' => 'Business', 'icon' => 'chart-bar'],
                    ['route' => 'admin.velipay-payments.index', 'label' => 'VeliPay communications', 'icon' => 'wallet'],
                ],
                'People & inbox' => [
                    ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'users'],
                    ['route' => 'admin.live-chats.index', 'label' => 'Live chats', 'icon' => 'chat'],
                    ['route' => 'admin.disputes.index', 'label' => 'Raised disputes', 'icon' => 'exclamation-triangle'],
                    ['route' => 'admin.contact.index', 'label' => 'Contact inbox', 'icon' => 'inbox'],
                    ['route' => 'admin.scam-reports.index', 'label' => 'Scam reports', 'icon' => 'shield-exclamation'],
                    ['route' => 'admin.support-help-items.index', 'label' => 'Support & help', 'icon' => 'lifebuoy'],
                ],
                'Content & audit' => [
                    ['route' => 'admin.legal-pages.index', 'label' => 'Legal pages', 'icon' => 'book-open'],
                    ['route' => 'admin.pages.index', 'label' => 'Pages (CMS)', 'icon' => 'document'],
                    ['route' => 'admin.security.index', 'label' => 'Security (2FA & email)', 'icon' => 'shield'],
                    ['route' => 'admin.api-access.index', 'label' => 'API & developers', 'icon' => 'code'],
                    ['route' => 'admin.activity-logs.index', 'label' => 'Activity log', 'icon' => 'clock'],
                    ['route' => 'admin.sms-logs.index', 'label' => 'SMS logs', 'icon' => 'chat'],
                    ['route' => 'admin.site-settings.edit', 'label' => 'Site settings', 'icon' => 'cog'],
                ],
            ];
        @endphp

        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
            <header
                class="z-30 shrink-0 border-b border-slate-200/90 bg-gradient-to-b from-white via-white to-slate-50/90 px-4 shadow-[0_1px_0_0_rgba(15,23,42,0.04)] backdrop-blur-md supports-[backdrop-filter]:bg-white/75 md:px-6"
            >
                <div class="flex h-14 shrink-0 items-center gap-3 md:h-16 md:gap-4">
                    <button
                        type="button"
                        class="inline-flex shrink-0 rounded-xl border border-slate-200/90 bg-white p-2 text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                        x-show="!sidebarOpen"
                        x-cloak
                        @click="sidebarOpen = true"
                        aria-label="Show sidebar"
                    >
                        <x-admin.icon name="bars" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-700/90">Admin</p>
                        <h1 class="truncate text-base font-semibold tracking-tight text-slate-900 md:text-lg">
                            @yield('page_title', 'Admin')
                        </h1>
                    </div>

                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-900 sm:inline-flex"
                    >
                        <x-admin.icon name="arrow-top-right-on-square" class="h-4 w-4 text-emerald-600" />
                        View site
                    </a>

                    <div class="relative shrink-0" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button
                            type="button"
                            id="admin-user-menu-button"
                            class="flex max-w-[14rem] items-center gap-2 rounded-2xl border border-slate-200/90 bg-white py-1.5 pl-1.5 pr-2.5 text-left shadow-sm ring-1 ring-slate-900/[0.03] transition hover:border-emerald-200/80 hover:ring-emerald-500/15 md:max-w-xs md:pl-2 md:pr-3"
                            @click="open = !open"
                            @click.outside="open = false"
                            :aria-expanded="open"
                            aria-haspopup="true"
                            aria-controls="admin-user-menu"
                        >
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-xs font-bold tabular-nums text-white shadow-inner shadow-emerald-900/20"
                                aria-hidden="true"
                            >{{ $adminInitials }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-slate-900">{{ $adminAuth->name }}</span>
                                <span class="hidden truncate text-xs text-slate-500 md:block">{{ $adminAuth->email }}</span>
                            </span>
                            <span class="inline-flex text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                                <x-admin.icon name="chevron-down" class="h-4 w-4 shrink-0" />
                            </span>
                        </button>

                        <div
                            id="admin-user-menu"
                            role="menu"
                            aria-labelledby="admin-user-menu-button"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            x-cloak
                            class="scrollbar-y-hidden absolute right-0 z-50 mt-2 max-h-[min(28rem,calc(100vh-5rem))] w-[min(100vw-2rem,20rem)] origin-top-right overflow-y-auto overscroll-contain rounded-2xl border border-slate-200/90 bg-white py-2 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5"
                        >
                            <div class="border-b border-slate-100 px-4 pb-3 pt-1">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Signed in as</p>
                                <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $adminAuth->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $adminAuth->email }}</p>
                            </div>

                            @foreach ($adminHeaderMenu as $groupLabel => $items)
                                <div class="px-2 pt-2" role="none">
                                    <p class="px-2 pb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $groupLabel }}</p>
                                    <ul class="space-y-0.5" role="none">
                                        @foreach ($items as $item)
                                            <li role="none">
                                                <a
                                                    href="{{ route($item['route']) }}"
                                                    role="menuitem"
                                                    class="group flex items-center gap-2.5 rounded-xl px-2 py-2 text-sm text-slate-700 transition hover:bg-emerald-50/80 hover:text-emerald-950"
                                                    @click="open = false"
                                                >
                                                    <x-admin.icon :name="$item['icon']" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-emerald-600" />
                                                    <span class="min-w-0 leading-snug">{{ $item['label'] }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach

                            <div class="mx-2 my-2 border-t border-slate-100" role="separator"></div>

                            <div class="px-2">
                                <a
                                    href="{{ route('home') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    role="menuitem"
                                    class="flex items-center gap-2.5 rounded-xl px-2 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                    @click="open = false"
                                >
                                    <x-admin.icon name="arrow-top-right-on-square" class="h-4 w-4 shrink-0 text-emerald-600" />
                                    <span>Open public website</span>
                                </a>
                            </div>

                            <div class="mx-2 my-2 border-t border-slate-100" role="separator"></div>

                            <div class="px-2 pb-1">
                                <form action="{{ route('admin.logout') }}" method="POST" role="none">
                                    @csrf
                                    <button
                                        type="submit"
                                        role="menuitem"
                                        class="flex w-full items-center gap-2.5 rounded-xl px-2 py-2 text-left text-sm font-medium text-red-700 transition hover:bg-red-50"
                                    >
                                        <x-admin.icon name="logout" class="h-4 w-4 shrink-0 text-red-500" />
                                        <span>Log out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="scrollbar-y-hidden min-h-0 flex-1 overflow-y-auto overscroll-y-contain px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
