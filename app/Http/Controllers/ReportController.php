<?php


namespace App\Http\Controllers;

use App\Models\{Report, ReportScore, ReportTemplate, Classroom, Student};
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /* ====== LIST /FILTER ====== */
    public function index(Request $r)
    {
        $reports = Report::with(['student:id,name','template:id,title'])
            ->when($r->class_id,  fn($q)=>$q->where('class_id',$r->class_id))
            ->when($r->student_id,fn($q)=>$q->where('student_id',$r->student_id))
            ->latest()->paginate(20);

        return response()->json($reports);
    }

    /* ====== DETAIL RAPOR ====== */
    public function show($id)
    {
        $report = Report::with([
            'student:id,name',
            'template:id,title',
            'scores.item:id,kode,label,parent_id'
        ])->findOrFail($id);

        return response()->json($report);
    }

    /* ====== BUAT RAPOR ====== */
    public function store(Request $r)
    {
        $data = $r->validate([
            'class_id'    => 'required|exists:classrooms,id',
            'student_id'  => 'required|exists:students,id',
            'template_id' => 'required|exists:report_templates,id',
            'scores'      => 'required|array|min:1',
            'scores.*.item_id' => 'required|exists:report_template_items,id',
            'scores.*.value'   => 'nullable|in:BM,MM,BSH,BSB',
            'scores.*.note'    => 'nullable|string',
        ]);

        $template = ReportTemplate::find($data['template_id']);

        $report = Report::create([
            'class_id'    => $data['class_id'],
            'student_id'  => $data['student_id'],
            'template_id' => $template->id,
            'semester_id' => $template->semester_id,
            'issued_at'   => now(),
        ]);

        /* bulk insert */
        $rows = collect($data['scores'])->map(function($row) use ($report){
            return [
                'report_id'        => $report->id,
                'template_item_id' => $row['item_id'],
                'value'            => $row['value'],
                'note'             => $row['note'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        })->toArray();

        ReportScore::insert($rows);

        return response()->json(['success'=>true,'report_id'=>$report->id], 201);
    }

    /* ====== UPDATE NILAI/CATATAN ====== */
    public function update(Request $r, $id)
    {
        $report = Report::findOrFail($id);

        $data = $r->validate([
            'scores'      => 'required|array|min:1',
            'scores.*.item_id' => 'required|exists:report_template_items,id',
            'scores.*.value'   => 'nullable|in:BM,MM,BSH,BSB',
            'scores.*.note'    => 'nullable|string',
        ]);

        foreach ($data['scores'] as $row) {
            ReportScore::updateOrCreate(
                ['report_id'=>$report->id,'template_item_id'=>$row['item_id']],
                ['value'=>$row['value'],'note'=>$row['note']]
            );
        }

        return ['success'=>true];
    }

    /* ====== HAPUS RAPOR ====== */
    public function destroy($id)
    {
        Report::whereKey($id)->delete();
        return response()->json(['success'=>true]);
    }
}
