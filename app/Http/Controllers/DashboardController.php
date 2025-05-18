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

    public function indexParent()
    {
        $AnnouncementController = new AnnouncementController();
        $announcementList = $AnnouncementController->fetchAnnouncementList();
        return view('Orangtua.index', compact('announcementList'));
    }

    public function childrenParent()
    {
        return view('Orangtua.children');
    }

    public function observationParent(){
        $ObservationController = new ObservationController();
        $observationList = $ObservationController->fetchObservationList();
        return view('Orangtua.observation', compact('observationList'));
    }
    
    public function scheduleParent(){
         $scheduleController = new ScheduleController();
         $scheduleList = $scheduleController->fetchScheduleList();
        return view('Orangtua.schedule', compact('scheduleList'));
    }
    
     public function attendanceParent(){
        
        return view('Orangtua.attendance');
    }

   public function announcementParent()
    {
        $AnnouncementController = new AnnouncementController();
        $announcementList = $AnnouncementController->fetchAnnouncementList();
        return view('Orangtua.announcement', compact('announcementList'));
    }

}
