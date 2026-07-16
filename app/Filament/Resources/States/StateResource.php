<?php

namespace App\Filament\Resources\States;

use App\Filament\Resources\States\Pages\CreateState;
use App\Filament\Resources\States\Pages\EditState;
use App\Filament\Resources\States\Pages\ListStates;
use App\Filament\Resources\States\Schemas\StateForm;
use App\Filament\Resources\States\Tables\StatesTable;
use App\Models\State;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) State::query()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total states';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'country.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('country');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Country' => $record->country?->name ?? '',
            'Cities' => (string) $record->cities()->count(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return StateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStates::route('/'),
            'create' => CreateState::route('/create'),
            'edit' => EditState::route('/{record}/edit'),
        ];
    }
}
