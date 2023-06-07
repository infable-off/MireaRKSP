
<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'profile_info'])->name('dashboard');

//Route::get('/movie/{type}', function () {
//    return view('type', ['element' => 'movieList']);
//});
//Route::view('/welcome', 'welcome');

//Route::patch('/movies/{type}', function (string $type) {
//    return Inertia::render('movieList');
//})->middleware(['auth']);


//Route::middleware('auth')->group(function () {
//    Route::patch('movies/{type}')->name('MovieList');
//});
//Route::middleware('auth')->group(function () {
//    Route::patch('movies/{type}')->name('{type}');
//});







Route::middleware(['auth', 'profile_info'])->group(function (){
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', ['can' => Auth::user()->can('admin')]);
    })->name('dashboard');


});

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin_panel', function (){
        return Inertia::render('Profile/AdminPanel');
    })->name('adminPanel');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/personal_information', function () {
        return Inertia::render('Profile/PersonalInfo');
    })->name('fillInfo');

    Route::post('/personal_information', [ProfileController::class, 'fillInfo'])->name('fillInfo');
});




require __DIR__.'/auth.php';
