@php
    $isAdmin = auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true);
    $isSuperAdmin = auth()->check() && (auth()->user()->role ?? '') === 'superadmin';

    $marketOpen = request()->routeIs(['market_activity.*', 'indices.*', 'reports.*', 'uploads.*', 'charts.*']);
    $learningOpen = request()->routeIs(['learning.index', 'notes.*', 'live_classes.*']);
    $portalOpen = request()->routeIs(['announcements.*', 'research.*', 'calendar.*', 'information.websites.*', 'admin.information_links.*']);
    $learningAdminOpen = request()->routeIs('admin.learning.*');
    $adminOpen = request()->routeIs(['admin.data_source_links.*', 'admin.admins.*']);
@endphp

<nav class="sidebar-nav flex-grow-1 overflow-y-auto pe-1">
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2"></i> {{ __('stockia.app.nav.dashboard') }}
    </a>

    @auth
        <details class="sidebar-group" @if($marketOpen) open @endif>
            <summary class="sidebar-group-toggle">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Market Data</span>
                <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
            </summary>
            <div class="sidebar-submenu">
                <a href="{{ route('market_activity.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('market_activity.*') ? 'active' : '' }}">Market Activity</a>
                <a href="{{ route('indices.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('indices.*') ? 'active' : '' }}">Indices</a>
                <a href="{{ route('reports.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
                <a href="{{ route('uploads.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('uploads.*') ? 'active' : '' }}">Uploads</a>
                <a href="{{ route('charts.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('charts.*') ? 'active' : '' }}">Charts</a>
            </div>
        </details>

        <details class="sidebar-group" @if($learningOpen) open @endif>
            <summary class="sidebar-group-toggle">
                <i class="bi bi-mortarboard"></i>
                <span>Learning</span>
                <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
            </summary>
            <div class="sidebar-submenu">
                <a href="{{ route('learning.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('learning.index') ? 'active' : '' }}">Trading Learning</a>
                <a href="{{ route('notes.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('notes.*') ? 'active' : '' }}">My Notes</a>
                <a href="{{ route('live_classes.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('live_classes.*') ? 'active' : '' }}">Live Classes</a>
            </div>
        </details>

        <details class="sidebar-group" @if($portalOpen) open @endif>
            <summary class="sidebar-group-toggle">
                <i class="bi bi-megaphone"></i>
                <span>Portal</span>
                <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
            </summary>
            <div class="sidebar-submenu">
                <a href="{{ route('information.websites.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('information.websites.*') ? 'active' : '' }}">{{ __('stockia.information_websites.nav') }}</a>
                <a href="{{ route('announcements.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('announcements.*') ? 'active' : '' }}">Announcements</a>
                <a href="{{ route('research.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('research.*') ? 'active' : '' }}">Research Hub</a>
                <a href="{{ route('calendar.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('calendar.*') ? 'active' : '' }}">Trading Calendar</a>
            </div>
        </details>

        @if ($isAdmin)
            <details class="sidebar-group sidebar-group-divider" @if($learningAdminOpen) open @endif>
                <summary class="sidebar-group-toggle">
                    <i class="bi bi-sliders"></i>
                    <span>Learning Admin</span>
                    <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
                </summary>
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.learning.dashboard') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.learning.batches.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.batches.*') ? 'active' : '' }}">Batches</a>
                    <a href="{{ route('admin.learning.enrollments.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.enrollments.*') ? 'active' : '' }}">Students</a>
                    <a href="{{ route('admin.learning.modules.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.modules.*') ? 'active' : '' }}">Modules</a>
                    <a href="{{ route('admin.learning.lectures.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.lectures.*') ? 'active' : '' }}">Lectures</a>
                    <a href="{{ route('admin.learning.videos.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.videos.*') ? 'active' : '' }}">Videos</a>
                    <a href="{{ route('admin.learning.documents.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.learning.documents.*') ? 'active' : '' }}">Documents</a>
                </div>
            </details>

            <details class="sidebar-group" @if($adminOpen) open @endif>
                <summary class="sidebar-group-toggle">
                    <i class="bi bi-shield-check"></i>
                    <span>Administration</span>
                    <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
                </summary>
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.data_source_links.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.data_source_links.*') ? 'active' : '' }}">{{ __('stockia.data_source.title') }}</a>
                    @if ($isSuperAdmin)
                        <a href="{{ route('admin.admins.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">{{ __('stockia.account.admins') }}</a>
                    @endif
                </div>
            </details>
        @endif
    @endauth

    <div class="sidebar-group sidebar-group-divider">
        <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>
    </div>
</nav>
