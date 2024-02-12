<?php

namespace App\Actions;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Throwable;
use Psl\Type;

class RefreshVpnServersAction
{
    public function __construct(
        private readonly DatabaseManager $database,
    ) {
    }

    public function handle(): void
    {
        $this->database->beginTransaction();

        try {
            $serversRaw = Http::get('https://raw.githubusercontent.com/qdm12/gluetun/master/internal/storage/servers.json')
                ->throw()
                ->json();

            Arr::forget($serversRaw, 'version');

//            dd($serversRaw['surfshark']['servers'][0]);

            $servers = Type\non_empty_dict(
                Type\string(),
                Type\shape([
                    'version' => Type\uint(),
                    'timestamp' => Type\uint(),
                    'servers' => Type\vec(Type\shape([
                        'vpn' => Type\optional(Type\string()),
                        'country' => Type\optional(Type\string()),
                        'region' => Type\optional(Type\string()),
                        'city' => Type\optional(Type\string()),
                        'hostname' => Type\optional(Type\string()),
                        'ips' => Type\non_empty_vec(Type\string()),
                    ])),
                ]),
            )->coerce($serversRaw);


//            $servers = Type\non_empty_vec(
//                Type\non_empty_dict(
//                    Type\string(),
//                    Type\shape([
//                        'version' => Type\uint(),
//                        'timestamp' => Type\uint(),
//                        'servers' => Type\vec(Type\shape([
//                            'country' => Type\non_empty_string(),
//                            'city' => Type\non_empty_string(),
//                            'hostname' => Type\non_empty_string(),
//                            'ips' => Type\non_empty_vec(Type\non_empty_string()),
//                        ])),
//                    ]),
//                ),
//            )->coerce($serversRaw);

            dd($servers);

            $this->database->commit();
        } catch (Throwable $e) {
            $this->database->rollBack();
            throw $e;
        }
    }
}
