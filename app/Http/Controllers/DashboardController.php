<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $AnnouncementController = new AnnouncementController();
        $announcementList = $AnnouncementController->fetchAnnouncementList();
        return view('Dashboard.index', compact('announcementList'));
    }
}
