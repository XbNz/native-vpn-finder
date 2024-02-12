<?php

namespace App\Livewire;

use App\Actions\RefreshVpnServersAction;
use App\Enums\DownloadType;
use App\Events\ServerInfoDownloadEvent;
use App\Models\ServerNetworkDetail;
use App\Models\VpnServer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use JJG\Ping;
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

    public function table(Table $table): Table
    {
        return $table
            ->query(ServerNetworkDetail::query())
            ->columns([
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hostname')
                    ->label('Host name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vpnServer.vpnProvider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vpnServer.country.name')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vpnServer.city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vpnServer.protocol')
                    ->label('Protocol')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('round_trip_time')
                    ->label('RTT')
                    ->sortable(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('refreshServers')
                    ->label('Refresh Servers')
                    ->action(fn() => app(RefreshVpnServersAction::class)->handle())
            ])
            ->bulkActions([
                BulkAction::make('copy_as_json')
                    ->label('Copy as JSON')
                    ->action(function (Collection $records) {
                        app(Dispatcher::class)->dispatch(
                            new ServerInfoDownloadEvent($records, DownloadType::Clipboard)
                        );
                    }),
                BulkAction::make('save_as_json')
                    ->label('Save as JSON')
                    ->action(function (Collection $records) {
                        app(Dispatcher::class)->dispatch(
                            new ServerInfoDownloadEvent($records, DownloadType::FileManager)
                        );
                    }),
//                BulkAction::make('ping_bulk')
//                    ->label('Ping selected')
//                    ->action(function (Collection $records) {
//                        $records->each(function (ServerNetworkDetail $record) {
//                            $pingResult = (new Ping($record->ip_address, timeout: 1))->ping();
//
//                            if ($pingResult === false) {
//                                return;
//                            }
//
//                            $record->update([
//                                'round_trip_time' => $pingResult,
//                            ]);
//                        });
//                    }),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('ping')
                    ->label('Ping')
                    ->action(function (ServerNetworkDetail $record) {
                        $pingResult = (new Ping($record->ip_address, timeout: 1))->ping();

                        if ($pingResult === false) {
                            return;
                        }

                        $record->update([
                            'round_trip_time' => $pingResult,
                        ]);
                    })
                    ->button()
            ]);
    }

//    protected function getTableFilters(): array
//    {
//        return [
//            SelectFilter::make('vpnServer.country.name')
//                ->multiple()
//                ->label('Country'),
//            SelectFilter::make('vpnServer.city.name')
//                ->multiple()
//                ->label('City'),
//        ];
//    }


}
