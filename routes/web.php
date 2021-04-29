<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Applications\ApplyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySupervisorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\InternshipApplicationController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UniversitySupervisorController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
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

require __DIR__.'/auth.php';

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/contact', 'Contact')->name('contact');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('internships', InternshipController::class);
    Route::resource('companies', CompanyController::class);
    Route::resource('company_supervisors', CompanySupervisorController::class);
    Route::resource('students', StudentController::class);
    Route::resource('university_supervisors', UniversitySupervisorController::class);
    Route::resource('fields', FieldController::class);
    Route::resource('skills', SkillController::class);
    
    Route::post('/applications/{application}', [ApplicationController::class, 'reply'])->name('applications.reply');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/internships/{internship}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/internship/{internship}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}/edit', [ApplicationController::class, 'edit'])->name('applications.edit');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::put('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::get('/internships/{internship}/applications', InternshipApplicationController::class)->name('internships.applications.index');

    Route::get('/likes', [LikeController::class, 'index'])->name('likes.index');
    Route::post('/like/{internship}', [LikeController::class, 'store'])->name('likes.store');
});


Route::get('/test', function () {
    $company = auth()->user()->userable;
    \App\Models\Application::query()->whereHas('internship.company', function (Builder $query) use ($company) {
        $query->whereKey($company->getKey());
    })->get()->dd();
});