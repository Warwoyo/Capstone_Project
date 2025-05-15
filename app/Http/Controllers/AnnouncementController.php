<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function fetchAnnouncementList()
    {
        return Announcement::orderBy('created_at', 'desc')->take(6)->get();
    }
}
