<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Coordinator\AnnouncementBroadcastController;
use App\Http\Controllers\Coordinator\AwardController;
use App\Http\Controllers\Coordinator\ParticipationApprovalController;
use App\Http\Controllers\Coordinator\UserManagementController;
use App\Http\Controllers\Itc\ActivityController as ItcActivityController;
use App\Http\Controllers\Itc\ClusterController as ItcClusterController;
use App\Http\Controllers\Lecturer\VerificationController;
use App\Http\Controllers\Management\SearchController as ManagementSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\ReportExportController;
use App\Http\Controllers\Student\ActivityController as StudentActivityController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Student\ParticipationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| STUDENT AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return match ($role) {
            \App\Enums\UserRole::Coordinator => redirect()->route('coordinator.dashboard'),
            \App\Enums\UserRole::Lecturer => redirect()->route('lecturer.dashboard'),
            \App\Enums\UserRole::ItcAdmin => redirect()->route('itc.dashboard'),
            \App\Enums\UserRole::Committee => redirect()->route('committee.dashboard'),
            \App\Enums\UserRole::StudentAffairs => redirect()->route('affairs.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    })->name('dashboard');
});

// Shared: leaderboard accessible to all authenticated roles
Route::middleware(['auth'])->group(function () {
    Route::get('/leaderboard', [StudentController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/history', [StudentController::class, 'history'])->name('history');
});

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', fn () => view('dashboard'))->name('student.dashboard');

    // Profile
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [StudentController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('profile.update');

    // Achievements
    Route::resource('achievements', AchievementController::class);

    // Activities & Participations
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('activities', [StudentActivityController::class, 'index'])->name('activities.index');
        Route::get('activities/{activity}', [StudentActivityController::class, 'show'])->name('activities.show');

        Route::get('participations', [ParticipationController::class, 'index'])->name('participations.index');
        Route::get('activities/{activity}/join', [ParticipationController::class, 'create'])->name('participations.create');
        Route::post('activities/{activity}/join', [ParticipationController::class, 'store'])->name('participations.store');
        Route::get('participations/{participation}', [ParticipationController::class, 'show'])->name('participations.show');
        Route::get('participations/{participation}/upload', [ParticipationController::class, 'edit'])->name('participations.edit');
        Route::put('participations/{participation}/upload', [ParticipationController::class, 'update'])->name('participations.update');

        Route::get('announcements', [StudentAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/{announcement}', [StudentAnnouncementController::class, 'show'])->name('announcements.show');

        Route::get('notifications', [StudentNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [StudentNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [StudentNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    });
});

/*
|--------------------------------------------------------------------------
| COORDINATOR AREA (Talent Unit FSKTM)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Achievement approvals
    Route::get('/approvals', [AchievementController::class, 'adminIndex'])->name('approvals');
    Route::post('/approvals/bulk-approve', [AchievementController::class, 'bulkApprove'])->name('approvals.bulk-approve');
    Route::post('/approvals/bulk-reject', [AchievementController::class, 'bulkReject'])->name('approvals.bulk-reject');

    Route::get('/achievements/history', [AchievementController::class, 'history'])->name('achievements.history');
    Route::post('/achievements/{id}/approve', [AchievementController::class, 'approve'])->name('achievements.approve');
    Route::post('/achievements/{id}/reject', [AchievementController::class, 'reject'])->name('achievements.reject');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');

    // Students management
    Route::get('/students', [\App\Http\Controllers\Admin\StudentManagementController::class, 'index'])->name('students');
    Route::get('/students/{id}/edit', [\App\Http\Controllers\Admin\StudentManagementController::class, 'edit'])->name('students.edit');
    Route::post('/students/{id}/update', [\App\Http\Controllers\Admin\StudentManagementController::class, 'update'])->name('students.update');
    Route::post('/students/{student}/archive', [UserManagementController::class, 'archiveStudent'])->name('students.archive');

    // Category leaderboard
    Route::get('/category/{id}/leaderboard', [StudentController::class, 'categoryLeaderboard'])->name('category.leaderboard');

    // User management
    Route::resource('users', UserManagementController::class)->except(['show']);

    // Awards
    Route::get('/awards', [AwardController::class, 'index'])->name('awards.index');
    Route::get('/awards/create', [AwardController::class, 'create'])->name('awards.create');
    Route::post('/awards', [AwardController::class, 'store'])->name('awards.store');
    Route::get('/awards/top', [AwardController::class, 'topPerformers'])->name('awards.top');

    // Participation approvals (two-stage: lecturer -> coordinator)
    Route::get('/participation-approvals', [ParticipationApprovalController::class, 'index'])->name('participation-approvals.index');
    Route::get('/participation-approvals/history', [ParticipationApprovalController::class, 'history'])->name('participation-approvals.history');
    Route::get('/participation-approvals/{participation}', [ParticipationApprovalController::class, 'show'])->name('participation-approvals.show');
    Route::post('/participation-approvals/{participation}/approve', [ParticipationApprovalController::class, 'approve'])->name('participation-approvals.approve');
    Route::post('/participation-approvals/{participation}/reject', [ParticipationApprovalController::class, 'reject'])->name('participation-approvals.reject');

    // Announcements
    Route::get('/announcements', [AnnouncementBroadcastController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementBroadcastController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementBroadcastController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementBroadcastController::class, 'destroy'])->name('announcements.destroy');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| LECTURER AREA (Accompanying Lecturer)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [VerificationController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/history', [VerificationController::class, 'history'])->name('verifications.history');
    Route::get('/verifications/{participation}', [VerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{participation}/verify', [VerificationController::class, 'verify'])->name('verifications.verify');
    Route::post('/verifications/{participation}/reject', [VerificationController::class, 'reject'])->name('verifications.reject');

    // Two-stage self-submitted achievements
    Route::get('/achievement-verifications', [\App\Http\Controllers\Lecturer\AchievementVerificationController::class, 'index'])->name('achievement-verifications.index');
    Route::get('/achievement-verifications/history', [\App\Http\Controllers\Lecturer\AchievementVerificationController::class, 'history'])->name('achievement-verifications.history');
    Route::get('/achievement-verifications/{achievement}', [\App\Http\Controllers\Lecturer\AchievementVerificationController::class, 'show'])->name('achievement-verifications.show');
    Route::post('/achievement-verifications/{achievement}/verify', [\App\Http\Controllers\Lecturer\AchievementVerificationController::class, 'verify'])->name('achievement-verifications.verify');
    Route::post('/achievement-verifications/{achievement}/reject', [\App\Http\Controllers\Lecturer\AchievementVerificationController::class, 'reject'])->name('achievement-verifications.reject');
});

/*
|--------------------------------------------------------------------------
| ITC ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'itc'])->prefix('itc')->name('itc.')->group(function () {
    Route::get('/dashboard', [ItcActivityController::class, 'dashboard'])->name('dashboard');

    Route::resource('activities', ItcActivityController::class)->except(['show']);
    Route::post('activities/{activity}/invite', [ItcActivityController::class, 'inviteMatchedStudents'])->name('activities.invite');

    Route::get('/talent-search', [ManagementSearchController::class, 'index'])->name('talent-search');

    Route::get('/clusters', [ItcClusterController::class, 'index'])->name('clusters.index');
    Route::post('/clusters', [ItcClusterController::class, 'store'])->name('clusters.store');
    Route::put('/clusters/{cluster}', [ItcClusterController::class, 'update'])->name('clusters.update');
    Route::delete('/clusters/{cluster}', [ItcClusterController::class, 'destroy'])->name('clusters.destroy');
});

/*
|--------------------------------------------------------------------------
| MANAGEMENT (Committee + Student Affairs + Coordinator) — Read-only reports
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'management'])->prefix('management')->name('management.')->group(function () {
    Route::get('/reports/achievements', [ReportExportController::class, 'achievements'])->name('reports.achievements');
    Route::get('/reports/students', [ReportExportController::class, 'students'])->name('reports.students');
});

// Committee dashboard
Route::middleware(['auth', 'management'])->prefix('committee')->name('committee.')->group(function () {
    Route::get('/dashboard', [ManagementSearchController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [ManagementSearchController::class, 'index'])->name('search');
});

// Shared: notify selected students (ITC + Management)
Route::middleware(['auth'])->post('/notify-students', [ManagementSearchController::class, 'notifySelected'])->name('notify.students');

// Student Affairs dashboard
Route::middleware(['auth', 'management'])->prefix('affairs')->name('affairs.')->group(function () {
    Route::get('/dashboard', [ReportsController::class, 'dashboard'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Legacy admin.* aliases for backward compat
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'coordinator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/approvals', [AchievementController::class, 'adminIndex'])->name('approvals');
    Route::post('/approvals/bulk-approve', [AchievementController::class, 'bulkApprove'])->name('approvals.bulk-approve');
    Route::post('/approvals/bulk-reject', [AchievementController::class, 'bulkReject'])->name('approvals.bulk-reject');
    Route::get('/achievements/history', [AchievementController::class, 'history'])->name('achievements.history');
    Route::post('/achievements/{id}/approve', [AchievementController::class, 'approve'])->name('achievements.approve');
    Route::post('/achievements/{id}/reject', [AchievementController::class, 'reject'])->name('achievements.reject');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');

    Route::get('/students', [\App\Http\Controllers\Admin\StudentManagementController::class, 'index'])->name('students');
    Route::get('/students/{id}/edit', [\App\Http\Controllers\Admin\StudentManagementController::class, 'edit'])->name('students.edit');
    Route::post('/students/{id}/update', [\App\Http\Controllers\Admin\StudentManagementController::class, 'update'])->name('students.update');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');

    Route::get('/participation-approvals', [ParticipationApprovalController::class, 'index'])->name('participation-approvals.index');
    Route::get('/participation-approvals/history', [ParticipationApprovalController::class, 'history'])->name('participation-approvals.history');
    Route::get('/participation-approvals/{participation}', [ParticipationApprovalController::class, 'show'])->name('participation-approvals.show');
    Route::post('/participation-approvals/{participation}/approve', [ParticipationApprovalController::class, 'approve'])->name('participation-approvals.approve');
    Route::post('/participation-approvals/{participation}/reject', [ParticipationApprovalController::class, 'reject'])->name('participation-approvals.reject');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
