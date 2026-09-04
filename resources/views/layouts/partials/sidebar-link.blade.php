@php
    $active = $active ?? false;
@endphp
<a href="{{ $href }}"
   class="group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ $active ? 'bg-hb-gold text-hb-blue-deeper font-semibold' : 'text-white/80 hover:bg-white/10' }}"
   :class="collapsed ? 'justify-center' : 'justify-start'">
    <i class="bi {{ $icon }} text-lg"></i>
    <span class="whitespace-nowrap" x-show="!collapsed">{{ $label }}</span>
</a>
