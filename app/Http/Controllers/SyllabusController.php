<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Syllabus;
use App\Models\Classroom;
use Illuminate\Support\Facades\Storage;

class SyllabusController extends Controller
{
    /**
     * Store a newly created syllabus module
     */
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        try {
            // Store the PDF file
            $file = $request->file('pdf_file');
            $filePath = $file->store('syllabus', 'public');
            $fileName = $file->getClientOriginalName();

            // Create syllabus record
            Syllabus::create([
                'classroom_id' => $classroom->id,
                'title' => $request->title,
                'file_path' => $filePath,
                'file_name' => $fileName,
            ]);

            return redirect()->route('classroom.tab', [$classroom->id, 'silabus'])
                ->with('success', 'Modul silabus berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan modul silabus: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified syllabus PDF
     */
    public function view(Syllabus $syllabus)
    {
        try {
            $filePath = storage_path('app/public/' . $syllabus->file_path);
            
            if (!file_exists($filePath)) {
                abort(404, 'File tidak ditemukan');
            }

            return response()->file($filePath);

        } catch (\Exception $e) {
            abort(404, 'File tidak dapat dibuka');
        }
    }

    /**
     * Remove the specified syllabus
     */
    public function destroy(Syllabus $syllabus)
    {
        try {
            $classroomId = $syllabus->classroom_id;
            
            // Delete the PDF file from storage
            if (Storage::disk('public')->exists($syllabus->file_path)) {
                Storage::disk('public')->delete($syllabus->file_path);
            }

            // Delete the syllabus record
            $syllabus->delete();

            return redirect()->route('classroom.tab', [$classroomId, 'silabus'])
                ->with('success', 'Modul silabus berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus modul silabus: ' . $e->getMessage());
        }
    }
}