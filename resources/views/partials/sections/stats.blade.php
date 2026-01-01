@php
    $stats = [
        ['icon' => 'file-box', 'value' => '1TB+', 'label' => 'Premium Assets', 'color' => 'from-cyan-500 to-cyan-400'],
        ['icon' => 'users', 'value' => '50K+', 'label' => 'Happy Designers', 'color' => 'from-violet-500 to-violet-400'],
        ['icon' => 'download', 'value' => '500K+', 'label' => 'Downloads', 'color' => 'from-cyan-400 to-violet-500'],
        ['icon' => 'sparkles', 'value' => '5.0', 'label' => 'Average Rating', 'color' => 'from-violet-400 to-cyan-500'],
    ];
@endphp

<section class="py-20 bg-card relative overflow-hidden">
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, hsl(var(--foreground)) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($stats as $index => $stat)
                <div class="text-center space-y-4 animate-slide-in-up" style="animation-delay: {{ $index * 0.15 }}s">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br {{ $stat['color'] }} flex items-center justify-center shadow-lg">
                        <i data-lucide="{{ $stat['icon'] }}" class="w-8 h-8 text-background"></i>
                    </div>
                    <div>
                        <p class="text-4xl md:text-5xl font-bold font-display gradient-text">
                            {{ $stat['value'] }}
                        </p>
                        <p class="text-muted-foreground mt-1">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

