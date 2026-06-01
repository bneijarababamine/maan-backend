<?php

use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\BankTransferController;
use App\Http\Controllers\API\ContributionController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DonationController;
use App\Http\Controllers\API\DonationTypeController;
use App\Http\Controllers\API\DonorController;
use App\Http\Controllers\API\FamilyController;
use App\Http\Controllers\API\GuardianController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\OrphanController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\WilayaController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/revenue', [DashboardController::class, 'revenue']);
    Route::get('/dashboard/unpaid-members', [DashboardController::class, 'unpaidMembers']);
    Route::get('/dashboard/near-18-orphans', [DashboardController::class, 'near18Orphans']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);
    Route::get('/dashboard/treasury', [DashboardController::class, 'treasury']);

    // Members
    Route::apiResource('members', MemberController::class);
    Route::get('members/{id}/contributions', [MemberController::class, 'contributions']);
    Route::get('members/{id}/unpaid-months', [MemberController::class, 'unpaidMonths']);
    Route::get('members/{id}/paid-months', [MemberController::class, 'paidMonths']);

    // Contributions
    Route::apiResource('contributions', ContributionController::class)->except(['update']);
    Route::put('contributions/{id}', [ContributionController::class, 'update']);

    // Donors
    Route::apiResource('donors', DonorController::class);
    Route::get('donors/{id}/donations', [DonorController::class, 'donations']);

    // Donations
    Route::apiResource('donations', DonationController::class);
    Route::apiResource('donation-types', DonationTypeController::class)->except(['show']);

    // Orphans
    Route::apiResource('orphans', OrphanController::class);
    Route::post('orphans/{id}/deactivate',  [OrphanController::class, 'deactivate']);
    Route::post('orphans/{id}/reactivate', [OrphanController::class, 'reactivate']);
    Route::get('orphans/{id}/siblings', [OrphanController::class, 'siblings']);
    Route::post('orphans/{id}/siblings', [OrphanController::class, 'addSibling']);
    Route::delete('orphans/{orphanId}/siblings/{siblingId}', [OrphanController::class, 'removeSibling']);
    Route::get('orphans/{id}/benefits', [OrphanController::class, 'benefits']);

    // Guardians
    Route::apiResource('guardians', GuardianController::class);
    Route::post('guardians/check-phone', [GuardianController::class, 'checkByPhone']);
    Route::get('guardians/{id}/orphans', [GuardianController::class, 'orphans']);

    // Families
    Route::apiResource('families', FamilyController::class);

    // Wilayas
    Route::get('wilayas', [WilayaController::class, 'index']);
    Route::post('wilayas', [WilayaController::class, 'store']);
    Route::put('wilayas/{id}', [WilayaController::class, 'update']);
    Route::delete('wilayas/{id}', [WilayaController::class, 'destroy']);

    // Settings
    Route::get('settings', [SettingsController::class, 'index']);
    Route::post('settings', [SettingsController::class, 'update']);

    // Banks
    Route::get('banks', [BankController::class, 'index']);
    Route::post('banks', [BankController::class, 'store']);
    Route::put('banks/{id}', [BankController::class, 'update']);
    Route::delete('banks/{id}', [BankController::class, 'destroy']);

    // Activities
    Route::apiResource('activities', ActivityController::class);
    Route::post('activities/{id}/photos', [ActivityController::class, 'addPhotos']);
    Route::delete('activities/{id}/photos/{photoId}', [ActivityController::class, 'deletePhoto']);
    Route::post('activities/{id}/beneficiaries', [ActivityController::class, 'addBeneficiaries']);
    Route::delete('activities/{id}/beneficiaries/{benefId}', [ActivityController::class, 'removeBeneficiary']);
    Route::post('activities/{id}/items', [ActivityController::class, 'addItems']);
    Route::delete('activities/{id}/items/{itemId}', [ActivityController::class, 'removeItem']);

    // Global search
    Route::get('search', [SearchController::class, 'search']);

    // Bank transfers
    Route::get('bank-transfers', [BankTransferController::class, 'index']);
    Route::post('bank-transfers', [BankTransferController::class, 'store']);
    Route::put('bank-transfers/{id}', [BankTransferController::class, 'update']);
    Route::delete('bank-transfers/{id}', [BankTransferController::class, 'destroy']);
});
