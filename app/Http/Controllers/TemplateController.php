<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReportTemplate;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        try {
            Log::info('Loading templates from database');
            
            // Get all active templates with their themes and sub-themes
            $templates = DB::table('report_templates')
                ->where('is_active', true)
                ->orderBy('title')
                ->get();
            
            Log::info('Found templates: ' . $templates->count());
            
            // Transform the data to include themes and sub-themes if needed
            $templatesWithThemes = $templates->map(function ($template) {
                // Get themes for this template
                $themes = DB::table('template_themes')
                    ->where('template_id', $template->id)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($theme) {
                        // Get sub-themes for this theme
                        $subThemes = DB::table('template_sub_themes')
                            ->where('theme_id', $theme->id)
                            ->orderBy('order')
                            ->get();
                        
                        $theme->sub_themes = $subThemes;
                        $theme->subThemes = $subThemes; // Also add camelCase version for compatibility
                        
                        return $theme;
                    });
                
                $template->themes = $themes;
                
                Log::info("Processing template ID: {$template->id} - {$template->title}");
                
                return $template;
            });
            
            Log::info('Final templates data: ' . json_encode($templatesWithThemes));
            
            return response()->json($templatesWithThemes);
            
        } catch (\Exception $e) {
            Log::error('Error loading templates: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load templates',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'semester_type' => 'required|in:ganjil,genap',
                'themes' => 'required|array|min:1',
                'themes.*.code' => 'required|string|max:10',
                'themes.*.name' => 'required|string|max:255',
                'themes.*.subThemes' => 'required|array|min:1',
                'themes.*.subThemes.*.code' => 'required|string|max:10',
                'themes.*.subThemes.*.name' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // Create the template
            $templateId = DB::table('report_templates')->insertGetId([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'semester_type' => $validatedData['semester_type'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create themes and sub-themes
            foreach ($validatedData['themes'] as $themeIndex => $themeData) {
                $themeId = DB::table('template_themes')->insertGetId([
                    'template_id' => $templateId,
                    'code' => $themeData['code'],
                    'name' => $themeData['name'],
                    'order' => $themeIndex + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($themeData['subThemes'] as $subThemeIndex => $subThemeData) {
                    DB::table('template_sub_themes')->insert([
                        'theme_id' => $themeId,
                        'code' => $subThemeData['code'],
                        'name' => $subThemeData['name'],
                        'order' => $subThemeIndex + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            // Return the created template
            $newTemplate = DB::table('report_templates')->where('id', $templateId)->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dibuat',
                'data' => $newTemplate
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified template.
     */
    public function show($id)
    {
        try {
            $template = DB::table('report_templates')
                ->where('id', $id)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template tidak ditemukan'
                ], 404);
            }

            // Get themes and sub-themes
            $themes = DB::table('template_themes')
                ->where('template_id', $id)
                ->orderBy('order')
                ->get()
                ->map(function ($theme) {
                    $subThemes = DB::table('template_sub_themes')
                        ->where('theme_id', $theme->id)
                        ->orderBy('order')
                        ->get();
                    
                    $theme->sub_themes = $subThemes;
                    $theme->subThemes = $subThemes;
                    
                    return $theme;
                });

            $template->themes = $themes;

            return response()->json($template);

        } catch (\Exception $e) {
            Log::error('Error showing template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat template'
            ], 500);
        }
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, $id)
    {
        // Implementation for updating templates
        return response()->json(['message' => 'Update not implemented yet']);
    }

    /**
     * Remove the specified template.
     */
    public function destroy($id)
    {
        try {
            // Soft delete by setting is_active to false
            $updated = DB::table('report_templates')
                ->where('id', $id)
                ->update([
                    'is_active' => false,
                    'updated_at' => now()
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Template tidak ditemukan'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Error deleting template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus template'
            ], 500);
        }
    }

    /**
     * Get assigned templates for a classroom.
     */
    public function getAssignedTemplates($classroomId)
    {
        try {
            Log::info("Getting assigned templates for classroom: {$classroomId}");
            
            // Check if template_assignments table exists
            if (!DB::getSchemaBuilder()->hasTable('template_assignments')) {
                Log::info('template_assignments table does not exist, returning empty array');
                return response()->json([]);
            }
            
            $assignments = DB::table('template_assignments')
                ->where('classroom_id', $classroomId)
                ->pluck('template_id');
            
            Log::info("Found assignments: " . $assignments->count());
            
            if ($assignments->isEmpty()) {
                return response()->json([]);
            }
            
            Log::info("Template IDs: " . json_encode($assignments->toArray()));
            
            $templates = DB::table('report_templates')
                ->whereIn('id', $assignments)
                ->where('is_active', true)
                ->get();
            
            Log::info("Found templates: " . $templates->count());
            
            // Add themes and sub-themes to each template
            $templatesWithThemes = $templates->map(function ($template) {
                Log::info("Processing template ID: {$template->id} - {$template->title}");
                
                $themes = DB::table('template_themes')
                    ->where('template_id', $template->id)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($theme) {
                        Log::info("Processing theme ID: {$theme->id} - {$theme->name} (Template: {$theme->template_id})");
                        
                        $subThemes = DB::table('template_sub_themes')
                            ->where('theme_id', $theme->id)
                            ->orderBy('order')
                            ->get();
                        
                        foreach ($subThemes as $subTheme) {
                            Log::info("Processing sub-theme ID: {$subTheme->id} - {$subTheme->name} (Theme: {$subTheme->theme_id})");
                        }
                        
                        Log::info("Theme {$theme->id} has " . $subThemes->count() . " sub-themes");
                        
                        $theme->sub_themes = $subThemes;
                        $theme->subThemes = $subThemes;
                        
                        return $theme;
                    });
                
                $template->themes = $themes;
                
                return $template;
            });
            
            Log::info('Final templates data: ' . json_encode($templatesWithThemes));
            
            return response()->json($templatesWithThemes);
            
        } catch (\Exception $e) {
            Log::error('Error getting assigned templates: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Assign a template to a classroom.
     */
    public function assignToClass(Request $request, $templateId)
    {
        try {
            $request->validate([
                'class_id' => 'required|integer|exists:classrooms,id'
            ]);

            // Check if template_assignments table exists, create if not
            if (!DB::getSchemaBuilder()->hasTable('template_assignments')) {
                DB::statement('
                    CREATE TABLE template_assignments (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        classroom_id BIGINT UNSIGNED NOT NULL,
                        template_id BIGINT UNSIGNED NOT NULL,
                        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE KEY unique_assignment (classroom_id, template_id)
                    )
                ');
            }

            // Insert assignment
            DB::table('template_assignments')->updateOrInsert(
                [
                    'classroom_id' => $request->class_id,
                    'template_id' => $templateId
                ],
                [
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil ditetapkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error assigning template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove assigned template from classroom.
     */
    public function removeAssignedTemplate(Request $request, $classroomId, $templateId = null)
    {
        try {
            Log::info("Removing assigned template. Classroom: {$classroomId}, Template: {$templateId}");
            
            $query = DB::table('template_assignments')
                ->where('classroom_id', $classroomId);
            
            if ($templateId) {
                $query->where('template_id', $templateId);
            }
            
            $deleted = $query->delete();
            
            Log::info("Removed {$deleted} assignments");
            
            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dilepas dari kelas'
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing assigned template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melepas template dari kelas'
            ], 500);
        }
    }
}