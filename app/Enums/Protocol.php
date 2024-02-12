<?php

namespace App\Enums;

enum Protocol: string
{
    case OpenVPN = 'openvpn';
    case WireGuard = 'wireguard';
}
