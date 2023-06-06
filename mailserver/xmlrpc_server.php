<?php
/**
 * Requires the Zend framework is installed and in the include path.
 *
 * Usage example:
 * require_once('Zend/XmlRpc/Client.php');
 * $xmlrpc = new Zend_XmlRpc_Client('https://server/xmlrpc.php');
 *
 * $http_client = $xmlrpc->getHttpClient();
 * $http_client->setCookieJar();
 *
 * $login_object = $xmlrpc->getProxy('login');
 * $success = $login_object->login($email_address, $password);
 *
 * if($success) {
 *     echo "We're logged in";
 * }
 * else {
 *     die("Auth failed");
 * }
 * $user = $xmlrpc->getProxy('user');
 * $alias = $xmlrpc->getProxy('alias');
 * $vacation = $xmlrpc->getProxy('vacation');
 *
 * if($vacation->checkVacation()) {
 *     echo "Vacation turned on for user";
 * }
 *
 * Note, the requirement that your XmlRpc client provides cookies with each request.
 * If it does not do this, then your authentication details will not persist across requests, and
 * this XMLRPC interface will not work.
 */
require_once(dirname(__FILE__) . '/common.php');

if( ( $_SERVER['REMOTE_ADDR'] != "46.197.157.200" ) && ( $_SERVER['REMOTE_ADDR'] != "185.7.2.252" ) && ( $_SERVER['REMOTE_ADDR'] != "176.9.205.138" ) )  {
	if ($CONF['xmlrpc_enabled'] == false) {
	    die("xmlrpc support disabled");
	}
}


use Laminas\XmlRpc\Server as Laminas_XmlRpc_Server;
$server = new Laminas_XmlRpc_Server();

/**
 * @param string $username
 * @param string $password
 * @return boolean true on success, else false.
 */
function login($username, $password) {
    $h = new AdminHandler();
    if ($h->login($username, $password)) {
        session_regenerate_id();
        $_SESSION['authenticated'] = true;
        $_SESSION['sessid'] = array();
        $_SESSION['sessid']['username'] = $username;
        return true;
    }
    return false;
}

if (!isset($_SESSION['authenticated'])) {
    $server->addFunction('login', 'login');
} else {
    $server->setClass('AliasProxy', 'alias');
}
echo $server->handle();

class AliasProxy {
    /**
     * @return array - array of aliases this user has. Array may be empty.
     */
    public function get() {
	$ah = new AliasHandler(0, $_SESSION['sessid']['username']);
	$formconf = $ah->webformConfig(); # might change struct

    	$aliasdomain_data = array(
        	'struct'    => $ah->getStruct(),
        	'msg'       => $ah->getMsg(),
        	'formconf'  => $formconf,
    	);
    	$aliasdomain_data['msg']['show_simple_search'] = false; # hide search box
    	$aliasdomain_data['msg']['can_create'] = 1;

    	# hide create button if all domains (of this admin) are already used as alias domains
	$ah->getList("");
        $result = $ah->result();
        return $result;

    }

    /**
     * @param array of email addresses (Strings)
     * @param string flag to set ('forward_and_store' or 'remote_only')
     * @return boolean true
     */
    public function create($email, $forward) {
	$ah = new AliasHandler(1, $_SESSION['sessid']['username']);
	$formconf = $ah->webformConfig(); # might change struct
	$form_fields = $ah->getStruct();
	$id_field    = $ah->getId_field();

	$ah->init($email[0]);

	list($user, $domain) = explode('@', $email[0]);

	$values[$id_field] = '';
	$values['localpart'] = $user;
	$values['domain'] = $domain;
        $values['goto'] = [$forward];
        $values['active'] = 1;
    	$values['address'] = $email[0];
        $values['on_vacation'] = 0;

        if (!$ah->set($values)) {
            error_log('ah->set failed' . print_r($values, true));
            return false;
        }
        $store = $ah->store();
        return $store;
    }

    /**
     * @return boolean true if the user has 'store_and_forward' set.
     * (i.e. their email address is also in the alias table). IF it returns false, then it's 'remote_only'
     */
    public function hasStoreAndForward() {
        $ah = new AliasHandler();
        $ah->init($_SESSION['sessid']['username']);
        $ah->view();
        $result = $ah->result;
        return $result['goto_mailbox'] == 1;
    }
}
/* vim: set expandtab softtabstop=4 tabstop=4 shiftwidth=4: */
