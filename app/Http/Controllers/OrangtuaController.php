<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrangtuaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'orangtua') {
                return redirect()->route('dashboard')->with('error', 'Akses tidak diizinkan');
            }
            return $next($request);
        });
    }

    public function rapot()
    {
        try {
            // Ensure user is authenticated and has proper session
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }

            $user = auth()->user();
            
            // Verify user has orangtua role
            if ($user->role !== 'orangtua') {
                return redirect()->route('dashboard')->with('error', 'Akses tidak diizinkan');
            }

            // Get orangtua data with session preservation
            $orangtua = $user->orangtua;
            if (!$orangtua) {
                return redirect()->route('orangtua.index')->with('error', 'Data orangtua tidak ditemukan');
            }

            // Get anak data
            $anak = $orangtua->anak;
            if (!$anak) {
                return redirect()->route('orangtua.index')->with('error', 'Data anak tidak ditemukan');
            }

            // Get rapot data
            $rapotData = collect(); // Initialize empty collection for now
            
            // You can add rapot data fetching logic here
            
            return view('Orangtua.rapot', compact('anak', 'rapotData'));
            
        } catch (\Exception $e) {
            \Log::error('Error in orangtua rapot: ' . $e->getMessage());
            return redirect()->route('orangtua.index')->with('error', 'Terjadi kesalahan saat memuat data rapot');
        }
    }
}