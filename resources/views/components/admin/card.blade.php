@props(['flush' => false])
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm']) }}>
    @isset($header)
        <div class="border-b border-slate-100 bg-slate-50/90 px-4 py-3 text-sm font-semibold text-slate-800 sm:px-5">
            {{ $header }}
        </div>
    @endisset
    <div @class(['p-4 sm:p-5' => ! $flush])>
        {{ $slot }}
    </div>
</div>
