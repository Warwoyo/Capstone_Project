<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport {{ $student->name }} - {{ $template->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
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
            background: white;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c5aa0;
        }
        
        .header h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .student-info div {
            flex: 1;
        }
        
        .student-info h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c5aa0;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .info-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        
        .scores-section {
            margin-bottom: 30px;
        }
        
        .theme-header {
            background: #2c5aa0;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .scores-table th,
        .scores-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .scores-table th {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        
        .scores-table td {
            font-size: 11px;
        }
        
        .score-value {
            text-align: center;
            font-weight: bold;
            padding: 5px;
            border-radius: 3px;
        }
        
        .score-BM { background: #ffeaa7; color: #d63031; }
        .score-MM { background: #fab1a0; color: #e17055; }
        .score-BSH { background: #81ecec; color: #00b894; }
        .score-BSB { background: #55a3ff; color: #0984e3; }
        
        .comments-section {
            margin-bottom: 30px;
        }
        
        .comment-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .comment-title {
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .physical-data {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .physical-item {
            flex: 1;
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 0 5px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .attendance-summary {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .attendance-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .attendance-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            border: 1px solid #ddd;
            padding: 20px;
            width: 200px;
            height: 100px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2c5aa0;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #1e3a75;
        }
        
        @media print {
            .print-button {
                display: none;
            }
            
            body {
                font-size: 11px;
            }
            
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Cetak PDF</button>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>RAPORT PAUD</h1>
            <h2>{{ $template->title }}</h2>
            <p>Semester {{ ucfirst($template->semester_type) }} - Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div>
                <h3>Data Siswa</h3>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span>{{ $student->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NISN:</span>
                    <span>{{ $student->nisn ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Lahir:</span>
                    <span>{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d F Y') : '-' }}</span>
                </div>
            </div>
            <div>
                <h3>Data Kelas</h3>
                <div class="info-row">
                    <span class="info-label">Kelas:</span>
                    <span>{{ $classroom->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Cetak:</span>
                    <span>{{ $current_date }}</span>
                </div>
            </div>
        </div>

        <!-- Physical Data -->
        @if(!empty($report->physical_data))
        <div class="physical-data">
            <div class="physical-item">
                <strong>Tinggi Badan</strong><br>
                {{ $report->physical_data['height'] ?? '-' }} cm
            </div>
            <div class="physical-item">
                <strong>Berat Badan</strong><br>
                {{ $report->physical_data['weight'] ?? '-' }} kg
            </div>
            <div class="physical-item">
                <strong>Lingkar Kepala</strong><br>
                {{ $report->physical_data['head_circumference'] ?? '-' }} cm
            </div>
        </div>
        @endif

        <!-- Attendance Summary -->
        @if(!empty($report->attendance_data))
        <div class="attendance-summary">
            <h3 style="margin-bottom: 10px; color: #2c5aa0;">Ringkasan Kehadiran</h3>
            <div class="attendance-grid">
                <div class="attendance-item">
                    <strong>Hadir</strong><br>
                    {{ $report->attendance_data['present'] ?? 0 }} hari
                </div>
                <div class="attendance-item">
                    <strong>Sakit</strong><br>
                    {{ $report->attendance_data['sick'] ?? 0 }} hari
                </div>
                <div class="attendance-item">
                    <strong>Izin</strong><br>
                    {{ $report->attendance_data['permission'] ?? 0 }} hari
                </div>
                <div class="attendance-item">
                    <strong>Alpha</strong><br>
                    {{ $report->attendance_data['absent'] ?? 0 }} hari
                </div>
            </div>
        </div>
        @endif

        <!-- Scores Section -->
        <div class="scores-section">
            <h3 style="color: #2c5aa0; margin-bottom: 20px; font-size: 16px;">Penilaian Perkembangan</h3>
            
            @if(isset($template->themes) && count($template->themes) > 0)
                @foreach($template->themes as $theme)
                    <div class="theme-header">
                        {{ $theme->code }} - {{ $theme->name }}
                    </div>
                    
                    @if(isset($theme->sub_themes) && count($theme->sub_themes) > 0)
                        <table class="scores-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="60%">Indikator Perkembangan</th>
                                    <th width="15%">Penilaian</th>
                                    <th width="20%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($theme->sub_themes as $index => $subTheme)
                                    @php
                                        $score = $report->scores[$subTheme->id] ?? null;
                                        $comment = $report->sub_theme_comments[$subTheme->id] ?? '';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $subTheme->name }}</td>
                                        <td class="score-value score-{{ $score }}">
                                            {{ $score ?? '-' }}
                                        </td>
                                        <td>{{ $comment }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    
                    @if(isset($report->theme_comments[$theme->id]))
                        <div class="comment-box">
                            <div class="comment-title">Catatan {{ $theme->name }}:</div>
                            <p>{{ $report->theme_comments[$theme->id] }}</p>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Teacher and Parent Comments -->
        <div class="comments-section">
            @if(!empty($report->teacher_comment))
            <div class="comment-box">
                <div class="comment-title">Catatan Guru:</div>
                <p>{{ $report->teacher_comment }}</p>
            </div>
            @endif

            @if(!empty($report->parent_comment))
            <div class="comment-box">
                <div class="comment-title">Catatan Orang Tua:</div>
                <p>{{ $report->parent_comment }}</p>
            </div>
            @endif
        </div>

        <!-- Legend -->
        <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h4 style="margin-bottom: 10px; color: #2c5aa0;">Keterangan Penilaian:</h4>
            <div style="display: flex; gap: 20px;">
                <span><strong>BM:</strong> Belum Muncul</span>
                <span><strong>MM:</strong> Mulai Muncul</span>
                <span><strong>BSH:</strong> Berkembang Sesuai Harapan</span>
                <span><strong>BSB:</strong> Berkembang Sangat Baik</span>
            </div>
        </div>

        <!-- Footer with Signatures -->
        <div class="footer">
            <div class="signature-box">
                <p><strong>Orang Tua/Wali</strong></p>
                <br><br><br>
                <p>(.............................)</p>
            </div>
            <div class="signature-box">
                <p><strong>Guru Kelas</strong></p>
                <br><br><br>
                <p>(.............................)</p>
            </div>
            <div class="signature-box">
                <p><strong>Kepala Sekolah</strong></p>
                <br><br><br>
                <p>(.............................)</p>
            </div>
        </div>
    </div>
</body>
</html>