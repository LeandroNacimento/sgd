<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentAttachmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentVersionController;
use App\Http\Controllers\DocumentWorkflowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('documents', DocumentController::class);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');

    // Document Versions
    Route::post('documents/{document}/versions', [DocumentVersionController::class, 'store'])->name('documents.versions.store');
    Route::post('documents/{document}/versions/{version}/revert', [DocumentVersionController::class, 'revert'])->name('documents.versions.revert');

    // Document Attachments
    Route::post('documents/{document}/attachments', [DocumentAttachmentController::class, 'store'])->name('documents.attachments.store');
    Route::delete('documents/{document}/attachments/{media}', [DocumentAttachmentController::class, 'destroy'])->name('documents.attachments.destroy');
    Route::get('documents/{document}/attachments/{media}/download', [DocumentAttachmentController::class, 'download'])->name('documents.attachments.download');

    // Document Workflow
    Route::post('documents/{document}/workflow/submit-for-review', [DocumentWorkflowController::class, 'submitForReview'])->name('documents.workflow.submitForReview');
    Route::post('documents/{document}/workflow/publish', [DocumentWorkflowController::class, 'publish'])->name('documents.workflow.publish');
    Route::post('documents/{document}/workflow/reject', [DocumentWorkflowController::class, 'reject'])->name('documents.workflow.reject');
    Route::post('documents/{document}/workflow/archive', [DocumentWorkflowController::class, 'archive'])->name('documents.workflow.archive');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Administrator-only routes
Route::middleware(['auth', 'can:is-admin'])->group(function () {
    // Routes added in future phases
});

// Operator and Administrator routes
Route::middleware(['auth', 'can:is-operator'])->group(function () {
    // Routes added in future phases
});

require __DIR__.'/auth.php';
