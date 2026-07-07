<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Filament\Schemas\Components\Section; 
use Filament\Schemas\Components\Group;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
 
use App\Models\Category;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Fields')
                        ->description('Fill all the fields')
                        ->icon('heroicon-o-rocket-launch')
                        ->schema([  
                            TextInput::make('title')->required(),
                            TextInput::make('slug')->required(),
                            Select::make('category_id')
                                ->label('Category')
                                ->options(Category::all()->pluck('name', 'id'))
                                ->searchable(),
                            ColorPicker::make('color'),
                        ])
                        ->columns(2), 

                    MarkdownEditor::make('body')->required(),
                ])
                ->columnSpan(2),

                Group::make([
                    Section::make('Image Upload')
                        ->schema([
                            FileUpload::make('image')
                                ->disk('public')
                                ->directory('posts'),
                        ]),

                    Section::make('Meta')
                        ->schema([
                            TagsInput::make('tags'),
                            Checkbox::make('published'),
                            DatePicker::make('published_at'),
                        ]),
                ])
                ->columnSpan(1),
            ])
            ->columns(3); 
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}