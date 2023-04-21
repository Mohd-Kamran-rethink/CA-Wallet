<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\StatusesController;
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
Route::get('/project/settings',[SettingsController::class,'view'])->name('view')->middleware('adminManager');
Route::post('/project/settings',[SettingsController::class,'add'])->name('add')->middleware('adminManager');

// DASHBOARD WORD STARS
Route::get('/dashboard',[DashboardController::class,'view'])->name('view')->middleware("ValidateUsers");

// MANAGER CRUD
Route::get('/managers',[UserController::class,'listManager'])->name('listManager')->middleware('adminManager');
Route::get('/managers/add',[UserController::class,'ManagerView'])->name('ManagerView')->middleware('adminManager');
Route::post('/managers/add',[UserController::class,'add'])->name('add')->middleware('adminManager');
Route::get('/managers/edit',[UserController::class,'ManagerView'])->name('ManagerView')->middleware('adminManager');
Route::post('/managers/edit',[UserController::class,'edit'])->name('edit')->middleware('adminManager');
Route::post('/managers/delete',[UserController::class,'delete'])->name('delete')->middleware('adminManager');
Route::get('/profile/edit',[UserController::class,'ManagerView'])->name('ManagerView')->middleware('adminManager');

// AGENT CRUD
Route::middleware('ValidateManager')->prefix('/agents')->group(function () {
    Route::get('',[UserController::class,'listAgents'])->name('listAgents');
    Route::get('/add',[UserController::class,'AgentView'])->name('AgentView');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::get('/edit',[UserController::class,'AgentView'])->name('AgentView');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
    Route::post('/delete',[UserController::class,'delete'])->name('delete');
});

// Sources
Route::middleware('adminManager')->prefix('/sources')->group(function () {
    Route::get('',[SourceController::class,'list'])->name('list');
    Route::get('/add',[SourceController::class,'addView'])->name('addView');
    Route::post('/add',[SourceController::class,'add'])->name('add');
    Route::get('/edit',[SourceController::class,'addView'])->name('addView');
    Route::post('/edit',[SourceController::class,'edit'])->name('edit');
    Route::post('/delete',[SourceController::class,'delete'])->name('delete');
});

// statuses
Route::middleware('adminManager')->prefix('/statuses')->group(function () {
    Route::get('',[StatusesController::class,'list'])->name('list');
    Route::get('/add',[StatusesController::class,'addView'])->name('addView');
    Route::post('/add',[StatusesController::class,'add'])->name('add');
    Route::get('/edit',[StatusesController::class,'addView'])->name('addView');
    Route::post('/edit',[StatusesController::class,'edit'])->name('edit');
    Route::post('/delete',[StatusesController::class,'delete'])->name('delete');
});

// leads
Route::middleware('ValidateUsers')->prefix('/leads')->group(function () {
    Route::get('',[LeadsController::class,'list'])->name('list');
    Route::get('/import',[LeadsController::class,'importView'])->name('importView');
    Route::post('/import',[LeadsController::class,'import'])->name('import');
    Route::post('/status/submit',[LeadsController::class,'submitStatus'])->name('submitStatus');
    Route::get('/download-sample-file',[LeadsController::class,'downloadfile'])->name('downloadfile');
    // followup leads
    Route::get('/follow-up',[LeadsController::class,'followUp'])->name('followUp');
});

// clients for agents
Route::middleware('ValidateAgent')->prefix('/clients')->group(function () {
    Route::get('',[ClientController::class,'list'])->name('list');
    Route::get('/add',[ClientController::class,'addView'])->name('addView');
    Route::post('/add',[ClientController::class,'add'])->name('add');
    Route::get('/edit',[ClientController::class,'addView'])->name('addView');
    Route::post('/edit',[ClientController::class,'edit'])->name('edit');
    Route::post('/delete',[ClientController::class,'delete'])->name('delete');
    Route::post('/redeposit',[ClientController::class,'redeposit'])->name('redeposit');
});

// attendance management
Route::get('/attendance/start-break', [AttendanceController::class, 'startBreak'])->name('start_break');
Route::get('/attendance/end-break', [AttendanceController::class, 'endBreak'])->name('end_break');

   
   
