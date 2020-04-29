<?php

namespace Webbhuset\CollectorCheckoutSDK\Adapter;

class Request
{
    public static function getSharedKeyHeader(
        string $userName,
        string $body,
        string $path,
        string $sharedAccessKey
    ) : string
    {
        $hashParts  = $body . $path . $sharedAccessKey;
        $shaHash    = hash('sha256', $hashParts);
        $hash       = "{$userName}:{$shaHash}";
        $base64hash = base64_encode($hash);

        return "SharedKey {$base64hash}";
    }
}
