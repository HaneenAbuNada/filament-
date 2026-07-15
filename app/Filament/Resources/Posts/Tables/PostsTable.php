<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Forms\Components\Checkbox;
use App\Models\Post;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true), 

                ImageColumn::make('image')
                    ->disk('public')
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(), 

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(), 

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(), 

                TextColumn::make('color'),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(), 

                TextColumn::make('tags')
                    ->label('Tags')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('published')
                    ->label('Published')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('Creation Date')
                    ->form([
                        DatePicker::make('created_at')
                            ->label('Select Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['created_at'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                        );
                    }),

                SelectFilter::make('category_id')
                    ->label('Select Category')
                    ->relationship('category', 'name')
                    ->preload(),
            ])
            ->actions([
                
                Action::make('status')
                    ->label('Status Change') 
                    ->icon('heroicon-o-academic-cap') 
                    ->form([
                        Checkbox::make('published') 
                            ->label('Published'),
                    ])
                    ->fillForm(fn (Post $record): array => [
                        'published' => $record->published,
                    ])
                    ->action(function (array $data, Post $record): void {
                        $record->update([
                            'published' => $data['published'],
                        ]);
                    }),

                ReplicateAction::make(),  
                DeleteAction::make(),   
                EditAction::make(),      
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('title', 'asc'); 
    }
}