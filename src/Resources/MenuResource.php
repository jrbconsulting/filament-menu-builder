<?php

namespace Vendor\FilamentMenuBuilder\Resources;

use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentMenuBuilder\Models\Menu;
use Vendor\FilamentMenuBuilder\Resources\MenuResource\Pages;
use Filament\Forms\Components\{TextInput, Select, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{TextColumn, ToggleColumn};
use Filament\Tables\Table;
use Filament\Actions\{ViewAction, EditAction, DeleteAction, BulkActionGroup, DeleteBulkAction, Action};

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-bars-3';
    }

    public static function getNavigationLabel(): string
    {
        return 'Menus';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Menu Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->rules(['string', 'max:255', 'regex:/^[\p{L}\p{N}\s\-_.!@#%]+$/u']),
                    TextInput::make('url')
                        ->required()
                        ->placeholder('/about or https://external.com')
                        ->maxLength(500)
                        ->rules(['string', 'max:500']),
                    TextInput::make('route_name')
                        ->placeholder('route.name for internal links')
                        ->maxLength(255)
                        ->rules(['nullable', 'string', 'max:255', 'regex:/^[a-z0-9._-]+$/']),
                    Select::make('parent_id')
                        ->label('Parent Menu')
                        ->options(function (?Model $record) {
                            return Menu::ordered()
                                ->where('id', '!=', $record?->id)
                                ->get()
                                ->mapWithKeys(function ($menu) {
                                    $depth = $menu->calculateDepth();
                                    $indent = str_repeat('— ', $depth);
                                    return [$menu->id => $indent . $menu->name];
                                });
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('No parent (top level)'),
                    Select::make('target')
                        ->options([
                            '_self' => 'Same window',
                            '_blank' => 'New tab',
                        ])
                        ->default('_self'),
                    Select::make('icon')
                        ->placeholder('Select an icon')
                        ->searchable()
                        ->options([
                            'heroicon-o-home' => '🏠 Home',
                            'heroicon-o-bars-3' => '☰ Menu',
                            'heroicon-o-arrow-right' => '→ Arrow Right',
                            'heroicon-o-arrow-left' => '← Arrow Left',
                            'heroicon-o-arrow-up' => '↑ Arrow Up',
                            'heroicon-o-arrow-down' => '↓ Arrow Down',
                            'heroicon-o-chevron-right' => '› Chevron Right',
                            'heroicon-o-chevron-left' => '‹ Chevron Left',
                            'heroicon-o-chevron-up' => 'ˆ Chevron Up',
                            'heroicon-o-chevron-down' => 'ˇ Chevron Down',
                            'heroicon-o-document-text' => '📄 Document',
                            'heroicon-o-document' => '📄 File',
                            'heroicon-o-folder' => '📁 Folder',
                            'heroicon-o-photo' => '🖼️ Photo',
                            'heroicon-o-video-camera' => '📹 Video',
                            'heroicon-o-book-open' => '📖 Book',
                            'heroicon-o-newspaper' => '📰 News',
                            'heroicon-o-envelope' => '✉️ Email',
                            'heroicon-o-chat-bubble-left-right' => '💬 Chat',
                            'heroicon-o-phone' => '📞 Phone',
                            'heroicon-o-user' => '👤 User',
                            'heroicon-o-users' => '👥 Users',
                            'heroicon-o-share' => '🔗 Share',
                            'heroicon-o-link' => '🔗 Link',
                            'heroicon-o-plus' => '➕ Plus',
                            'heroicon-o-x-mark' => '✕ Close',
                            'heroicon-o-check' => '✓ Check',
                            'heroicon-o-trash' => '🗑️ Trash',
                            'heroicon-o-pencil' => '✏️ Edit',
                            'heroicon-o-magnifying-glass' => '🔍 Search',
                            'heroicon-o-bell' => '🔔 Bell',
                            'heroicon-o-star' => '⭐ Star',
                            'heroicon-o-heart' => '❤️ Heart',
                            'heroicon-o-briefcase' => '💼 Business',
                            'heroicon-o-building-office' => '🏢 Office',
                            'heroicon-o-currency-dollar' => '💵 Dollar',
                            'heroicon-o-shopping-cart' => '🛒 Cart',
                            'heroicon-o-credit-card' => '💳 Card',
                            'heroicon-o-chart-bar' => '📊 Chart',
                            'heroicon-o-cog-6-tooth' => '⚙️ Settings',
                            'heroicon-o-wrench' => '🔧 Tools',
                            'heroicon-o-shield-check' => '🛡️ Security',
                            'heroicon-o-lock-closed' => '🔒 Lock',
                            'heroicon-o-key' => '🔑 Key',
                            'heroicon-o-information-circle' => 'ℹ️ Info',
                            'heroicon-o-question-mark-circle' => '❓ Help',
                            'heroicon-o-exclamation-circle' => '⚠️ Warning',
                            'heroicon-o-calendar' => '📅 Calendar',
                            'heroicon-o-clock' => '⏰ Clock',
                            'heroicon-o-location-marker' => '📍 Location',
                            'heroicon-o-globe-alt' => '🌐 Globe',
                            'heroicon-o-cloud' => '☁️ Cloud',
                            'heroicon-o-sun' => '☀️ Sun',
                            'heroicon-o-moon' => '🌙 Moon',
                            'heroicon-o-wifi' => '📶 Wifi',
                            'heroicon-o-rocket' => '🚀 Rocket',
                            'heroicon-o-fire' => '🔥 Fire',
                        ])
                        ->native(false),
                    TextInput::make('order')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->rules(['integer', 'min:0']),
                ]),
            Section::make('Settings')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Toggle::make('is_external')
                        ->label('External Link'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->alignCenter()
                    ->size('xs')
                    ->width('50px'),
                TextColumn::make('tree_name')
                    ->label('Menu Structure')
                    ->getStateUsing(function (Menu $record) {
                        $depth = $record->calculateDepth();
                        $indent = str_repeat('— ', $depth);
                        return $indent . $record->name;
                    })
                    ->wrap(),
                TextColumn::make('url')
                    ->limit(40)
                    ->icon(fn ($record) => $record->is_external ? 'heroicon-o-arrow-top-right-on-square' : null),
                TextColumn::make('order')
                    ->alignCenter(),
                ToggleColumn::make('is_active')
                    ->alignCenter(),
            ])
            ->actions([
                Action::make('moveUp')
                    ->icon('heroicon-o-arrow-up')
                    ->color('gray')
                    ->tooltip('Move up')
                    ->requiresConfirmation(false)
                    ->hidden(function (Menu $record) {
                        // Hide if no previous sibling exists
                        $query = Menu::where('id', '!=', $record->id);
                        if ($record->parent_id) {
                            $query->where('parent_id', $record->parent_id);
                        } else {
                            $query->whereNull('parent_id');
                        }
                        $count = $query->count();
                        return $count === 0 || $record->order <= 0;
                    })
                    ->action(function (Menu $record, $livewire) {
                        if ($record->order <= 0) return;
                        
                        $newOrder = $record->order - 1;
                        
                        // Get items in same parent scope (including current for re-sequence)
                        $baseQuery = Menu::query();
                        if ($record->parent_id) {
                            $baseQuery->where('parent_id', $record->parent_id);
                        } else {
                            $baseQuery->whereNull('parent_id');
                        }
                        
                        // Shift items at target order down by 1 (excluding current)
                        $conflicting = (clone $baseQuery)->where('id', '!=', $record->id)->where('order', $newOrder)->get();
                        foreach ($conflicting as $item) {
                            $item->update(['order' => $item->order + 1]);
                        }
                        
                        // Move current item up
                        $record->update(['order' => $newOrder]);
                        
                        // Re-sequence ALL items in group to be clean sequential
                        $allItems = (clone $baseQuery)->orderBy('order')->orderBy('id')->get();
                        foreach ($allItems as $index => $item) {
                            $item->update(['order' => $index]);
                        }
                        
                        $livewire->dispatch('refreshTable');
                    }),
                Action::make('moveDown')
                    ->icon('heroicon-o-arrow-down')
                    ->color('gray')
                    ->tooltip('Move down')
                    ->requiresConfirmation(false)
                    ->hidden(function (Menu $record) {
                        // Hide if no next sibling exists
                        $query = Menu::where('id', '!=', $record->id);
                        if ($record->parent_id) {
                            $query->where('parent_id', $record->parent_id);
                        } else {
                            $query->whereNull('parent_id');
                        }
                        $count = $query->count();
                        return $count === 0;
                    })
                    ->action(function (Menu $record, $livewire) {
                        $newOrder = $record->order + 1;
                        
                        // Get items in same parent scope (including current for re-sequence)
                        $baseQuery = Menu::query();
                        if ($record->parent_id) {
                            $baseQuery->where('parent_id', $record->parent_id);
                        } else {
                            $baseQuery->whereNull('parent_id');
                        }
                        
                        // Shift items at target order down by 1 (excluding current)
                        $conflicting = (clone $baseQuery)->where('id', '!=', $record->id)->where('order', $newOrder)->get();
                        foreach ($conflicting as $item) {
                            $item->update(['order' => $item->order + 1]);
                        }
                        
                        // Move current item down
                        $record->update(['order' => $newOrder]);
                        
                        // Re-sequence ALL items in group to be clean sequential
                        $allItems = (clone $baseQuery)->orderBy('order')->orderBy('id')->get();
                        foreach ($allItems as $index => $item) {
                            $item->update(['order' => $index]);
                        }
                        
                        $livewire->dispatch('refreshTable');
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->orderBy('parent_id')->orderBy('order'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
