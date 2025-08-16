<?php

namespace App\Http\Controllers;

use App\Services\WhoisService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhoisController extends Controller
{
    private $whoisService;

    public function __construct(WhoisService $whoisService)
    {
        $this->whoisService = $whoisService;
    }

    /**
     * Affiche le formulaire de recherche WHOIS
     */
    public function index(): View
    {
        return view('whois.index');
    }

    /**
     * Effectue la recherche WHOIS et affiche les résultats
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|min:3',
        ]);

        try {
            $query = trim($request->input('domain'));

            $whoisData = $this->whoisService->getWhoisInfo($query);

            return view('whois.result', compact('whoisData'));
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erreur lors de la recherche WHOIS: ' . $e->getMessage()]);
        }
    }
}
