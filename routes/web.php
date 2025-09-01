<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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
        Route::get('/dashboard', [App\Http\Controllers\CoordinatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/intern-progress-tracker', [App\Http\Controllers\CoordinatorController::class, 'internProgressTracker'])->name('intern-progress-tracker');
        Route::view('/requirements-management', 'coordinators.requirements-management.index')->name('requirements-management');
        Route::get('/tasks-management', [App\Http\Controllers\CoordinatorController::class, 'tasksManagement'])->name('tasks-management');
        Route::get('/tasks-management/create', [App\Http\Controllers\CoordinatorController::class, 'createTask'])->name('tasks-management.create');
        Route::post('/tasks-management', [App\Http\Controllers\CoordinatorController::class, 'storeTask'])->name('tasks-management.store');
        Route::get('/logbook-review', [App\Http\Controllers\CoordinatorController::class, 'logbookReview'])->name('logbook-review');
        Route::post('/logbook-review/{id}/approve', [App\Http\Controllers\CoordinatorController::class, 'approveLogbookEntry'])->name('logbook-review.approve');
        Route::post('/logbook-review/{id}/reject', [App\Http\Controllers\CoordinatorController::class, 'rejectLogbookEntry'])->name('logbook-review.reject');
        Route::view('/internship-output-archive', 'coordinators.internship-output-archive.index')->name('internship-output-archive');
        Route::view('/evaluation-feedback', 'coordinators.evaluation-feedback.index')->name('evaluation-feedback');
        // Create Intern routes
        Route::get('/create-intern', [App\Http\Controllers\CoordinatorController::class, 'showCreateInternForm'])->name('create-intern');
        Route::post('/create-intern', [App\Http\Controllers\CoordinatorController::class, 'createIntern']);
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
        
        // Assigned Tasks routes
        Route::get('/assigned-tasks', [App\Http\Controllers\Student\AssignedTaskController::class, 'index'])->name('assigned-tasks');
        Route::patch('/assigned-tasks/{task}/start', [App\Http\Controllers\Student\AssignedTaskController::class, 'startTask'])->name('assigned-tasks.start');
        Route::patch('/assigned-tasks/{task}/complete', [App\Http\Controllers\Student\AssignedTaskController::class, 'completeTask'])->name('assigned-tasks.complete');
        
        // Logbook routes
        Route::get('/logbook', [App\Http\Controllers\Student\LogbookController::class, 'index'])->name('logbook');
        Route::post('/logbook/time-in', [App\Http\Controllers\Student\LogbookController::class, 'timeIn'])->name('logbook.time-in');
        Route::post('/logbook/time-out', [App\Http\Controllers\Student\LogbookController::class, 'timeOut'])->name('logbook.time-out');
        Route::get('/logbook/entries', [App\Http\Controllers\Student\LogbookController::class, 'getEntries'])->name('logbook.entries');
        
        Route::view('/documentions-uploads-output', 'student-interns.documentions-uploads-output.index')->name('documentions-uploads-output');
        Route::view('/evaluation-forms', 'student-interns.evaluation-forms.index')->name('evaluation-forms');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
