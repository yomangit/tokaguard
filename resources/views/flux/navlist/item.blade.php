@php $iconTrailing = $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconVariant = $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@aware([ 'variant' ])

@props([
'iconVariant' => 'outline',
'iconTrailing' => null,
'badgeColor' => null,
'variant' => null,
'iconDot' => null,
'accent' => true,
'badge' => null,
'icon' => null,
])
@php
// Button should be a square if it has no text contents...
$square ??= $slot->isEmpty();

// Size-up icons in square/icon-only buttons...
$iconClasses = Flux::classes($square ? 'size-5!' : 'size-4!');

$classes = Flux::classes()
->add('h-10 lg:h-8  relative flex items-center gap-3 outline-none rounded-sm')
->add($square ? 'px-2.5!' : '')
->add('py-0 text-start w-full px-3 my-px')
->add('text-zinc-500 dark:text-base-content/80')
->add(match ($variant) {
'outline' => match ($accent) {
true => [
'data-current:text-(--color-neutral-content) hover:data-current:text-(--color-base-content)',
'data-current:bg-white dark:data-current:bg-neutral data-current:border data-current:border-zinc-200 dark:data-current:border-transparent',
'hover:text-zinc-800 dark:hover:text-(--color-neutral-content) hover:bg-accent ',
'border border-transparent',
],
false => [
'data-current:text-zinc-800 dark:data-current:text-zinc-100 data-current:border-zinc-200 ',
'data-current:bg-white dark:data-current:bg-white/10 data-current:border data-current:border-zinc-200 dark:data-current:border-white/10 data-current:shadow-xs',
'hover:text-zinc-800 dark:hover:text-(--color-neutral-content) hover:bg-accent',
],
},
default => match ($accent) {
true => [
'data-current:text-(--color-neutral-content) hover:data-current:text-(--color-neutral-content)',
'data-current:bg-zinc-800/[4%] dark:data-current:bg-neutral ',
'hover:text-zinc-800 dark:hover:text-(--color-neutral-content) hover:bg-accent ',
],
false => [
'data-current:text-zinc-800 dark:data-current:text-zinc-100',
'data-current:bg-zinc-800/[4%] dark:data-current:bg-white/10',
'hover:text-zinc-800 dark:hover:text-(--color-neutral-content) hover:bg-accent dark:hover:bg-accent',
],
},
})
;
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-navlist-item>
    <?php if ($icon): ?>
    <div class="relative">
        <?php if (is_string($icon) && $icon !== ''): ?>
        <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
        <?php else: ?>
        {{ $icon }}
        <?php endif; ?>

        <?php if ($iconDot): ?>
        <div class="absolute top-[-2px] end-[-2px]">
            <div class="size-[6px] rounded-sm bg-zinc-500 dark:bg-zinc-400"></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($slot->isNotEmpty()): ?>
    <div class="flex-1 text-xs font-medium leading-none whitespace-nowrap [[data-nav-footer]_&]:hidden [[data-nav-sidebar]_[data-nav-footer]_&]:block" data-content>{{ $slot }}</div>
    <?php endif; ?>

    <?php if (is_string($iconTrailing) && $iconTrailing !== ''): ?>
    <flux:icon :icon="$iconTrailing" :variant="$iconVariant" class="size-4!" />
    <?php elseif ($iconTrailing): ?>
    {{ $iconTrailing }}
    <?php endif; ?>

    <?php if ($badge): ?>
    <flux:navlist.badge :color="$badgeColor">{{ $badge }}</flux:navlist.badge>
    <?php endif; ?>
</flux:button-or-link>
