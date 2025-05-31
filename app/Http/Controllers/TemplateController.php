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
            Log::info("Showing template ID: " . $template->id);
            
            // Load with ordered relationships
            $template->load(['themes' => function($query) {
                $query->orderBy('order');
            }, 'themes.subThemes' => function($query) {
                $query->orderBy('order');
            }]);
            
            // Log the structure
            Log::info("Template " . $template->id . " has " . $template->themes->count() . " themes");
            foreach ($template->themes as $theme) {
                Log::info("Theme " . $theme->id . " (" . $theme->name . ") has " . $theme->subThemes->count() . " sub-themes");
                foreach ($theme->subThemes as $subTheme) {
                    Log::info("Sub-theme: " . $subTheme->id . " - " . $subTheme->name . " (belongs to theme " . $subTheme->theme_id . ")");
                }
            }
            
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
        try {
            $classId = $request->input('class_id');
            
            Log::info("Assigning template {$template->id} to class {$classId}");

            // Check if assignment already exists (remove is_current requirement)
            $existingAssignment = TemplateAssignment::where('classroom_id', $classId)
                                    ->where('template_id', $template->id)
                                    ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template sudah ditetapkan untuk kelas ini'
                ], 422);
            }

            // Create new assignment
            $assignment = TemplateAssignment::create([
                'classroom_id' => $classId,
                'template_id'  => $template->id,
                'is_current'   => false, // Set to false to match existing data
            ]);

            Log::info("Template assignment created: " . $assignment->id);

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil ditetapkan',
                'data' => $assignment
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error assigning template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan template'
            ], 500);
        }
    }
    /**
     * Get multiple assigned templates for a classroom
     */
    public function getAssignedTemplates($classroomId)
    {
        try {
            // Debug log
            Log::info("Getting assigned templates for classroom: " . $classroomId);
            
            // Remove is_current requirement since all records have is_current = 0
            $assignments = TemplateAssignment::where('classroom_id', $classroomId)
                            ->get();

            Log::info("Found assignments: " . $assignments->count());

            if ($assignments->isEmpty()) {
                return response()->json([], 200);
            }

            $templateIds = $assignments->pluck('template_id')->unique();
            Log::info("Template IDs: " . $templateIds->toJson());
            
            // Load templates with all relationships including sub_themes, ordered properly
            $templates = ReportTemplate::with(['themes' => function($query) {
                $query->orderBy('order');
            }, 'themes.subThemes' => function($query) {
                $query->orderBy('order');
            }])
            ->whereIn('id', $templateIds)
            ->where('is_active', true)
            ->get();

            Log::info("Found templates: " . $templates->count());
            
            // Transform to ensure proper structure with detailed logging
            $templatesData = $templates->map(function ($template) {
                Log::info("Processing template ID: " . $template->id . " - " . $template->title);
                
                $themesData = $template->themes->map(function ($theme) {
                    Log::info("Processing theme ID: " . $theme->id . " - " . $theme->name . " (Template: " . $theme->template_id . ")");
                    
                    $subThemesData = $theme->subThemes->map(function ($subTheme) use ($theme) {
                        Log::info("Processing sub-theme ID: " . $subTheme->id . " - " . $subTheme->name . " (Theme: " . $subTheme->theme_id . ")");
                        return [
                            'id' => $subTheme->id,
                            'code' => $subTheme->code,
                            'name' => $subTheme->name,
                            'theme_id' => $subTheme->theme_id,
                            'order' => $subTheme->order
                        ];
                    });
                    
                    Log::info("Theme " . $theme->id . " has " . $subThemesData->count() . " sub-themes");
                    
                    return [
                        'id' => $theme->id,
                        'code' => $theme->code,
                        'name' => $theme->name,
                        'template_id' => $theme->template_id,
                        'order' => $theme->order,
                        'sub_themes' => $subThemesData,
                        'subThemes' => $subThemesData // Keep both for compatibility
                    ];
                });
                
                return [
                    'id' => $template->id,
                    'title' => $template->title,
                    'description' => $template->description,
                    'semester_type' => $template->semester_type,
                    'is_active' => $template->is_active,
                    'themes' => $themesData
                ];
            });

            Log::info("Final templates data: " . $templatesData->toJson());

            return response()->json($templatesData);
        } catch (\Exception $e) {
            Log::error('Error loading assigned templates: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to load assigned templates',
                'message' => $e->getMessage()
            ], 500);
        }
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
    public function removeAssignedTemplate($classroomId, $templateId = null)
    {
        try {
            Log::info("Removing assigned template. Classroom: {$classroomId}, Template: {$templateId}");
            
            $query = TemplateAssignment::where('classroom_id', $classroomId);
            
            // If templateId is provided, only remove that specific template
            if ($templateId) {
                $query->where('template_id', $templateId);
            }
            
            $removed = $query->delete();
            Log::info("Removed {$removed} assignments");

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