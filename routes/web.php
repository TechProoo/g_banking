<?php

use App\Http\Controllers\Admin\ClearCacheController;
use Illuminate\Support\Facades\Route;
use App\Models\Settings;
use Laravel\Fortify\Http\Controllers\NewPasswordController;

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

require __DIR__ . '/home.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
require __DIR__ . '/botman.php';

//activate and deactivate Online Trader
Route::any('/activate', function () {
	return view('activate.index', [
		'settings' => safe_settings(),
	]);
});

Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
});

Route::get('register-license', [ClearCacheController::class, 'saveLicense']);

Route::any('/revoke', function () {
	return view('revoke.index');
});

Route::post('/reset-password', [NewPasswordController::class, 'store'])
	->middleware(['guest:' . config('fortify.guard')])
	->name('password.update');

// Debug session test - remove after testing
Route::get('/session-test', function () {
    session(['test_key' => 'it_works_' . time()]);
    return response()->json([
        'session_driver' => config('session.driver'),
        'session_id' => session()->getId(),
        'session_secure' => config('session.secure'),
        'session_same_site' => config('session.same_site'),
        'session_domain' => config('session.domain'),
        'app_env' => config('app.env'),
        'is_https' => request()->isSecure(),
        'url_scheme' => request()->getScheme(),
        'test_value' => session('test_key'),
        'cookie_name' => config('session.cookie'),
    ]);
});
Route::get('/session-check', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'test_value' => session('test_key'),
        'has_session' => session('test_key') ? 'YES' : 'NO',
        'auth_check' => auth()->check() ? 'logged_in' : 'not_logged_in',
    ]);
});