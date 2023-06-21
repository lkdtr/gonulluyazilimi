<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

use Laminas\XmlRpc\Client as Laminas_XmlRpc_Client;

trait PostfixAdmin
{

    private function init_server() {

        $server = config('services.postfixadmin.server');
        $username = config('services.postfixadmin.username');
        $password = config('services.postfixadmin.password');

        $xmlrpc = new Laminas_XmlRpc_Client('https://'.$server.'/xmlrpc.php');
        $http_client = $xmlrpc->getHttpClient();

        $login_object = $xmlrpc->getProxy('login');
        $result = $login_object->login($username, $password);
        if($result) {
            return $xmlrpc;
        }
        else {
            return false;
        }
    }

    public function create_alias($alias_email, $target_email) {
        $xmlrpc = $this->init_server();

        if($xmlrpc) {
            $alias = $xmlrpc->getProxy('alias');
            $result = $alias->create($alias_email, $target_email);

            if($result) {
                return $result;
            }
        }

        return false;
    }

}

