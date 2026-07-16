<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PostStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $query = $this->filteredQuery();
        $total = (clone $query)->count();
        $published = (clone $query)->where('published', true)->count();
        $drafts = $total - $published;

        $trend = $this->filteredQuery()
            ->latest('created_at')
            ->limit(7)
            ->pluck('id')
            ->reverse()
            ->values()
            ->map(fn (int $id, int $index): int => $index + 1)
            ->all();

        return [
            Stat::make('Total posts', $total)
                ->description('Posts created in the selected period')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart($trend),
            Stat::make('Published posts', $published)
                ->description($total ? round(($published / $total) * 100).'% of all posts' : 'No posts yet')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Draft posts', $drafts)
                ->description('Waiting to be published')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),
        ];
    }

    private function filteredQuery(): Builder
    {
        return Post::query()
            ->when($this->pageFilters['startDate'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($this->pageFilters['endDate'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date));
    }
}
