<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;

class TemplateController extends Controller
{
    public function index()
    {
        // sementara fetch semua. Sesuaikan eager-load semester
        return ReportTemplate::with('semester:id,name,year,timeline')->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'semester'       => 'required|in:Ganjil,Genap',
            'tema_kode'      => 'required|string|max:10',
            'tema'           => 'required|string|max:100',
            'sub_tema_kode'  => 'required|string|max:10',
            'sub_tema'       => 'required|string|max:100',
            'description'    => 'nullable|string',
        ]);

        $tpl = ReportTemplate::create($data);

        return response()->json($tpl, 201);
    }
    
}
