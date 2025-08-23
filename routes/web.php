<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If user is already authenticated, redirect based on role
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('coordinators.dashboard');
        } elseif ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }
    }
    // If user is not authenticated, redirect to login
    return redirect()->route('login');
});

// Admin/Coordinator Routes - Protected by admin role
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('coordinators')
    ->name('coordinators.')
    ->group(function () {
        Route::view('/dashboard', 'coordinators.dashboard.index')->name('dashboard');
        Route::view('/intern-progress-tracker', 'coordinators.intern-progress-tracker.index')->name('intern-progress-tracker');
        Route::view('/requirements-management', 'coordinators.requirements-management.index')->name('requirements-management');
        Route::view('/tasks-management', 'coordinators.tasks-management.index')->name('tasks-management');
        Route::view('/logbook-review', 'coordinators.logbook-review.index')->name('logbook-review');
        Route::view('/documentation-output-uploads', 'coordinators.documentation-output-uploads.index')->name('documentation-output-uploads');
        Route::view('/evaluation-feedback', 'coordinators.evaluation-feedback.index')->name('evaluation-feedback');
    });

// Student Routes - Protected by student role
Route::middleware(['auth', 'verified', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [StudentDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::view('/requirements-checklist', 'student-interns.requirements-checklist.index')->name('requirements-checklist');
        Route::view('/logbook', 'student-interns.logbook.index')->name('logbook');
        Route::view('/documentions-uploads-output', 'student-interns.documentions-uploads-output.index')->name('documentions-uploads-output');
        Route::view('/evaluation-forms', 'student-interns.evaluation-forms.index')->name('evaluation-forms');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
