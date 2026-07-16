<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Step;
use Filament\Schemas\Components\Group;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Actions\Action;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Post Info')
                        ->description('Fill all the fields')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Group::make([
                                TextInput::make('title')
                                    ->required()
                                    ->rules(['required', 'min:3', 'max:10']),
                                    
                                TextInput::make('slug')
                                        ->required()
                                        ->unique(ignorable: fn ($record) => $record)
                                        ->validationMessages([
                                            'unique' => 'slug should be unique.',
                                        ]),
                            ])->columns(2),

                            Group::make([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable(),
                                    
                                ColorPicker::make('color'),
                            ])->columns(2),
                        ]),
                    Step::make('Content')
                        ->description('Write your post content')
                        ->schema([
                            MarkdownEditor::make('body')->required(),
                        ]),

                    Step::make('Media & Status')
                        ->description('Fill media and status')
                        ->schema([
                            FileUpload::make('image')
                                ->disk('public')
                                ->directory('posts'),
                                
                            Select::make('tags')
                                ->label('Tags')
                                ->relationship('tags', 'name')
                                ->multiple()
                                ->preload(),

                            Checkbox::make('published'),
                            DatePicker::make('published_at'),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable()
                ->submitAction(
                    Action::make('save')
                        ->label('Save Post')
                        ->color('primary')
                        ->submit('save')
                ),
            ]);
    }
}