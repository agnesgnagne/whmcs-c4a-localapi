<?php
	
namespace WHMCS\Cloud4Africa;

use WHMCS\Database\Capsule;

class LocalApi 
{
	private $admin;

	public function __construct($admin){
		$this->admin = $admin;
	}

	public function getClientDomains($clientId) {
		$command = 'GetClientsDomains';
	    $postData = array(
	        'clientid' => $clientId,
	        'stats' => true,
	    );

	    return localAPI($command, $postData, $this->admin);
	}

	public function getClientDomain($clientId, $domainName) {
		$command = 'GetClientsDomains';
	    $postData = array(
	        'clientid' => $clientId,
	        'stats' => true,
	    );
	    $response = null;
	    $results = localAPI($command, $postData, $this->admin);

	    if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
	    	$domains = $results['domains']['domain'];
	    	foreach ($domains as $domain) {
	    		if ($domain['domainname'] == $domainName) {
	    			$response[] = $domain;
	    		}
	    	}
	    }

	    return $response;
	}

	public function getClientActiveDomains($clientId) {
		$command = 'GetClientsDomains';
	    $postData = array(
	        'clientid' => $clientId,
	        'stats' => true,
	    );

	    $results = localAPI($command, $postData, $this->admin);
	    if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
	    	$domains = $results['domains']['domain'];
	    	foreach ($domains as $domain) {
	    		if ($domain['status'] == 'Active') {
	    			$response[] = $domain;
	    		}
	    	}
	    }

	    return $response;
	}

	public function getClientPendingDomains($clientId) {
		$command = 'GetClientsDomains';
	    $postData = array(
	        'clientid' => $clientId,
	        'stats' => true,
	    );

	    $results = localAPI($command, $postData, $this->admin);
	    if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
	    	$domains = $results['domains']['domain'];
	    	foreach ($domains as $domain) {
	    		if ($domain['status'] == 'Pending') {
	    			$response[] = $domain;
	    		}
	    	}
	    }

	    return $response;
	}

	public function getClientExpiredDomains($clientId) {
		$command = 'GetClientsDomains';
	    $postData = array(
	        'clientid' => $clientId,
	        'stats' => true,
	    );

	    $results = localAPI($command, $postData, $this->admin);
	    if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
	    	$domains = $results['domains']['domain'];
	    	$date = new \Datetime();

	    	foreach ($domains as $domain) {
	    		if ($domain['status'] != 'Active' && $domain['status'] != 'Pending' && $domain['expirydate'] < $date->format('Y-m-d')) {
	    			$response[] = $domain;
	    		}
	    	}
	    }

	    return $response;
	}

	public function getClientProducts($clientId) {
		$command = 'GetClientsProducts';
	    $postData = array(
	        'clientid' => $clientId,
	        'stats' => true,
	    );

	    return localAPI($command, $postData, $this->admin);
	}
	
	public function getClientDetails($clientId) {
        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $clientId,
            'stats' => true,
        );
        
        return localAPI($command, $postData, $this->admin);
    }

    public function getDomainWhois($domain) {
        $command = 'DomainWhois';
        $postData = array(
            'domain' => $domain,
        );

        return localAPI($command, $postData, $this->admin);
    }
}

?>
