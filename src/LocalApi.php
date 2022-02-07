<?php
	
namespace WHMCS\Cloud4Africa;

use WHMCS\Database\Capsule;

class LocalApi 
{
	private $admin;

	public function __construct($admin){
		$this->admin = $admin;
	}

	public function getRawProducts()
	{
	    $sql = "SELECT * FROM tblproducts";
        return Capsule::connection()->select($sql);
	}
	
	public function getRawProduct($productId)
	{
	    $sql = "SELECT * FROM tblproducts WHERE id = ".$productId;
	    return Capsule::connection()->select($sql);
	}
	
	public function countClientProductsByServerType($clientId, $serverType)
	{
		$sql = "SELECT COUNT(DISTINCT(hosting.id)) as count
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                WHERE product.servertype = '".$serverType."' AND hosting.userid = '".$clientId."'"
        ;
        $results = Capsule::connection()->select($sql);
        
        return $results[0]->count;
	}

	public function getClientProductsByServerType($clientId, $serverType)
	{
		$sql = "SELECT hosting.id as id, hosting.domain as domain, hosting.domainstatus as status, product.name as name
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                WHERE product.servertype = '".$serverType."' AND hosting.userid = '".$clientId."'"
        ;
        return Capsule::connection()->select($sql);
	}

	public function getClientProductByServerType($clientId, $productId, $serverType)
	{
	    $sql = "SELECT hosting.id as id, hosting.domain as domain, hosting.domainstatus as status, product.name as name
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                WHERE product.servertype = '".$serverType."' AND hosting.userid = '".$clientId."' AND hosting.id = '".$productId."'"
                    ;
                    return Capsule::connection()->select($sql);
	}

	public function getClientProductRegion($productId)
	{
	    $sql = "SELECT productconfigoptionssub.optionname
                FROM tblhosting AS hosting
                JOIN tblhostingconfigoptions AS hostingconfigoption
                ON hostingconfigoption.relid = hosting.id
                JOIN tblproductconfigoptionssub AS productconfigoptionssub
                ON productconfigoptionssub.configid = hostingconfigoption.configid AND productconfigoptionssub.id = hostingconfigoption.optionid
                JOIN tblproductconfigoptions AS productconfigoptions
                ON productconfigoptions.id = hostingconfigoption.configid AND productconfigoptions.optiontype = 1
                WHERE hosting.id = ".$productId
        ;
        return Capsule::connection()->select($sql);
	}

	public function getClientProductCustomFieldData($productId)
	{
	    $sql = "SELECT customfieldvalue.id, customfieldvalue.value, customfield.fieldname
                FROM tblhosting AS hosting
                JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE hosting.id = ".$productId
        ;
        
        return Capsule::connection()->select($sql);
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
	    			$response = $domain;
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

	public function getClientProductById($clientId, $productId) {
		$command = 'GetClientsProducts';
	    $postData = array(
	        'clientid' => $clientId,
	        'pid' => $productId,
	        'stats' => true,
	    );

	    return localAPI($command, $postData, $this->admin);
	}

	public function getClientProductByDomain($clientId, $domain) {
		$command = 'GetClientsProducts';
	    $postData = array(
	        'clientid' => $clientId,
	        'domain' => $domain,
	        'stats' => true,
	    );

	    $response = localAPI($command, $postData, $this->admin);

	    if ($response['totalresults'] > 0) {
	    	return $response['products']['product'][0];
	    }

	    return null;
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
