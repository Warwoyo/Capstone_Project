@props([
    'kode',
    'kompetensi',
    'nilai' => ['BM' => false, 'MM' => false, 'BSH' => false, 'BSB' => false],
    'withCatatan' => false,
    'catatanText' => '',
    'catatanRowspan' => 1
])

<tr>
    <td class="border text-center px-2 py-1">{{ $kode }}</td>
    <td class="border px-2 py-1">{{ $kompetensi }}</td>

    @foreach (['BM', 'MM', 'BSH', 'BSB'] as $item)
        <td class="border text-center px-2 py-1">
            <input type="checkbox"
                   name="nilai[{{ $kode }}][{{ $item }}]"
                   value="1"
                   @checked($nilai[$item] ?? false)
                   class="w-4 h-4 text-sky-600 rounded focus:ring focus:ring-sky-300" />
        </td>
    @endforeach
    
    @if ($withCatatan)
        <td class="border align-top" rowspan="{{ $catatanRowspan }}">
            <textarea name="teacher_notes"
                      class="w-full h-[150px] resize-y p-1 text-xs border-none focus:ring-0 focus:outline-none"
                      placeholder="Tulis catatan...">{{ $catatanText }}</textarea>
        </td>
    @endif
</tr>

