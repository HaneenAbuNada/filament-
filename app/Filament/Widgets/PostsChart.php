<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PostsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Posts created per month';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->subMonths(11)->startOfMonth())->startOfMonth();
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now())->endOfMonth();

        $months = collect();
        for ($month = $start->copy(); $month->lte($end); $month->addMonth()) {
            $months->put($month->format('Y-m'), 0);
        }

        Post::query()
            ->whereBetween('created_at', [$start, $end])
            ->pluck('created_at')
            ->each(function ($createdAt) use ($months): void {
                $key = Carbon::parse($createdAt)->format('Y-m');
                $months->put($key, $months->get($key, 0) + 1);
            });

        return [
            'datasets' => [[
                'label' => 'Posts',
                'data' => $months->values()->all(),
                'borderColor' => '#f59e0b',
                'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                'fill' => true,
                'tension' => 0.35,
            ]],
            'labels' => $months->keys()->map(fn (string $month) => Carbon::createFromFormat('Y-m', $month)->format('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
