<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Contract;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\ContractExporter;
use App\Filament\Imports\ContractImporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\ContractResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ContractResource\RelationManagers;

class ContractResource extends Resource
{
    
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih Pegawai')
                    ->label('Nama Pegawai'),
                DatePicker::make('start_date')
                    ->label('Awal Kontrak')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Akhir Kontrak'),
                Select::make('agreement_type')
                    ->options([
                        'PKWT' => 'PKWT',
                        'PKWTT' => 'PKWTT',
                        'Magang' => 'Magang',
                    ])
                    ->required()
                    ->label('Tipe Kontrak'),
                
                TextInput::make('attachment')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Contract::query()
                    ->select('contracts.*')
                    ->joinSub(
                        Contract::selectRaw('user_id, MAX(start_date) as latest_start_date')
                            ->groupBy('user_id'),
                        'latest_contracts',
                        function ($join) {
                            $join->on('contracts.user_id', '=', 'latest_contracts.user_id')
                                ->on('contracts.start_date', '=', 'latest_contracts.latest_start_date');
                        }
                    )
            )
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('agreement_type')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('view_contracts')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn (Contract $record) => route('filament.admin.resources.contracts.user-history', ['record' => $record->user_id]))
                ])
                ->icon('heroicon-o-bars-3')
                ->label('Actions')
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
           'user-history' => Pages\UserContractHistory::route('/{record}/user-history'),
        ];
    }
}
