<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Data')
                ->schema([
                    TextInput::make('nip')
                        ->label('NIP')
                        ->disabled()
                        ->placeholder('NIP Will Be Created Automatically')
                        ->visible(fn ($livewire) => $livewire instanceof EditRecord),
                        
                    TextInput::make('nik')
                        ->required()    
                        ->label('NIK')
                        ->maxLength(20) 
                        ->validationMessages([
                            'unique' => 'This NIK has already been taken',
                        ])
                        ->rules(function ($state, $get, $livewire) {
                            $recordId = $livewire->record?->getKey();
                            return [
                                Rule::unique('users', 'nik')->ignore($recordId),
                            ];
                        })
                        ->placeholder('Entry NIK')
                        ->visible(fn ($livewire) => $livewire instanceof CreateRecord || $livewire instanceof EditRecord),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(50)    
                        ->label('Full Name')
                        ->autofocus()
                        ->placeholder('Entry Full Name')
                        ->dehydrateStateUsing(fn ($state) => Str::title($state))
                        ->afterStateUpdated(fn (&$state) => $state = Str::title($state))
                        ->visible(fn ($livewire) => $livewire instanceof CreateRecord || $livewire instanceof EditRecord),
                    
                    Radio::make('gender')
                        ->label('Gender')
                        ->required()
                        ->options([
                            'laki_laki' => 'Laki-laki',
                            'perempuan' => 'Perempuan'
                        ])
                        ->inline()
                        ->inlineLabel(false)
                        ->visible(fn ($livewire) => $livewire instanceof CreateRecord || $livewire instanceof EditRecord),


                    TextInput::make('phone')
                        ->required()
                        ->maxLength(20)
                        ->label('Phone')
                        ->placeholder('Entry Phone Number')
                        ->tel()
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                        ->dehydrateStateUsing(function ($state) {
                            if (str_starts_with($state, '0')) {
                                return '+62' . substr($state, 1);
                            }
                            return $state;
                        })
                        ->visible(fn ($livewire) => $livewire instanceof CreateRecord || $livewire instanceof EditRecord),

                    TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->autofocus()
                        ->email()
                        ->placeholder('Entry Email')
                        ->visible(fn ($livewire) => $livewire instanceof CreateRecord || $livewire instanceof EditRecord),
                    
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->placeholder('Entry Password')
                        ->autocomplete('new-password')
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                        ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                    
                  
                    
                    Placeholder::make('nip')
                        ->label('NIP')
                        ->content(fn ($record) => $record->nip ?? '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),

                    Placeholder::make('nik')
                        ->label('NIK')
                        ->content(fn ($record) => $record->nik ?? '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),

                   
                  
                    
                    Placeholder::make('name')
                        ->label('Full Name')
                        ->content(fn ($record) => $record->name ?? '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                
                    Placeholder::make('phone')
                        ->label('Phone Number')
                        ->content(fn ($record) => $record->phone ?? '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                    Placeholder::make('email')
                        ->label('Email')
                        ->content(fn ($record) => $record->email ?? '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                    
                    Placeholder::make('gender')
                        ->label('Gender')
                        ->content(fn ($record) => match ($record->gender) {
                            'laki_laki' => 'Laki-laki',
                            'perempuan' => 'Perempuan',
                            default => '-',
                        })
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                ])
                ->columns(2),

                Section::make('Contract Detail')
                ->schema([
                    DatePicker::make('join_date')
                        ->label('Join Date')
                        ->required()
                        ->displayFormat('d M Y')
                        ->native(false)
                        ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord || $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    
                    Select::make('division_id')
                        ->relationship('division', 'name')
                        ->label('Division')
                        ->required()
                        ->placeholder('Choose Division')
                        ->visible(fn ($livewire) => $livewire instanceof CreateRecord || $livewire instanceof EditRecord),
                    
                    Placeholder::make('agreement_type_display')
                        ->label('Aggeement Type')
                        ->content(fn ($record) => match(optional($record->contracts()->latest('end_date')->first())->agreement_type) {
                            'PKWT' => 'PKWT',
                            'PKWTT' => 'PKWTT',
                            'Intership' => 'Intership',
                            default => '-',
                        })
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]'
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\ViewRecord),

                    Placeholder::make('division_id')
                        ->label('Divisi')
                        ->content(fn ($record) => $record->division?->name ?? '-')
                        ->extraAttributes(['class' => 'text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800'])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                    
                    Placeholder::make('join_date')
                        ->label('Join Date')
                        ->content(fn ($record) => $record->join_date ? Carbon::parse($record->join_date)->format('d M Y') : '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white bg-gray-100 dark:bg-gray-800 border rounded-md px-3 py-2 shadow-sm',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                    
                    Placeholder::make('latest_contract_end_date')
                        ->label('Ends Contract')
                        ->content(fn ($record) =>
                            $record->contracts()
                                ->orderByDesc('end_date')
                                ->value('end_date')
                                ? Carbon::parse($record->contracts()->orderByDesc('end_date')->value('end_date'))->format('d M Y')
                                : '-'
                        )
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white bg-gray-100 dark:bg-gray-800 border rounded-md px-3 py-2 shadow-sm',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                    Placeholder::make('working_duration')
                        ->label('Works Duration')
                        ->content(fn ($record) =>
                            $record->working_duration ?? '-'
                        )
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white bg-gray-100 dark:bg-gray-800 border rounded-md px-3 py-2 shadow-sm',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),

                    Placeholder::make('remaining_days')
                        ->label('Remaining Contract')
                        ->content(fn ($record) => $record->remaining_contract_days ?? '-')
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]'
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof ViewRecord),
                    \Filament\Forms\Components\Hidden::make('status')
                        ->default('active'),
                    
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active'   => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    
                    Placeholder::make('status')
                        ->label('Status')
                        ->content(fn ($record) => match(strtolower($record->status)) {
                            'active' => '🟢 Active',
                            'inactive' => '🔴 Inactiver',
                            default => ucfirst($record->status),
                        })
                        ->extraAttributes([
                            'class' => 'text-sm text-black dark:text-white font-medium border rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-800 shadow-sm min-h-[38px]'
                        ])
                        ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\ViewRecord),
                    
                    
                ])
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('nip')
                ->searchable()
                ->sortable()
                ->label('NIP'),

            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Name'),

            TextColumn::make('phone')
                ->searchable()
                ->label('Phone Number')
                ->copyable()
                ->copyMessage('Copied Successfully!')
                ->copyMessageDuration(1500),

            TextColumn::make('division.name')
                ->label('Division')
                ->searchable(),
            
            TextColumn::make('status')
                ->label('Status')
                ->formatStateUsing(function ($state) {
                    $mapping = [
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ];
            
                    return $mapping[$state] ?? $state;
                })
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                }),
        ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ViewAction::make(),
                ])
                ->icon('heroicon-o-bars-3') 
                ->label('Aksi')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
