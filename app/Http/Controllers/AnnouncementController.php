<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;


class AnnouncementController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'photo'        => 'nullable|image|max:2048',
            'date'         => 'nullable|date',
        ]);

        // ── upload file jika ada ───────────────────────────────
        if ($r->hasFile('photo')) {
            $data['image'] = $r->file('photo')->store('announcements', 'public');
        }

        // ganti key agar cocok dgn kolom migration
        $data['published_at'] = $data['date'] ?? now();
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

