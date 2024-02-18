<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\AnnonceController;


########################
##Authentification
########################
Route::middleware(['redirectIfAuthenticated', 'prevent-back-history'])->group(function () {
    Route::get('/', function () { return view('auth.authentication_originale'); })->name('auth');
});

Route::group(['middleware' => 'prevent-back-history'],function(){
	Route::post('/login', [AuthController::class, 'login'])->name('login');

/////////////////////////////////////
//MIDDLEWARE AUTH
/////////////////////////////////////
Route::middleware(['auth', 'firstConn'])->group(function () {
    ########################
    ##Dashboard
    ########################
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::match(['get', 'post'], '/first_connection', [UserController::class, 'first_connection'])->name('first_connection');

    //Get Now date
    Route::get('/nowDate', [UserController::class, 'nowDate'])->name('nowDate');

    ########################
    ##Fonctionnalités
    ########################
    Route::match(['get', 'post'], '/parameters', [AuthController::class, 'parameters'])->name('parameters');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware('firstConn');

    ########################
    ##Ressources
    ########################
    Route::prefix('/resources')->name('resources.')->group(function () {
        Route::match(['get', 'post'], '/', [ResourceController::class, 'index'])->name('index');
        Route::get('/getResource/{id}', [ResourceController::class , 'getResource'])->name('getResource');
        Route::post('/download/{id}', [ResourceController::class, 'download'])->name('download');
    });

    ########################
    ##Forums
    ########################
    Route::prefix('/forums')->name('forums.')->group(function () {
        Route::match(['get', 'post'], '/', [ForumController::class, 'index'])->name('index');
        Route::get('/forum/{level_id}', [ForumController::class, 'forum'])->name('forum');
        Route::post('/addMsgForum/{level_id}', [ForumController::class, 'addMsgForum'])->name('addMsgForum');
    });

    ########################
    ##Users
    ########################
    Route::get('/getUserNotifs/{id}', [UserController::class , 'getUserNotifs'])->name('getUserNotifs');
    Route::get('/getNotifCreatedTime/{id}', [UserController::class , 'getNotifCreatedTime'])->name('getNotifCreatedTime');
    Route::get('/resetNotifs/{id}', [UserController::class , 'resetNotifs'])->name('resetNotifs');
    Route::post('/users/suppNotifs', [UserController::class , 'suppNotifs'])->name('users.suppNotifs');
    Route::post('/users/suppAnnonces', [UserController::class , 'suppAnnonces'])->name('users.suppAnnonces');

    ########################
    ##Annonces
    ########################
    Route::prefix('/annonces')->name('annonces.')->group(function () {
        Route::get('/', [AnnonceController::class, 'index'])->name('index');
        Route::get('/getAnnonces', [AnnonceController::class, 'getAnnonces'])->name('getAnnonces');
        Route::get('/getAnnonce/{id}', [AnnonceController::class, 'getAnnonce'])->name('getAnnonce');
        Route::get('/getAnnonceCreatedTime/{id}', [AnnonceController::class, 'getAnnonceCreatedTime'])->name('getAnnonceCreatedTime');
        Route::get('/getAnnonceRelation/{id}', [AnnonceController::class, 'getAnnonceRelation'])->name('getAnnonceRelation');
        Route::get('/resetAnnonces', [AnnonceController::class, 'resetAnnonces'])->name('resetAnnonces');
        Route::delete('/suppAnnonces', [AnnonceController::class, 'suppAnnonces'])->name('suppAnnonces');
        Route::delete('/deleteRelation/{id}', [AnnonceController::class, 'deleteRelation'])->name('deleteRelation');
    });


    
});

/////////////////////////////////////
//MIDDLEWARE ADMINISTRATEUR
/////////////////////////////////////
Route::middleware(['auth', 'admin'])->group(function () {
    ########################
    ##Users
    ########################
    Route::prefix('/users')->name('users.')->group(function () {
        Route::match(['get', 'post'], '/', [UserController::class, 'index'])->name('index');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::post('/edit', [UserController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('delete');
        Route::get('/getUser/{id}', [UserController::class , 'getUser'])->name('getUser');
        Route::post('/download', [UserController::class , 'download'])->name('download')->withoutMiddleware('prevent-back-history');
        Route::post('/importCSV', [UserController::class , 'importCSV'])->name('importCSV')->withoutMiddleware('prevent-back-history');
    });

    ########################
    ##Filières
    ########################
    Route::prefix('/sectors')->name('sectors.')->group(function () {
        Route::match(['get', 'post'], '/', [SectorController::class, 'index'])->name('index');
        Route::post('/store', [SectorController::class, 'store'])->name('store');
        Route::post('/edit', [SectorController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [SectorController::class, 'delete'])->name('delete');
        Route::get('/getSector/{id}', [SectorController::class , 'getSector'])->name('getSector');
    });

    ########################
    ##Modules
    ########################
    Route::prefix('/modules')->name('modules.')->group(function () {
        Route::match(['get', 'post'], '/', [ModuleController::class, 'index'])->name('index');
        Route::post('/store', [ModuleController::class, 'store'])->name('store');
        Route::post('/edit', [ModuleController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [ModuleController::class, 'delete'])->name('delete');
        Route::get('/getModule/{id}', [ModuleController::class , 'getModule'])->name('getModule');
        Route::get('/getProfFiliere/{id}', [ModuleController::class , 'getProfFiliere'])->name('getProfFiliere');
    });


    ########################
    ##Forums
    ########################
    Route::prefix('/forums')->name('forums.')->group(function () {
        Route::delete('/suppForum/{id}', [ForumController::class, 'suppForum'])->name('suppForum');
    });


});

/////////////////////////////////////
//MIDDLEWARE ADMIN ET PROF
/////////////////////////////////////
Route::middleware(['auth', 'adminEtProf'])->group(function() {
    ########################
    ##Ressources
    ########################
    Route::prefix('/resources')->name('resources.')->group(function () {
        Route::post('/edit', [ResourceController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [ResourceController::class, 'delete'])->name('delete');
    });

    ########################
    ##Annonces
    ########################
    Route::prefix('/annonces')->name('annonces.')->group(function () {
        Route::post('/store', [AnnonceController::class, 'store'])->name('store');
        Route::post('/edit', [AnnonceController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [AnnonceController::class, 'delete'])->name('delete');
    });

});

/////////////////////////////////////
//MIDDLEWARE PROF
/////////////////////////////////////
Route::middleware(['auth', 'prof'])->group(function() {
    Route::prefix('/resources')->name('resources.')->group(function () {
        Route::post('/store', [ResourceController::class, 'store'])->name('store');
        });
});


});



