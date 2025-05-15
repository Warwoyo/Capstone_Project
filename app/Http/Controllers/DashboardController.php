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
