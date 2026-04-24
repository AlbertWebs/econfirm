@props(['title'])
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        <h2 class="text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">{{ $title }}</h2>
        @isset($description)
            <p class="mt-1 max-w-3xl text-sm text-slate-600">{{ $description }}</p>
        @endisset
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
