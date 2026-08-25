<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Laminas\XmlRpc\Client as XmlRpcClient;

class PostfixAdminClient
{
    public function updateAlias(string $aliasEmail, string $targetEmail): bool
    {
        try {
            $client = new XmlRpcClient('https://'.config('services.postfixadmin.server').'/xmlrpc.php');
            $login = $client->getProxy('login');

            if (! $login->login(config('services.postfixadmin.username'), config('services.postfixadmin.password'))) {
                return false;
            }

            return (bool) $client->getProxy('alias')->update($aliasEmail, $targetEmail);
        } catch (\Throwable $exception) {
            Log::error('PostfixAdmin alias update failed.', [
                'alias' => $aliasEmail,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
