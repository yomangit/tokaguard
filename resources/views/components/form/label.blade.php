<div>
    <label {{ $attributes->merge(['class' => 'text-xs']) }}>
        {{ $label }}
        @if ($required)
        <span class="text-red-500 font-bold">*</span>
        @endif
    </label>


</div>
