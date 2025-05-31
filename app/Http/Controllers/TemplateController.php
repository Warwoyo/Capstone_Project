<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use App\Models\TemplateTheme;
use App\Models\TemplateSubTheme;
use App\Models\TemplateAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemplateController extends Controller
{
    /**
     * Menampilkan daftar template
     */
    public function index()
    {
        try {
            $templates = ReportTemplate::with(['themes.subThemes'])
                ->active()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($templates);
        } catch (\Exception $e) {
            Log::error('Error loading templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat template'
            ], 500);
        }
    }

    /**
     * Menyimpan template baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_type' => 'required|in:ganjil,genap',
            'themes' => 'required|array|min:1',
            'themes.*.code' => 'required|string|max:10',
            'themes.*.name' => 'required|string|max:100',
            'themes.*.subThemes' => 'required|array|min:1',
            'themes.*.subThemes.*.code' => 'required|string|max:10',
            'themes.*.subThemes.*.name' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Buat template
            $template = ReportTemplate::create([
                'title' => $request->title,
                'description' => $request->description,
                'semester_type' => $request->semester_type,
                'is_active' => true,
            ]);

            // Buat tema dan sub-tema
            foreach ($request->themes as $themeIndex => $themeData) {
                $theme = TemplateTheme::create([
                    'template_id' => $template->id,
                    'code' => $themeData['code'],
                    'name' => $themeData['name'],
                    'order' => $themeIndex + 1,
                ]);

                foreach ($themeData['subThemes'] as $subThemeIndex => $subThemeData) {
                    TemplateSubTheme::create([
                        'theme_id' => $theme->id,
                        'code' => $subThemeData['code'],
                        'name' => $subThemeData['name'],
                        'order' => $subThemeIndex + 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dibuat',
                'data' => $template->load('themes.subThemes')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat template'
            ], 500);
        }
    }

    /**
     * Menampilkan detail template
     */
    public function show(ReportTemplate $template)
    {
        try {
            $template->load(['themes.subThemes', 'assignments.classroom']);
            
            return response()->json([
                'success' => true,
                'data' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail template'
            ], 500);
        }
    }

    /**
     * Update template
     */
    public function update(Request $request, ReportTemplate $template)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_type' => 'required|in:ganjil,genap',
            'themes' => 'required|array|min:1',
            'themes.*.code' => 'required|string|max:10',
            'themes.*.name' => 'required|string|max:100',
            'themes.*.subThemes' => 'required|array|min:1',
            'themes.*.subThemes.*.code' => 'required|string|max:10',
            'themes.*.subThemes.*.name' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Update template
            $template->update([
                'title' => $request->title,
                'description' => $request->description,
                'semester_type' => $request->semester_type,
            ]);

            // Hapus tema dan sub-tema lama
            $template->themes()->delete();

            // Buat tema dan sub-tema baru
            foreach ($request->themes as $themeIndex => $themeData) {
                $theme = TemplateTheme::create([
                    'template_id' => $template->id,
                    'code' => $themeData['code'],
                    'name' => $themeData['name'],
                    'order' => $themeIndex + 1,
                ]);

                foreach ($themeData['subThemes'] as $subThemeIndex => $subThemeData) {
                    TemplateSubTheme::create([
                        'theme_id' => $theme->id,
                        'code' => $subThemeData['code'],
                        'name' => $subThemeData['name'],
                        'order' => $subThemeIndex + 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil diperbarui',
                'data' => $template->load('themes.subThemes')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui template'
            ], 500);
        }
    }

    /**
     * Hapus template
     */
    public function destroy(ReportTemplate $template)
    {
        try {
            DB::beginTransaction();

            // Cek apakah template sedang digunakan
            if ($template->studentReports()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template tidak dapat dihapus karena sudah digunakan untuk laporan siswa'
                ], 422);
            }

            $template->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus template'
            ], 500);
        }
    }

    /**
     * Assign template ke kelas
     */
    public function assignToClass(Request $request, ReportTemplate $template)
    {
        $classId = $request->input('class_id');

        // clear previous assignment
        TemplateAssignment::where('classroom_id', $classId)
                          ->update(['is_current' => false]);

        // create new assignment
        $assignment = TemplateAssignment::create([
            'classroom_id' => $classId,
            'template_id'  => $template->id,
            'is_current'   => true,
        ]);

        return response()->json($assignment, 201);
    }
    public function getAssignedTemplate(int $classroomId)
    {
        $assignment = TemplateAssignment::where('classroom_id', $classroomId)
                        ->where('is_current', true)
                        ->first();

        if (! $assignment) {
            // no template assigned
            return response()->json(null, 204);
        }

        $template = ReportTemplate::with('themes.subThemes')
                    ->findOrFail($assignment->template_id);

        return response()->json($template);
    }
    public function removeAssignedTemplate($classroomId)
    {
        try {
            // Find and remove the current assignment (unassign template from class)
            $removed = TemplateAssignment::where('classroom_id', $classroomId)
                                        ->where('is_current', true)
                                        ->delete();

            if ($removed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil dilepas dari kelas'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada template yang ditetapkan untuk kelas ini'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error removing assigned template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melepas template dari kelas'
            ], 500);
        }
    }
}