<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReportTemplate;
use App\Models\TemplateTheme;
use App\Models\TemplateSubTheme;

class ReportTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Template untuk Semester Ganjil
        $template1 = ReportTemplate::create([
            'title' => 'Template Rapor PAUD Semester Ganjil',
            'description' => 'Template penilaian untuk anak usia dini semester ganjil dengan fokus pada pengembangan aspek kognitif, sosial, dan motorik.',
            'semester_type' => 'ganjil',
            'is_active' => true,
        ]);

        // Tema untuk Template 1
        $theme1 = TemplateTheme::create([
            'template_id' => $template1->id,
            'code' => 'T01',
            'name' => 'Nilai Agama dan Moral',
            'order' => 1,
        ]);

        // Sub-tema untuk Tema 1
        TemplateSubTheme::create([
            'theme_id' => $theme1->id,
            'code' => 'ST01',
            'name' => 'Mengenal Tuhan melalui ciptaan-Nya',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme1->id,
            'code' => 'ST02',
            'name' => 'Mengucapkan doa sebelum dan sesudah melakukan kegiatan',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme1->id,
            'code' => 'ST03',
            'name' => 'Mengenal perilaku baik/sopan dan buruk',
            'order' => 3,
        ]);

        $theme2 = TemplateTheme::create([
            'template_id' => $template1->id,
            'code' => 'T02',
            'name' => 'Fisik Motorik',
            'order' => 2,
        ]);

        // Sub-tema untuk Tema 2
        TemplateSubTheme::create([
            'theme_id' => $theme2->id,
            'code' => 'ST01',
            'name' => 'Motorik Kasar',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2->id,
            'code' => 'ST02',
            'name' => 'Motorik Halus',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2->id,
            'code' => 'ST03',
            'name' => 'Kesehatan dan Perilaku Keselamatan',
            'order' => 3,
        ]);

        $theme3 = TemplateTheme::create([
            'template_id' => $template1->id,
            'code' => 'T03',
            'name' => 'Kognitif',
            'order' => 3,
        ]);

        // Sub-tema untuk Tema 3
        TemplateSubTheme::create([
            'theme_id' => $theme3->id,
            'code' => 'ST01',
            'name' => 'Belajar dan Pemecahan Masalah',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme3->id,
            'code' => 'ST02',
            'name' => 'Berfikir Logis',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme3->id,
            'code' => 'ST03',
            'name' => 'Berfikir Simbolik',
            'order' => 3,
        ]);

        $theme4 = TemplateTheme::create([
            'template_id' => $template1->id,
            'code' => 'T04',
            'name' => 'Bahasa',
            'order' => 4,
        ]);

        // Sub-tema untuk Tema 4
        TemplateSubTheme::create([
            'theme_id' => $theme4->id,
            'code' => 'ST01',
            'name' => 'Memahami Bahasa',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme4->id,
            'code' => 'ST02',
            'name' => 'Mengungkapkan Bahasa',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme4->id,
            'code' => 'ST03',
            'name' => 'Keaksaraan',
            'order' => 3,
        ]);

        $theme5 = TemplateTheme::create([
            'template_id' => $template1->id,
            'code' => 'T05',
            'name' => 'Sosial Emosional',
            'order' => 5,
        ]);

        // Sub-tema untuk Tema 5
        TemplateSubTheme::create([
            'theme_id' => $theme5->id,
            'code' => 'ST01',
            'name' => 'Kesadaran Diri',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme5->id,
            'code' => 'ST02',
            'name' => 'Rasa Tanggung Jawab untuk Diri dan Orang Lain',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme5->id,
            'code' => 'ST03',
            'name' => 'Perilaku Prososial',
            'order' => 3,
        ]);

        $theme6 = TemplateTheme::create([
            'template_id' => $template1->id,
            'code' => 'T06',
            'name' => 'Seni',
            'order' => 6,
        ]);

        // Sub-tema untuk Tema 6
        TemplateSubTheme::create([
            'theme_id' => $theme6->id,
            'code' => 'ST01',
            'name' => 'Mengekspresikan diri melalui gerakan',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme6->id,
            'code' => 'ST02',
            'name' => 'Mengekspresikan diri melalui karya seni',
            'order' => 2,
        ]);

        // Template untuk Semester Genap
        $template2 = ReportTemplate::create([
            'title' => 'Template Rapor PAUD Semester Genap',
            'description' => 'Template penilaian untuk anak usia dini semester genap dengan pengembangan lanjutan dari semester ganjil.',
            'semester_type' => 'genap',
            'is_active' => true,
        ]);

        // Tema untuk Template 2 (sama seperti template 1 tapi untuk semester genap)
        $theme2_1 = TemplateTheme::create([
            'template_id' => $template2->id,
            'code' => 'T01',
            'name' => 'Nilai Agama dan Moral',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2_1->id,
            'code' => 'ST01',
            'name' => 'Mengenal Tuhan melalui ciptaan-Nya',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2_1->id,
            'code' => 'ST02',
            'name' => 'Mengucapkan doa sebelum dan sesudah melakukan kegiatan',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2_1->id,
            'code' => 'ST03',
            'name' => 'Menunjukkan sikap toleran',
            'order' => 3,
        ]);

        $theme2_2 = TemplateTheme::create([
            'template_id' => $template2->id,
            'code' => 'T02',
            'name' => 'Fisik Motorik',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2_2->id,
            'code' => 'ST01',
            'name' => 'Motorik Kasar (lanjutan)',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2_2->id,
            'code' => 'ST02',
            'name' => 'Motorik Halus (lanjutan)',
            'order' => 2,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme2_2->id,
            'code' => 'ST03',
            'name' => 'Kesehatan dan Perilaku Keselamatan (lanjutan)',
            'order' => 3,
        ]);

        // Template sederhana untuk testing
        $template3 = ReportTemplate::create([
            'title' => 'Template Dasar Penilaian',
            'description' => 'Template dasar untuk testing sistem penilaian.',
            'semester_type' => 'ganjil',
            'is_active' => true,
        ]);

        $theme3_1 = TemplateTheme::create([
            'template_id' => $template3->id,
            'code' => 'T01',
            'name' => 'Perkembangan Dasar',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme3_1->id,
            'code' => 'ST01',
            'name' => 'Kemampuan Komunikasi',
            'order' => 1,
        ]);

        TemplateSubTheme::create([
            'theme_id' => $theme3_1->id,
            'code' => 'ST02',
            'name' => 'Kemampuan Bersosialisasi',
            'order' => 2,
        ]);
    }
}