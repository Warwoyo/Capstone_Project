@props([
  'label', 
  'value' => '', 
  'name' => '', 
  'editable' => false,
  'type' => 'text',
  'id' => null,
])

<div class="flex flex-col gap-1">
  <label class="text-sm font-semibold text-gray-700">{{ $label }}</label>

  @if ($editable)
    <input 
      id="{{ $id }}"  
      type="{{ $type }}" 
      name="{{ $name }}" 
      value="{{ old($name, $value) }}" 
      class="w-full text-sm text-gray-700 py-2 px-3 py-1.5 border border-sky-600 rounded-full focus:ring-2 focus:ring-sky-500"
    />
  @else
    <p class="text-sm text-gray-500 border border-sky-600 rounded-full px-3 py-1.5">{{ $value }}</p>
  @endif
</div>
