@php
    $byCategory = collect($developer['skills'] ?? [])->groupBy('category');
    $categoryLabels = [
        'backend' => 'Backend',
        'frontend' => 'Frontend',
        'devops' => 'DevOps',
        'ai' => 'AI / ML',
        'tools' => 'Tools',
        'other' => 'Other',
    ];
@endphp

<x-section-heading
    eyebrow="What I work with"
    title="Stack & skills"
    description="Tools I reach for daily. Levels are honest self-assessments." />

<div class="space-y-8">
    @foreach($byCategory as $category => $items)
        <div>
            <h3 class="text-sm font-semibold text-slate-300 mb-3">{{ $categoryLabels[$category] ?? ucfirst($category) }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($items as $skill)
                    <div class="group relative px-3 py-2 rounded-lg bg-white/5 border border-white/10 hover:border-indigo-500/40 transition">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-slate-100">{{ $skill['name'] }}</span>
                            <span class="text-xs text-slate-500">{{ $skill['level'] }}%</span>
                        </div>
                        <div class="mt-1.5 h-1 w-full bg-white/5 rounded overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-400 rounded"
                                 style="width: {{ $skill['level'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
