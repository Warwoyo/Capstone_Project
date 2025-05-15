<?php
namespace App\Http\Controllers;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Alumni;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $AnnouncementController = new AnnouncementController();
        $announcementList = $AnnouncementController->fetchAnnouncementList();
        $user = auth()->user();
        $activeStudentCount = Student::count();
        $alumniCount = Alumni::count();

        switch ($user->role) {
            case 'admin':
            case 'teacher':
                return view('Dashboard.index', [
                    'activeStudentCount' => Student::count(),
                    'alumniCount' => Alumni::count(),
                    'announcementList' => Announcement::latest()->get(), // dari database
                ]);
            case 'parent':
                return view('Dashboard.parent', compact('user')); // dashboard khusus orang tua
            default:
                abort(403);
        }

        
        
    }
}
