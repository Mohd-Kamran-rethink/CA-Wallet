<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// AUTH ROUTES
Route::get('/',[AuthController::class,'loginView'])->name('loginView');
Route::post('/login',[AuthController::class,'login'])->name('login');
Route::get('/logout',[AuthController::class,'logout'])->name('logout');

// SETTINGS
Route::get('/project/settings',[SettingsController::class,'view'])->name('view')->middleware('ValidateUsers');
Route::post('/project/settings',[SettingsController::class,'add'])->name('add')->middleware('ValidateUsers');

// DASHBOARD WORD STARS
Route::get('/dashboard',[DashboardController::class,'view'])->name('view')->middleware('ValidateUsers');

// MANAGER CRUD
Route::get('/managers',[ManagerController::class,'list'])->name('list')->middleware('ValidateUsers');
Route::get('/managers/add',[ManagerController::class,'addView'])->name('addView')->middleware('ValidateUsers');
Route::post('/managers/add',[ManagerController::class,'add'])->name('add')->middleware('ValidateUsers');
Route::get('/managers/edit',[ManagerController::class,'addView'])->name('addView')->middleware('ValidateUsers');
Route::post('/managers/edit',[ManagerController::class,'edit'])->name('edit')->middleware('ValidateUsers');
Route::post('/managers/delete',[ManagerController::class,'delete'])->name('delete')->middleware('ValidateUsers');
