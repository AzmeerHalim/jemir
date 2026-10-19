@props(['href' => '#', 'active' => false, 'icon' => 'circle', 'label' => ''])

<a href="{{ $href }}"
    @class([
        'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition',
        'bg-violet-50 text-violet-700 border-r-2 border-violet-600' => $active,
        'text-slate-600 hover:bg-violet-50 hover:text-violet-700' => !$active,
    ])>
    <i data-lucide="{{ $icon }}" class="w-5 h-5 shrink-0"></i>
    <span>{{ $label }}</span>
</a>
