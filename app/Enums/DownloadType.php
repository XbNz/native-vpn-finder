<?php

namespace App\Enums;

enum DownloadType: string
{
    case Clipboard = 'clipboard';
    case FileManager = 'file-manager';
}
