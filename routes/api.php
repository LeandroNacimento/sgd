<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DocumentAttachmentController;
use App\Http\Controllers\Api\V1\DocumentAuditController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\DocumentVersionController;
use App\Http\Controllers\Api\V1\DocumentWorkflowController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

        // Documents CRUD
        Route::apiResource('documents', DocumentController::class);

        // Document Versions
        Route::get('/documents/{document}/versions', [DocumentVersionController::class, 'index']);
        Route::get('/documents/{document}/versions/{version}', [DocumentVersionController::class, 'show']);

        // Document Workflow
        Route::post('/documents/{document}/workflow/submit', [DocumentWorkflowController::class, 'submit']);
        Route::post('/documents/{document}/workflow/publish', [DocumentWorkflowController::class, 'publish']);
        Route::post('/documents/{document}/workflow/reject', [DocumentWorkflowController::class, 'reject']);
        Route::post('/documents/{document}/workflow/archive', [DocumentWorkflowController::class, 'archive']);
        Route::post('/documents/{document}/workflow/new-version', [DocumentWorkflowController::class, 'newVersion']);

        // Document Attachments
        Route::get('/documents/{document}/attachments', [DocumentAttachmentController::class, 'index']);
        Route::post('/documents/{document}/attachments', [DocumentAttachmentController::class, 'store']);
        Route::get('/documents/{document}/attachments/{media}/download', [DocumentAttachmentController::class, 'download'])->name('api.v1.documents.attachments.download');
        Route::delete('/documents/{document}/attachments/{media}', [DocumentAttachmentController::class, 'destroy']);

        // Document Audit
        Route::get('/documents/{document}/audit', [DocumentAuditController::class, 'index']);
    });
});
