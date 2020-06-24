<?php
declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

class IpAddressSettings
{
    public const ATTRIBUTE_NAME = 'Client-Ip';
    public const CHECK_PROXY_HEADERS = true;
    public const TRUSTED_PROXIES = [];
    public const CLOUDFLARE_HEADERS_TO_INSPECT = [
        'CF-Connecting-IP',
        'True-Client-IP',
        'Forwarded',
        'X-Forwarded-For',
        'X-Forwarded',
        'X-Cluster-Client-Ip',
        'Client-Ip',
    ];
    public const HEADERS_TO_INSPECT = [
        'CF-Connecting-IP',
        'True-Client-IP',
        'X-Real-IP',
        'Forwarded',
        'X-Forwarded-For',
        'X-Forwarded',
        'X-Cluster-Client-Ip',
        'Client-Ip',
    ];
}