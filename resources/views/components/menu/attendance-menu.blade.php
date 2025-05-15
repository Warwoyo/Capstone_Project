@php
$students = [
    ['name' => 'Anita Silalahi', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Chyntia Tamba', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Calista Bing', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Farida Nur', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Ilana Tan', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Justin Bieber', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Komang', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Justin Timberlake', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Putu Karan', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Rhode Billy', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    // Tambahan 10 murid baru
    ['name' => 'Sari Dewi', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Budi Santoso', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Lina Marlina', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Ahmad Fadli', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Citra Ayu', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Dewi Lestari', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Yuda Wijaya', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Rani Putri', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Fajar Nugraha', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
    ['name' => 'Hana Zahra', 'total' => 4, 'percentage' => '80%', 'totalPresent' => 4],
];
@endphp

<x-card.attendance-card :students="$students" />
