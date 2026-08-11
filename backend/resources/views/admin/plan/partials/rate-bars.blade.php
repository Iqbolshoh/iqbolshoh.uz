{{-- A completion rate per row, shared by every segment breakdown.
     @param list<array{label: string, rate: float, total: int}> $rows --}}
<div class="space-y-3">
    @foreach($rows as $row)
    @php
        $tint = $row['total'] === 0
            ? '#8B95A5'
            : ($row['rate'] >= 80 ? '#22C55E' : ($row['rate'] >= 50 ? '#F59E0B' : '#EF4444'));
    @endphp
    <div>
        <div class="flex items-center justify-between text-xs mb-1.5">
            <span class="text-[var(--text-secondary)]">{{ $row['label'] }}</span>
            <span class="text-[var(--text-muted)]">
                {{ $row['total'] }} plans ·
                <span class="font-mono font-bold text-white">{{ $row['rate'] }}%</span>
            </span>
        </div>
        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
            <div class="h-full rounded-full" style="width: {{ $row['rate'] }}%; background: {{ $tint }};"></div>
        </div>
    </div>
    @endforeach
</div>
