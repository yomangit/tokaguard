<div>
    <label {{ $attributes->merge(['class' => 'block']) }}>
        {{ $label }}
        @if ($required)
        <span class="text-red-500 font-bold">*</span>
        @endif
    </label>


</div>
