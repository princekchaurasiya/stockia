<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\InformationLinkController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InformationWebsiteController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DataSourceLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MarketActivityController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\UploadsController;
use App\Http\Controllers\Portal\AnnouncementController;
use App\Http\Controllers\Portal\CalendarController;
use App\Http\Controllers\Portal\ChartController;
use App\Http\Controllers\Portal\LiveClassController;
use App\Http\Controllers\Portal\NotesController;
use App\Http\Controllers\Portal\ResearchController;
use App\Http\Controllers\Nifty50ExtendedController;
use App\Http\Controllers\Nifty50ExtendedExportController;
use App\Http\Controllers\SheetExportController;
use App\Http\Controllers\SheetViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('dashboard', DashboardController::class)->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('sheet/export', SheetExportController::class)->name('sheet.export');
    Route::get('sheet/{sheet}', [SheetViewController::class, 'show'])->name('sheet.show');

    Route::get('nifty50-table', [Nifty50ExtendedController::class, 'index'])->name('nifty50.extended.index');
    Route::get('nifty50-table/export', Nifty50ExtendedExportController::class)->name('nifty50.extended.export');

    Route::get('market-activity', [MarketActivityController::class, 'index'])->name('market_activity.index');
    Route::post('market-activity/upload', [MarketActivityController::class, 'upload'])->name('market_activity.upload');
    Route::get('market-activity/download', [MarketActivityController::class, 'download'])->name('market_activity.download');

    Route::get('indices', [IndexController::class, 'index'])->name('indices.index');
    Route::get('indices/{slug}', [IndexController::class, 'show'])->name('indices.show');

    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('uploads', [UploadsController::class, 'index'])->name('uploads.index');
    Route::get('information-websites', [InformationWebsiteController::class, 'index'])->name('information.websites.index');
    Route::get('trading-learning', [LearningController::class, 'index'])->name('learning.index');
    Route::get('live-classes', [LiveClassController::class, 'index'])->name('live_classes.index');
    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('research', [ResearchController::class, 'index'])->name('research.index');
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('my-notes', [NotesController::class, 'index'])->name('notes.index');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
});

Route::get('data-source/{dataSourceLink}/open', [DataSourceLinkController::class, 'open'])
    ->name('data_source.open');
Route::get('data-source/{dataSourceLink}/download', [DataSourceLinkController::class, 'download'])
    ->name('data_source.download');

Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('trading-learning', '/admin/learning');
    Route::get('learning', fn () => view('admin.learning.dashboard'))->name('learning.dashboard');
    Route::get('learning/batches', fn () => view('admin.learning.batches.index'))->name('learning.batches.index');
    Route::get('learning/batches/{batch}', fn (\App\Models\Batch $batch) => view('admin.learning.batches.show', compact('batch')))->name('learning.batches.show');
    Route::get('learning/modules', fn () => view('admin.learning.modules.index'))->name('learning.modules.index');
    Route::get('learning/lectures', fn () => view('admin.learning.lectures.index'))->name('learning.lectures.index');
    Route::get('learning/videos', fn () => view('admin.learning.videos.index'))->name('learning.videos.index');
    Route::get('learning/documents', fn () => view('admin.learning.documents.index'))->name('learning.documents.index');
    Route::get('learning/students', fn () => view('admin.learning.enrollments.index'))->name('learning.enrollments.index');
});

Route::middleware(['auth', 'role:admin,superadmin', 'account.scope'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('data-source-links', [DataSourceLinkController::class, 'index'])->name('data_source_links.index');
    Route::post('data-source-links', [DataSourceLinkController::class, 'store'])->name('data_source_links.store');
    Route::get('data-source-links/{data_source_link}/edit', [DataSourceLinkController::class, 'edit'])->name('data_source_links.edit');
    Route::put('data-source-links/{data_source_link}', [DataSourceLinkController::class, 'update'])->name('data_source_links.update');
    Route::delete('data-source-links/{data_source_link}', [DataSourceLinkController::class, 'destroy'])->name('data_source_links.destroy');

    Route::get('information-links', [InformationLinkController::class, 'index'])->name('information_links.index');
    Route::get('information-links/create', [InformationLinkController::class, 'create'])->name('information_links.create');
    Route::post('information-links', [InformationLinkController::class, 'store'])->name('information_links.store');
    Route::get('information-links/{information_link}/edit', [InformationLinkController::class, 'edit'])->name('information_links.edit');
    Route::put('information-links/{information_link}', [InformationLinkController::class, 'update'])->name('information_links.update');
    Route::delete('information-links/{information_link}', [InformationLinkController::class, 'destroy'])->name('information_links.destroy');

    Route::post('impersonate/{user}', [ImpersonationController::class, 'store'])->name('impersonate.store');
});

Route::post('admin/stop-impersonation', [ImpersonationController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.impersonate.destroy');

Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('admins', [AdminUserController::class, 'index'])->name('admins.index');
    Route::get('admins/create', [AdminUserController::class, 'create'])->name('admins.create');
    Route::post('admins', [AdminUserController::class, 'store'])->name('admins.store');
});
