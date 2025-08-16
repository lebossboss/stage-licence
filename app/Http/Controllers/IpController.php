<?php

namespace App\Http\Controllers;

use App\Models\{City, Ip};
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log; // Import Log facade

class IpController extends Controller
{
    public function index(Request $request)
    {
        // Public IP
        $userIp = $request->ip();
        $ipInfo = ['error' => 'Impossible de récupérer les informations IP publiques.']; // Default error message

        try {
            if (in_array($userIp, ['127.0.0.1', '::1'])) {
                $response = Http::get("http://ip-api.com/json/");
            } else {
                $response = Http::get("http://ip-api.com/json/{$userIp}");
            }
            $ipInfo = $response->json();

            if (isset($ipInfo['status']) && $ipInfo['status'] === 'fail') {
                $ipInfo['error'] = $ipInfo['message'] ?? 'Erreur lors de la récupération des informations IP.';
                Log::error("IP API call failed: " . ($ipInfo['message'] ?? 'Unknown error'));
            }

        } catch (ConnectionException $e) {
            Log::error("Connection error to ip-api.com: " . $e->getMessage());
            $ipInfo['error'] = 'Erreur de connexion au service IP (ip-api.com). Veuillez vérifier votre connexion internet ou les paramètres DNS.';
        } catch (\Exception $e) {
            Log::error("Unexpected error in IpController: " . $e->getMessage());
            $ipInfo['error'] = 'Une erreur inattendue est survenue lors de la récupération des informations IP.';
        }

        // Local IP (for demonstration purposes, as actual local IP detection is client-side)
        $localIp = $request->ip(); // This will often be 127.0.0.1 or ::1 in development

        // Render the local IP view to a string
        $localIpView = View::make('ip.local_ip_info', compact('localIp'))->render();

        return view('ip.index', compact('ipInfo', 'localIpView'));
    }
}
