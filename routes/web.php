<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ReportController;
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
Route::get('/logout',[AuthController::class,'logout'])->name('logout')->middleware('ValidateUsers');

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
    Route::get('/duplicate',[LeadsController::class,'duplicateLeads'])->name('duplicateLeads');
    Route::get('/import',[LeadsController::class,'importView'])->name('importView');
    Route::post('/import',[LeadsController::class,'import'])->name('import');
    Route::post('/status/submit',[LeadsController::class,'submitStatus'])->name('submitStatus');
    Route::get('/download-sample-file',[LeadsController::class,'downloadfile'])->name('downloadfile');
    // followup leads
    Route::get('/demoid',[LeadsController::class,'demoIdLeads'])->name('demoIdLeads');
    Route::get('/idcreated',[LeadsController::class,'createdIdLeads'])->name('createdIdLeads');
    Route::get('/callback',[LeadsController::class,'callbackLeads'])->name('callbackLeads');
    Route::post('/status/mass/submit',[LeadsController::class,'massStatusChange'])->name('massStatusChange');
    Route::post('/agent/mass/change',[LeadsController::class,'massAgentChange'])->name('massAgentChange');
    // for approval leads only show to default manager
    Route::get('/approval',[LeadsController::class,'nonApprovedLeads'])->name('nonApprovedLeads');
    Route::post('/acceptapproval',[LeadsController::class,'approveLead'])->name('approveLead');
    Route::post('/delete',[LeadsController::class,'deleteLeads'])->name('deleteLeads');
    Route::get('/add',[LeadsController::class,'mannualAdd'])->name('mannualAdd');
    // leads import by manager is different
    Route::post('/manager/import',[LeadsController::class,'leadsImportByManager'])->name('leadsImportByManager');

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
    Route::get('/deposit/history/{id}',[ClientController::class,'depositHistory'])->name('depositHistory');
});

// attendance management
Route::middleware('ValidateUsers')->prefix('/attendance')->group(function () {
    Route::get('/start-break', [AttendanceController::class, 'startBreak'])->name('start_break');
    Route::get('/end-break', [AttendanceController::class, 'endBreak'])->name('end_break');
    Route::get('/', [AttendanceController::class, 'list'])->name('list');
    Route::get('/viewActivity', [AttendanceController::class, 'viewActivity'])->name('viewActivity');
});

// repors
Route::middleware('ValidateManager')->prefix('/reports')->group(function () {
    Route::get('leads', [ReportController::class, 'leadsReport'])->name('leadsReport');
    Route::get('deposits', [ReportController::class, 'deposits'])->name('deposits');
    Route::post('/leads/export', [ReportController::class, 'exportLeads'])->name('exportLeads');
    Route::post('/deposits/export', [ReportController::class, 'exportDeposit'])->name('exportDeposit');
});
   
   
