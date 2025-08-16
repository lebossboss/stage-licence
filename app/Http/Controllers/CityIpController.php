<?php

namespace App\Http\Controllers;

use App\Models\{City, Ip};
use Illuminate\Http\Request;

class CityIpController extends Controller
{
    public function showByCity(Request $request)
    {
        $city = City::with('ips')->where('city', 'like', '%' . $request->validate(['city' => 'required|string'])['city'] . '%')->firstOrFail();
        return view('ip.city', compact('city'));
    }

    public function showByIp(Request $request)
    {
        $ip = Ip::with('city')->where('ip_address', $request->validate(['ip' => 'required|ip'])['ip'])->firstOrFail();
        return view('ip.ip', compact('ip'));
    }

    public function showByRange(Request $request)
    {
        $validated = $request->validate(['range' => 'required|string']);
        $cities = City::with('ips')->where('netrange', 'like', '%' . $validated['range'] . '%')->get();
        return view('ip.range', compact('cities'));
    }

    public function showByContact(Request $request)
    {
        $validated = $request->validate(['contact' => 'required|string']);
        $cities = City::with('ips')->where('admin_name', 'like', '%' . $validated['contact'] . '%')
            ->orWhere('admin_email', 'like', '%' . $validated['contact'] . '%')
            ->orWhere('tech_name', 'like', '%' . $validated['contact'] . '%')
            ->orWhere('tech_email', 'like', '%' . $validated['contact'] . '%')
            ->get();
        return view('ip.contact', compact('cities'));
    }

    public function showByOrganization(Request $request)
    {
        $validated = $request->validate(['organization' => 'required|string']);
        $cities = City::with('ips')->where('organization', 'like', '%' . $validated['organization'] . '%')->get();
        return view('ip.organization', compact('cities'));
    }
}
