<?php

namespace App\Livewire;

use App\Models\VpnServer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class ListVpnServers extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function render()
    {
        return view('livewire.list-vpn-servers');
    }

    public function refreshServersAction(): Action
    {
        return Action::make('refreshServers')
            ->label('Refresh Servers')
            ->action(function () {
//                Artisan::call('db:seed');
//                dd('test');
            });
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(VpnServer::query())
            ->columns([
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('domain'),
                TextColumn::make('vpnProvider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([

            ]);
    }


}
