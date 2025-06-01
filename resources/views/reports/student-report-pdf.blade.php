<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 11px;
            color: #6b7280;
        }

        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .student-info-row {
            display: table-row;
        }

        .student-info-cell {
            display: table-cell;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .student-info-cell:first-child {
            background-color: #f3f4f6;
            font-weight: bold;
            width: 25%;
        }

        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #374151;
        }

        .assessment-table th {
            background-color: #1e40af;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #374151;
        }

        .assessment-table td {
            border: 1px solid #374151;
            padding: 6px 8px;
            vertical-align: top;
        }

        .theme-header {
            background-color: #dbeafe;
            font-weight: bold;
            color: #1e40af;
        }

        .subtheme-row {
            background-color: #ffffff;
        }

        .subtheme-row:nth-child(even) {
            background-color: #f9fafb;
        }

        .score-cell {
            text-align: center;
            font-weight: bold;
        }

        .score-bm { color: #dc2626; }
        .score-mm { color: #d97706; }
        .score-bsh { color: #2563eb; }
        .score-bsb { color: #16a34a; }

        .competency-name {
            font-weight: 500;
        }

        .competency-desc {
            font-size: 10px;
            color: #6b7280;
            font-style: italic;
            margin-top: 2px;
        }

        .data-section {
            margin-bottom: 20px;
        }

        .data-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
        }

        .data-grid {
            display: table;
            width: 100%;
        }

        .data-row {
            display: table-row;
        }

        .data-cell {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }

        .info-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            width: 40%;
        }

        .comments-section {
            margin-top: 20px;
        }

        .comment-box {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px;
            min-height: 60px;
            background-color: #f9fafb;
            margin-bottom: 10px;
        }

        .comment-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }

        .legend {
            margin-top: 20px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px;
            background-color: #f8fafc;
        }

        .legend h4 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }

        .legend-grid {
            display: table;
            width: 100%;
        }

        .legend-row {
            display: table-row;
        }

        .legend-cell {
            display: table-cell;
            width: 25%;
            padding: 3px;
            text-align: center;
        }

        .legend-badge {
            display: inline-block;
            width: 30px;
            height: 20px;
            line-height: 20px;
            border-radius: 3px;
            font-weight: bold;
            color: white;
            margin-right: 5px;
            font-size: 10px;
        }

        .legend-bm { background-color: #dc2626; }
        .legend-mm { background-color: #d97706; }
        .legend-bsh { background-color: #2563eb; }
        .legend-bsb { background-color: #16a34a; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }

        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-row {
            display: table-row;
        }

        .signature-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }

        .signature-line {
            border-bottom: 1px solid #374151;
            width: 150px;
            margin: 40px auto 5px;
        }

        @page {
            margin: 20mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #1e40af;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak Rapor</button>

    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PERKEMBANGAN ANAK DIDIK</h1>
        <h2>PENDIDIKAN ANAK USIA DINI (PAUD)</h2>
        <p>{{ $classroom->name ?? 'PAUD KARTIKA' }}</p>
        <p>Semester: {{ ucfirst($template->semester_type ?? 'Ganjil') }} - Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="student-info-row">
            <div class="student-info-cell">Nama Lengkap</div>
            <div class="student-info-cell">: {{ $student->name }}</div>
            <div class="student-info-cell">Tempat, Tanggal Lahir</div>
            <div class="student-info-cell">: {{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d F Y') : '-' }}</div>
        </div>
        <div class="student-info-row">
            <div class="student-info-cell">NISN</div>
            <div class="student-info-cell">: {{ $student->nisn ?? '-' }}</div>
            <div class="student-info-cell">Jenis Kelamin</div>
            <div class="student-info-cell">: {{ $student->gender == 'L' ? 'Laki-laki' : ($student->gender == 'P' ? 'Perempuan' : '-') }}</div>
        </div>
        <div class="student-info-row">
            <div class="student-info-cell">Kelas</div>
            <div class="student-info-cell">: {{ $classroom->name }}</div>
            <div class="student-info-cell">Tanggal Rapor</div>
            <div class="student-info-cell">: {{ $current_date }}</div>
        </div>
    </div>

    <!-- Assessment Results -->
    <table class="assessment-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 45%;">KOMPETENSI DASAR</th>
                <th style="width: 12%;">PENILAIAN</th>
                <th style="width: 35%;">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($template->themes as $themeIndex => $theme)
                <!-- Theme Header -->
                <tr class="theme-header">
                    <td style="text-align: center;">{{ $themeIndex + 1 }}</td>
                    <td><strong>{{ $theme->name }}</strong></td>
                    <td style="text-align: center;"><strong>TEMA</strong></td>
                    <td>{{ $report->theme_comments[$theme->id] ?? '-' }}</td>
                </tr>
                
                <!-- Sub-themes -->
                @foreach($theme->sub_themes as $subIndex => $subTheme)
                    <tr class="subtheme-row">
                        <td style="text-align: center;">{{ $theme->code ?? 'T'.($themeIndex + 1) }}.{{ $subIndex + 1 }}</td>
                        <td>
                            <div class="competency-name">{{ $subTheme->name }}</div>
                            @if(!empty($subTheme->description))
                                <div class="competency-desc">{{ $subTheme->description }}</div>
                            @endif
                        </td>
                        <td class="score-cell">
                            @php
                                $score = $report->scores[$subTheme->id] ?? '-';
                                $scoreClass = match($score) {
                                    'BM' => 'score-bm',
                                    'MM' => 'score-mm', 
                                    'BSH' => 'score-bsh',
                                    'BSB' => 'score-bsb',
                                    default => ''
                                };
                            @endphp
                            <span class="{{ $scoreClass }}">{{ $score }}</span>
                        </td>
                        <td>{{ $report->sub_theme_comments[$subTheme->id] ?? '-' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <!-- Physical & Attendance Data -->
    <div class="data-section">
        <div class="data-grid">
            <div class="data-row">
                <div class="data-cell">
                    <h3>Data Fisik</h3>
                    <table class="info-table">
                        <tr>
                            <th>Lingkar Kepala</th>
                            <td>{{ $report->physical_data['head_circumference'] ?? '-' }} cm</td>
                        </tr>
                        <tr>
                            <th>Tinggi Badan</th>
                            <td>{{ $report->physical_data['height'] ?? '-' }} cm</td>
                        </tr>
                        <tr>
                            <th>Berat Badan</th>
                            <td>{{ $report->physical_data['weight'] ?? '-' }} kg</td>
                        </tr>
                    </table>
                </div>
                <div class="data-cell">
                    <h3>Data Kehadiran</h3>
                    <table class="info-table">
                        <tr>
                            <th>Sakit</th>
                            <td>{{ $report->attendance_data['sick'] ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <th>Izin</th>
                            <td>{{ $report->attendance_data['permission'] ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <th>Alpa</th>
                            <td>{{ $report->attendance_data['absent'] ?? 0 }} hari</td>
                        </tr>
                        <tr style="background-color: #dcfce7;">
                            <th>Hadir</th>
                            <td><strong>{{ $report->attendance_data['present'] ?? 0 }} hari</strong></td>
                        </tr>
                        <tr style="background-color: #dbeafe;">
                            <th>Total Sesi</th>
                            <td><strong>{{ $report->attendance_data['total_sessions'] ?? 0 }} hari</strong></td>
                        </tr>
                        @php
                            $attendancePercentage = ($report->attendance_data['total_sessions'] ?? 0) > 0 
                                ? round(($report->attendance_data['present'] ?? 0) / $report->attendance_data['total_sessions'] * 100) 
                                : 0;
                        @endphp
                        <tr style="background-color: #fef3c7;">
                            <th>Persentase Kehadiran</th>
                            <td><strong>{{ $attendancePercentage }}%</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section">
        <div class="data-grid">
            <div class="data-row">
                <div class="data-cell">
                    <div class="comment-title">Pesan Guru:</div>
                    <div class="comment-box">
                        {{ $report->teacher_comment ?: 'Tidak ada pesan dari guru.' }}
                    </div>
                </div>
                <div class="data-cell">
                    <div class="comment-title">Pesan Orang Tua:</div>
                    <div class="comment-box">
                        {{ $report->parent_comment ?: 'Tidak ada pesan dari orang tua.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Legend -->
    <div class="legend">
        <h4>Keterangan Penilaian:</h4>
        <div class="legend-grid">
            <div class="legend-row">
                <div class="legend-cell">
                    <span class="legend-badge legend-bm">BM</span>
                    <small>Belum Muncul</small>
                </div>
                <div class="legend-cell">
                    <span class="legend-badge legend-mm">MM</span>
                    <small>Mulai Muncul</small>
                </div>
                <div class="legend-cell">
                    <span class="legend-badge legend-bsh">BSH</span>
                    <small>Berkembang Sesuai Harapan</small>
                </div>
                <div class="legend-cell">
                    <span class="legend-badge legend-bsb">BSB</span>
                    <small>Berkembang Sangat Baik</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-cell">
                <p>Mengetahui,</p>
                <p><strong>Orang Tua/Wali</strong></p>
                <div class="signature-line"></div>
                <p>{{ $student->parent_name ?? '(...............................)' }}</p>
            </div>
            <div class="signature-cell">
                <p>{{ $classroom->location ?? 'Balikpapan' }}, {{ $current_date }}</p>
                <p><strong>Guru Kelas</strong></p>
                <div class="signature-line"></div>
                <p>{{ $classroom->owner->name ?? '(...............................)' }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dicetak pada {{ $current_date }} - {{ $classroom->name ?? 'PAUD KARTIKA' }}</p>
    </div>
</body>
</html>