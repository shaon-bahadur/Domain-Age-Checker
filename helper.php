<?php
/**
 * @package TechSpaceHub
 *
 * @copyright (C) 2022 Tech Space Hub.
 * @license GNU General Public License version 3 or later
 */
 
defined('_JEXEC') or die;

class ModDomainAgeCheckerHelper
{
    public function getDataAjax()
    {
		$WHOIS_SERVERS = array(
			"com" => array("whois.verisign-grs.com", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"net" => array("whois.verisign-grs.com", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"org" => array("whois.pir.org", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"info" => array("whois.afilias.info", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"biz" => array("whois.neulevel.biz", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"us" => array("whois.nic.us", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"uk" => array("whois.nic.uk", "/Registered on:(.*)/", "/Expiry date:(.*)/", "/Last updated:(.*)/", "/Registrar:(.*)/", "/Name servers:(.*)/"), 
			"ca" => array("whois.cira.ca", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"tel" => array("whois.nic.tel", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"ie" => array("whois.iedr.ie", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"it" => array("whois.nic.it", "/Created:(.*)/", "/Expire Date:(.*)/", "/Last Update:(.*)/", "/Organization:(.*)/", "/Nameservers:(.*)/"), 
			"cc" => array("whois.nic.cc", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"ws" => array("whois.website.ws", "/Creation Date:(.*)/", "/Registrar Registration Expiration Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"sc" => array("whois2.afilias-grs.net", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"mobi" => array("whois.dotmobiregistry.net", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"pro" => array("whois.registrypro.pro", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"edu" => array("whois.educause.net", "/Domain record activated:(.*)/", "/Domain expires:(.*)/", "/Domain record last updated:(.*)/", "/Registrant:(.*)/", "/Name Servers:(.*)/"), 
			"tv" => array("whois.nic.tv", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"travel" => array("whois.nic.travel", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"in" => array("whois.registry.in", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"me" => array("whois.nic.me", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"cn" => array("whois.cnnic.cn", "/Registration Time:(.*)/", "/Expiration Time:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"asia" => array("whois.nic.asia", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"ro" => array("whois.rotld.ro", "/Registered On:(.*)/", "/Expires On:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Nameserver:(.*)/"), 
			"aero" => array("whois.aero", "/Creation Date:(.*)/", "/Registry Expiry Date:(.*)/", "/Updated Date:(.*)/", "/Registrar:(.*)/", "/Name Server:(.*)/"), 
			"nu" => array("whois.iis.nu", "/created:(.*)/", "/expires:(.*)/", "/modified:(.*)/", "/registrar:(.*)/", "/nserver:(.*)/")
		);
		$domain     =   JFactory::getApplication()->input->get('domain_age_input');
		$domain     =   preg_replace('/\s/', '', $domain);
        $domain = trim($domain); 
        if (substr(strtolower($domain), 0, 7) == "http://")
            $domain = substr($domain, 7); 
        if (substr(strtolower($domain), 0, 8) == "https://")
            $domain = substr($domain, 8); 
        if (substr(strtolower($domain), 0, 4) == "www.")
            $domain = substr($domain, 4); 
        if (preg_match("/^([-a-z0-9]{2,100}).([a-z.]{2,8})$/i", $domain)) {
            $domain_parts = explode(".", $domain);
            $tld          = strtolower(array_pop($domain_parts));
			
            if (!$server = @$WHOIS_SERVERS[$tld][0]) {
                return 'notFound';
            }
            $res = QueryWhoisServer($server, $domain);		

            if (preg_match($WHOIS_SERVERS[$tld][1], $res, $ageMatch)) {
                date_default_timezone_set('UTC');
                $time  = time() - strtotime($ageMatch[1]);
                $years = floor($time / 31556926);
                $days  = floor(($time % 31556926) / 86400);
                if ($years == "1") {
                    $y = "1 year";
                } else {
                    $y = $years . " years";
                }
                if ($days == "1") {
                    $d = "1 day";
                } else {
                    $d = $days . " days";
                }
				$creationDate = strtotime($ageMatch[1]);
				$creationDate = date('Y-m-d H:i:s', $creationDate);	
            } 
			if(@preg_match($WHOIS_SERVERS[$tld][2], $res, $expMatch)){	
				$expireDate = strtotime($expMatch[1]);
				$expireDate = date('Y-m-d H:i:s', $expireDate);		
			}
			if(@preg_match($WHOIS_SERVERS[$tld][3], $res, $updateMatch)){	
				$updatedDate = strtotime($updateMatch[1]);
				$updatedDate = date('Y-m-d H:i:s', $updatedDate);		
			}
			if(@preg_match($WHOIS_SERVERS[$tld][4], $res, $registrarMatch)){	
				$registrarName = trim($registrarMatch[1]);	
			}
			if(@preg_match($WHOIS_SERVERS[$tld][5], $res, $nameserverMatch)){	
				$nameServer = trim($nameserverMatch[1]);	
			}
			if(!@$creationDate){
				return 'notFound';
			}
			else{
				return '<h2 class="resultTitle">Results</h2><table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="resultTable"><tr><th>Domain Name</th><td style="background: #e8f0fe;font-weight: bold;">'.@$domain.'</td></tr><tr><th>Created on</th><td>'.@$creationDate.'</td></tr><tr><th>Age</th><td>'.@$y.', '.@$d.'</td></tr><tr><th>Updated on</th><td>'.@$updatedDate.'</td></tr><tr><th>Expiration Date</th><td>'.@$expireDate.'</td></tr><tr><th>Registrar</th><td>'.@$registrarName.'</td></tr><tr><th>Name Server</th><td>'.@$nameServer.'</td></tr></table>';
			}
        } 
		else{
            return false;
		}
    }
}

function QueryWhoisServer($whoisserver, $domain)
{
	$port    = 43;
	$timeout = 10;
	$fp = @fsockopen($whoisserver, $port, $errno, $errstr, $timeout) or die("Socket Error " . $errno . " - " . $errstr);
	fputs($fp, $domain . "\r\n");
	$out = "";
	while (!feof($fp)) {
		$out .= fgets($fp);
	}
	fclose($fp);
	
	$res = "";
	if ((strpos(strtolower($out), "error") === FALSE) && (strpos(strtolower($out), "not allocated") === FALSE)) {
		$rows = explode("\n", $out);
		foreach ($rows as $row) {
			$row = trim($row);
			if (($row != '') && ($row{0} != '#') && ($row{0} != '%')) {
				$res .= $row . "\n";
			}
		}
	}
	return $res;
}

?>