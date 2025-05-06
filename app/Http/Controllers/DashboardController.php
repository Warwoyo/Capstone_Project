<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $AnnouncementController = new AnnouncementController();
        $announcementList = $AnnouncementController->fetchAnnouncementList();
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
            case 'teacher':
                return view('Dashboard.index', compact('announcementList')); // tampilan dashboard umum untuk admin/guru
            case 'parent':
                return view('Dashboard.parent', compact('user')); // dashboard khusus orang tua
            default:
                abort(403);
        }

        
        
    }
}
