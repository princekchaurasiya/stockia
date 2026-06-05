<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Stockia') – Market Data</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @livewireStyles
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/charts.jsx'])
    @endif
    @stack('styles')
</head>
<body class="bg-light stockia-app">
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-primary">Stockia</span>

        <div class="ms-auto d-flex align-items-center gap-3">
            @auth
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                            id="accountMenu"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                </div>
            @endauth
        </div>
    </div>
</nav>

<div class="d-flex min-vh-100">
    {{-- Sidebar --}}
    <aside class="app-sidebar bg-white border-end vh-100 p-3 position-sticky top-0 d-flex flex-column">
        <div class="sidebar-brand mb-3">Stockia</div>
        @include('layouts.partials.sidebar-nav')
        <div class="mt-auto pt-4 border-top">
            @auth
                @if(session('impersonator_id'))
                    <form action="{{ route('admin.impersonate.destroy') }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm w-100">{{ __('stockia.impersonation.stop') }}</button>
                    </form>
                @endif
            @endauth
        </div>
    </aside>

    {{-- Content --}}
    <main class="flex-fill p-4 overflow-auto">
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
<script>
    (function () {
        function hideAllColumnHelpPopovers() {
            document.querySelectorAll('.column-help-popover').forEach(function (el) {
                el.classList.remove('column-help-active');
                el.setAttribute('aria-expanded', 'false');

                var instance = bootstrap.Popover.getInstance(el);
                if (instance) {
                    instance.hide();
                }
            });
        }

        function ensureColumnHelpPopover(el) {
            var instance = bootstrap.Popover.getInstance(el);
            if (instance) {
                return instance;
            }

            instance = new bootstrap.Popover(el, {
                container: 'body',
                customClass: 'column-help-popover-panel',
                trigger: 'manual',
                focus: false,
                sanitize: true,
            });

            el.addEventListener('hidden.bs.popover', function () {
                el.classList.remove('column-help-active');
                el.setAttribute('aria-expanded', 'false');
            });

            return instance;
        }

        function initColumnHelpPopovers(root) {
            (root || document).querySelectorAll('.column-help-popover').forEach(function (el) {
                ensureColumnHelpPopover(el);
            });
        }

        document.addEventListener('click', function (event) {
            var helpButton = event.target.closest('.column-help-popover');

            if (helpButton) {
                event.preventDefault();
                event.stopPropagation();

                var popover = ensureColumnHelpPopover(helpButton);
                var wasActive = helpButton.classList.contains('column-help-active');

                hideAllColumnHelpPopovers();

                if (!wasActive) {
                    popover.show();
                    helpButton.classList.add('column-help-active');
                    helpButton.setAttribute('aria-expanded', 'true');
                }

                return;
            }

            if (event.target.closest('.popover')) {
                return;
            }

            hideAllColumnHelpPopovers();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                hideAllColumnHelpPopovers();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            initColumnHelpPopovers();
        });

        document.addEventListener('livewire:init', function () {
            Livewire.hook('morph.updated', function () {
                initColumnHelpPopovers(document);
            });

            Livewire.hook('commit', function ({ succeed }) {
                succeed(function () {
                    requestAnimationFrame(function () {
                        hideAllColumnHelpPopovers();
                        initColumnHelpPopovers(document);
                    });
                });
            });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
