<?php

namespace App\Listeners;

use App\Enums\DownloadType;
use App\Events\ServerInfoDownloadEvent;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Native\Laravel\Clipboard;
use Native\Laravel\Dialog;

class ServerInfoDownloadListener
{
    public function __construct(
        private readonly Clipboard $clipboard,
        private readonly Dialog $dialog,
        private readonly FilesystemManager $filesystem,
    ) {
    }

    public function handle(ServerInfoDownloadEvent $event): void
    {
        match ($event->downloadType) {
            DownloadType::Clipboard => $this->copyToClipboard($event),
            DownloadType::FileManager => $this->saveToFileManagerPath($event),
        };
    }

    private function copyToClipboard(ServerInfoDownloadEvent $event): void
    {
        $this->clipboard->clear();
        $this->clipboard->text(
            $event->serverNetworkDetails->toJson()
        );
    }

    private function saveToFileManagerPath(ServerInfoDownloadEvent $event): void
    {
        $disk = $this->filesystem->build([
            'driver' => 'local',
            'root' => '/',
        ]);

        $pathToSave = $this
            ->dialog
            ->title('Save VPN server info')
            ->save();


        $disk->put(
            $pathToSave,
            $event->serverNetworkDetails->toJson()
        );
    }
}
