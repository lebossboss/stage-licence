<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhoisController;
use App\Http\Controllers\IpController;
use App\Http\Controllers\NmapController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\CityIpController;

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

// Route pour la page d'accueil
Route::get('/', function () {
    return view('home');
})->name('home');

// Routes pour le service WHOIS
Route::get('/whois', [WhoisController::class, 'index'])->name('whois.index');
Route::post('/whois/lookup', [WhoisController::class, 'lookup'])->name('whois.lookup');

// Route pour afficher l'IP
Route::get('/ip', [IpController::class, 'index'])->name('ip.index');

// Routes pour le scanner Nmap
Route::get('/nmap', [NmapController::class, 'index'])->name('nmap.index');
Route::post('/nmap/scan', [NmapController::class, 'scan'])->name('nmap.scan');

// Routes pour le détecteur de proxy
Route::get('/proxy-detector', [ProxyController::class, 'index'])->name('proxy.detector');
Route::post('/proxy-detector/scan', [ProxyController::class, 'scan'])->name('proxy.scan');

// Routes pour l'inscription
Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Routes pour la connexion
Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login');
Route::post('/login', [App\Http\Controllers\LoginController::class, 'store'])->name('login.submit');

// Route pour la déconnexion
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');

Route::prefix('city-ips')->group(function() {
    Route::get('/by-city', [CityIpController::class, 'showByCity'])->name('city-ips.by-city');
    Route::get('/by-ip', [CityIpController::class, 'showByIp'])->name('city-ips.by-ip');
    Route::get('/by-range', [CityIpController::class, 'showByRange'])->name('city-ips.by-range');
    Route::get('/by-contact', [CityIpController::class, 'showByContact'])->name('city-ips.by-contact');
    Route::get('/by-organization', [CityIpController::class, 'showByOrganization'])->name('city-ips.by-organization');
});
