<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(): View
    {
        $schedules = Schedule::with(['subThemes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('schedules.index', [
            'schedules' => $schedules,
            'mode' => 'view'
        ]);
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'sub_themes' => 'required|array',
        'sub_themes.*.title' => 'required|string|max:255',
        'sub_themes.*.start_date' => 'required|date',
        'sub_themes.*.end_date' => 'required|date|after_or_equal:sub_themes.*.start_date',
    ]);

    try {
        // Simpan jadwal utama
        $schedule = Schedule::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
        ]);

        // Simpan sub tema
        foreach ($validatedData['sub_themes'] as $subTheme) {
            $schedule->subThemes()->create($subTheme);
        }

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}