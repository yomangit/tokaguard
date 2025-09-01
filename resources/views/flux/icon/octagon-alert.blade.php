{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
'variant' => 'outline',
])

@php
if ($variant === 'solid') {
throw new \Exception('The "solid" variant is not supported in Lucide.');
}

$classes = Flux::classes('shrink-0')->add(
match ($variant) {
'outline' => '[:where(&)]:size-6',
'solid' => '[:where(&)]:size-6',
'mini' => '[:where(&)]:size-5',
'micro' => '[:where(&)]:size-4',
},
);

$strokeWidth = match ($variant) {
'outline' => 2,
'mini' => 2.25,
'micro' => 2.5,
};
@endphp

<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-octagon-alert-icon lucide-octagon-alert">
    <path d="M12 16h.01" />
    <path d="M12 8v4" />
    <path d="M15.312 2a2 2 0 0 1 1.414.586l4.688 4.688A2 2 0 0 1 22 8.688v6.624a2 2 0 0 1-.586 1.414l-4.688 4.688a2 2 0 0 1-1.414.586H8.688a2 2 0 0 1-1.414-.586l-4.688-4.688A2 2 0 0 1 2 15.312V8.688a2 2 0 0 1 .586-1.414l4.688-4.688A2 2 0 0 1 8.688 2z" /></svg>
