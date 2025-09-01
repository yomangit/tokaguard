@php $iconTrailing = $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconVariant = $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@props([
'iconTrailing' => null,
'iconVariant' => 'mini',
'variant' => 'default',
'suffix' => null,
'accent' => true,
'value' => null,
'icon' => null,
'kbd' => null,
])

@php
if ($kbd) $suffix = $kbd;

$iconClasses = Flux::classes()
->add('me-2')
// When using the outline icon variant, we need to size it down to match the default icon sizes...
->add($iconVariant === 'outline' ? 'size-5' : null)
;

$trailingIconClasses = Flux::classes()
->add('ms-auto text-zinc-400 [[data-flux-menu-item-icon]:hover_&]:text-current')
// When using the outline icon variant, we need to size it down to match the default icon sizes...
->add($iconVariant === 'outline' ? 'size-5' : null)
;

$classes = Flux::classes()
->add('flex items-center px-2 py-1.5 gap-4 w-full focus:outline-hidden capitalize')
->add('rounded-sm')
->add('text-start text-xs font-medium')
->add('[&[disabled]]:opacity-50')
->add(match ($variant) {
'outline' => match ($accent) {
true => [
'data-current:text-(--color-primary-content) hover:data-current:text-(--color-base-content)',
'data-current:bg-white dark:data-current:bg-neutral/[30%] dark:data-current:border-neutral dark:data-current:border-b-4 data-current:border data-current:border-zinc-200 dark:data-current:border-transparent',
'hover:text-zinc-800 dark:hover:text-base-content hover:bg-zinc-800/5 ',
'border border-transparent',
],
false => [
'data-current:text-zinc-800 dark:data-current:text-zinc-800 dark: data-current:border-zinc-200',
'data-current:bg-white dark:data-current:bg-white/10 data-current:border data-current:border-zinc-200 dark:data-current:border-white/10 data-current:shadow-xs',
'hover:text-zinc-800 dark:hover:text-base-content',
],
},
default => match ($accent) {
true => [
'data-current:text-(--color-base-content) hover:data-current:text-(--color-base-content)',
'data-current:bg-zinc-800/[4%] dark:data-current:bg-neutral/[30%] dark:data-current:border-neutral dark:data-current:border-b-4',
'hover:text-zinc-800 dark:hover:text-accent-content hover:bg-accent dark:text-(--color-neutral)',
],
false => [
'data-current:text-zinc-800 dark:data-current:text-zinc-800',
'data-current:bg-zinc-800/[4%] dark:data-current:bg-white/10',
'hover:text-zinc-800 dark:hover:text-accent-content hover:bg-accent dark:hover:bg-white/10',
],
},
})
;

$suffixClasses = Flux::classes()
->add('ms-auto text-xs text-zinc-400')
;
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-menu-item :data-flux-menu-item-has-icon="!! $icon">
    <?php if (is_string($icon) && $icon !== ''): ?>
    <flux:icon :$icon :variant="$iconVariant" :class="$iconClasses" data-flux-menu-item-icon />
    <?php elseif ($icon): ?>
    {{ $icon }}
    <?php else: ?>
    <div class="w-7 hidden [[data-flux-menu]:has(>[data-flux-menu-item-has-icon])_&]:block"></div>
    <?php endif; ?>

    {{ $slot }}

    <?php if ($suffix): ?>
    <?php if (is_string($suffix)): ?>
    <div class="{{ $suffixClasses }}">
        {{ $suffix }}
    </div>
    <?php else: ?>
    {{ $suffix }}
    <?php endif; ?>
    <?php endif; ?>

    <?php if (is_string($iconTrailing) && $iconTrailing !== ''): ?>
    <flux:icon :icon="$iconTrailing" :variant="$iconVariant" :class="$trailingIconClasses" data-flux-menu-item-icon />
    <?php elseif ($iconTrailing): ?>
    {{ $iconTrailing }}
    <?php endif; ?>

    {{ $submenu ?? '' }}
</flux:button-or-link>
