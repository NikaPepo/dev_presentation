@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => '', 'error' => null])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-200 mb-1.5">
        {{ $label }}
        @if($required)<span class="text-rose-400">*</span>@endif
    </label>

    @if($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="5"
                  placeholder="{{ $placeholder }}"
                  @if($required) required @endif
                  {{ $attributes->merge(['class' => "w-full rounded-lg bg-slate-900/70 border ".($error ? 'border-rose-500/60' : 'border-white/10')." px-3 py-2 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition"]) }}>{{ $value }}</textarea>
    @elseif($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}"
                @if($required) required @endif
                {{ $attributes->merge(['class' => "w-full rounded-lg bg-slate-900/70 border ".($error ? 'border-rose-500/60' : 'border-white/10')." px-3 py-2 text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition"]) }}>
            {{ $slot }}
        </select>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
               value="{{ $value }}" placeholder="{{ $placeholder }}"
               @if($required) required @endif
               {{ $attributes->merge(['class' => "w-full rounded-lg bg-slate-900/70 border ".($error ? 'border-rose-500/60' : 'border-white/10')." px-3 py-2 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition"]) }}>
    @endif

    @if($error)
        <p class="mt-1.5 text-xs text-rose-400">{{ $error }}</p>
    @endif
</div>
