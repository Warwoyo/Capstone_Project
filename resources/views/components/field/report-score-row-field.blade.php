@props([
    'kode',
    'kompetensi',
    'subitems' => [],
    'nilai' => ['BM' => false, 'MM' => false, 'BSH' => false, 'BSB' => false],
    'hideMainCheckboxes' => false,
])

<tr>
    <td class="border text-center align-top">{{ $kode }}</td>
    <td class="border align-top  pl-6 py-2">{{ $kompetensi }}</td>

    @foreach (['BM', 'MM', 'BSH', 'BSB'] as $key)
        <td class="border text-center">
            @unless($hideMainCheckboxes)
                <input type="checkbox"
                       name="nilai[{{ $kode }}][{{ $key }}]"
                       value="1"
                       @checked($nilai[$key] ?? false)
                       class="w-4 h-4 text-sky-600 rounded focus:ring focus:ring-sky-300" />
            @endunless
        </td>
    @endforeach
</tr>

@foreach($subitems as $sub)
<tr>
    <td class="border text-center py-2"></td>
    <td class="border pl-6 py-2">{{ $sub['label'] }}</td>
    @foreach (['BM', 'MM', 'BSH', 'BSB'] as $key)
        <td class="border text-center py-2">
            <input type="checkbox"
                   name="nilai[{{ $kode }}][subitems][{{ Str::slug($sub['label']) }}][{{ $key }}]"
                   value="1"
                   @checked($sub['nilai'][$key] ?? false)
                   class="w-4 h-4 text-sky-600 rounded focus:ring focus:ring-sky-300" />
        </td>
    @endforeach
</tr>
@endforeach

