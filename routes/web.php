<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SourceController;
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
Route::get('/managers',[UserController::class,'listManager'])->name('listManager')->middleware('ValidateUsers');
Route::get('/managers/add',[UserController::class,'ManagerView'])->name('ManagerView')->middleware('ValidateUsers');
Route::post('/managers/add',[UserController::class,'add'])->name('add')->middleware('ValidateUsers');
Route::get('/managers/edit',[UserController::class,'ManagerView'])->name('ManagerView')->middleware('ValidateUsers');
Route::post('/managers/edit',[UserController::class,'edit'])->name('edit')->middleware('ValidateUsers');
Route::post('/managers/delete',[UserController::class,'delete'])->name('delete')->middleware('ValidateUsers');
Route::get('/profile/edit',[UserController::class,'ManagerView'])->name('ManagerView')->middleware('ValidateUsers');

// AGENT CRUD
Route::middleware('ValidateUsers')->prefix('/agents')->group(function () {
    Route::get('',[UserController::class,'listAgents'])->name('listAgents');
    Route::get('/add',[UserController::class,'AgentView'])->name('AgentView');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::get('/edit',[UserController::class,'AgentView'])->name('AgentView');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
    Route::post('/delete',[UserController::class,'delete'])->name('delete');
});

// Sources
Route::middleware('ValidateUsers')->prefix('/sources')->group(function () {
    Route::get('',[SourceController::class,'list'])->name('list');
    Route::get('/add',[SourceController::class,'addView'])->name('addView');
    Route::post('/add',[SourceController::class,'add'])->name('add');
    Route::get('/edit',[SourceController::class,'addView'])->name('addView');
    Route::post('/edit',[SourceController::class,'edit'])->name('edit');
    Route::post('/delete',[SourceController::class,'delete'])->name('delete');
});

// leads
Route::middleware('ValidateUsers')->prefix('/leads')->group(function () {
    Route::get('',[LeadsController::class,'list'])->name('list');
    Route::get('/import',[LeadsController::class,'importView'])->name('importView');
    Route::post('/import',[LeadsController::class,'import'])->name('import');
    Route::get('/add',[LeadsController::class,'addView'])->name('addView');
    Route::post('/add',[LeadsController::class,'add'])->name('add');
    Route::get('/edit',[LeadsController::class,'addView'])->name('addView');
    Route::post('/edit',[LeadsController::class,'edit'])->name('edit');
    Route::post('/delete',[LeadsController::class,'delete'])->name('delete');
});
