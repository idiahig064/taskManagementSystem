<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\TrashController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/edit/{task}', [TaskController::class, 'edit'])->name('tasks.edit');

    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.comments.store');

//    Route::put('/tasks/{id}/status', [TaskController::class, 'update'])->name('tasks.update');
//    Route::post('/tasks/{id}/attachments', [TaskController::class, 'store'])->name('tasks.store');
//    Route::post('/tasks/{id}/comments', [TaskController::class, 'store'])->name('tasks.store');

    Route::delete('/tasks/{task}/attachments', [TaskController::class, 'destroyAttachment'])->name('tasks.attachments.destroy');

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');

    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // Reporting routes
    Route::get('/reports/summary', [ReportingController::class, 'summary'])->name('reports.summary');
    Route::get('/reports/time-based', [ReportingController::class, 'timeBased'])->name('reports.time-based');
    Route::get('/reports/trend', [ReportingController::class, 'trend'])->name('reports.trend');
//    Route::get('/reports/overdue', [ReportingController::class, 'time-based']);
//    Route::get('/reports/weekly', [ReportingController::class, 'weekly']);
    Route::get('/reports/export/{type}', [ReportingController::class, 'export'])->name('reports.export');

    Route::get('/reports/category-tasks/{id}', function ($id) {
        $count = \App\Models\Task::where('category_id', $id)->count();
        return response()->json(['count' => $count]);
    });


// routes/web.php
    Route::prefix('trash')->group(function () {
        Route::get('/', [TrashController::class, 'index'])->name('trash.index');
        Route::post('/restore/{id}', [TrashController::class, 'restoreCategory'])->name('trash.restore');
    });
});

// Verify email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


require __DIR__.'/auth.php';
