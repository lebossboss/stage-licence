<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationConfirmation;

class RegisterController extends Controller
{
    public function index()
    {
        return view('register.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'numero_telephone' => 'required|string|max:20',
            'mail' => 'required|email|max:255|unique:inscriptions',
            'username' => 'required|string|max:255|unique:inscriptions',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $inscription = Inscription::create([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'numero_telephone' => $request->numero_telephone,
                'mail' => $request->mail,
                'username' => $request->username,
                'mot_de_passe' => bcrypt($request->password),
            ]);
            Mail::to($request->mail)->send(new RegistrationConfirmation($inscription, $request->password));
            return redirect()->route('register.index')->with('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
        } catch (\Exception $e) {
            return redirect()->route('register.index')->with('error', 'Erreur lors de l\'inscription.');
        }
    }
}
