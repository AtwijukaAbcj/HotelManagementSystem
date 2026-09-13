@props(['label', 'value', 'hint' => null, 'tone' => 'teal'])
<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex items-start justify-between gap-3"><div><div class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $label }}</div><div class="mt-2 text-2xl font-bold text-slate-900">{{ $value }}</div>@if($hint)<div class="mt-1 text-xs text-slate-500">{{ $hint }}</div>@endif</div><span class="h-2.5 w-2.5 rounded-full {{ ['teal'=>'bg-teal-500','red'=>'bg-red-500','amber'=>'bg-amber-500','blue'=>'bg-blue-500'][$tone] ?? 'bg-slate-400' }}"></span></div>
</div>
