<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AnnouncementController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date'         => 'nullable|date',
        ]);

        // ── upload file jika ada dengan penamaan custom ───────────────────────────────
        if ($r->hasFile('photo')) {
            $image = $r->file('photo');
            $extension = $image->getClientOriginalExtension();
            
            // Generate filename: clean title + timestamp miliseconds
            $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '_', $data['title']);
            $cleanTitle = trim($cleanTitle, '_');
            $timestamp = round(microtime(true) * 1000); // Get milliseconds
            $filename = $cleanTitle . '_' . $timestamp . '.' . $extension;
            
            // Store image in storage/app/public/announcements
            $data['image'] = $image->storeAs('announcements', $filename, 'public');
        }

        // ganti key agar cocok dgn kolom migration
        $data['published_at'] = $data['date'] ?? now();
        $data['created_by'] = auth()->id();
        unset($data['photo'], $data['date']);

        Announcement::create($data);

        return back()->with('success', 'Pengumuman berhasil disimpan');
    }
    public function destroy(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }

        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

}

