<?php

use App\Http\Controllers\MasterLists;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Handled by Admin middleware for redirection if logged in)
|--------------------------------------------------------------------------
*/

Route::middleware(['master'])->group(function () {
    // Show login form
    Route::get('/', [UserController::class, 'showLoginForm'])->name('login.form');

    // Login attempt
    Route::post('/login', [UserController::class, 'login'])->name('login');
});

/*
|--------------------------------------------------------------------------
| Logout (no middleware needed, just route)
|--------------------------------------------------------------------------
*/
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Require login)
|--------------------------------------------------------------------------
*/

Route::middleware(['master'])->group(function () {
    Route::get('/dashboard', function () {
        return view('master');
    })->name('dashboard');

    Route::get('/sync-member', [MasterLists::class, 'syncProfile'])->name('sync-member');
    Route::get('/test/{id}',[MasterLists::class, 'getPerson']);
    Route::get('/members-information', [MasterLists::class, 'showMember'])->name('members-information');
    Route::get('/person/{person}',[MasterLists::class,'getPerson'])->name('get-person');
    Route::post('/update-person',[MasterLists::class,'updatePerson'])->name('update-person');
    Route::get('/dd',[MasterLists::class,'debugMember'])->name('debug-member');
    Route::get('/loan',[MasterLists::class,'fetchLoans'])->name('fetch-loans');

    Route::get('/person-info/{person}', [MasterLists::class, 'getPersonInfo'])->name('get-person-info');

    //Loan Management
    Route::get('/co-maker-verifier', function () {
        return view('loan-management.co-maker-verifier');
    });
});
