<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_api_mac_vendor extends eqLogic {
    
    public static $_jsonMajVendorApi = __DIR__ . "/../../../../plugins/scan_ip/data/json/majVendorApi";
    public static $_jsonRoulement = 3600 * 7; // 3600 secondes = 1 journée
    
    public static function get_MacVendor($_mac){
        log::add('scan_ip', 'debug', 'get_MacVendor :. Lancement');
        
        $now = time();
        $jsonMajVendorApi = scan_ip_json::getJson(self::$_jsonMajVendorApi);
        
        if(!empty($jsonMajVendorApi[$_mac])){
            if(($now - $jsonMajVendorApi[$_mac]) >= self::$_jsonRoulement){
                $pass = TRUE;
            } else {
                $pass = FALSE;
            }
        } else {
            $pass = TRUE;
        }
        
        if($pass == TRUE){
            $return = self::get_MacvendorsCom($_mac);
        
            if($return == NULL){
                $return = self::get_MacvendorsCo($_mac);
            }

            if($return != NULL){
                scan_ip_json::createJsonFile(self::$_jsonMajVendorApi, $jsonMajVendorApi);
                return $return;
            } else {
                $jsonMajVendorApi[$_mac] = $now;
                scan_ip_json::createJsonFile(self::$_jsonMajVendorApi, $jsonMajVendorApi);
                return "...";
            }
        }
         
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TESTS SUR DIFFERENTES API
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function get_MacvendorsCom($_mac){
        log::add('scan_ip', 'debug', 'get_MacvendorsCom :. Lancement de la recherche');
        sleep(2);
        $url = "https://api.macvendors.com/" . urlencode($_mac);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), TRUE);         
        if(!empty($response) AND empty($response["errors"])) {
            log::add('scan_ip', 'debug', 'get_MacvendorsCom :. Trouvé '.$response);
            return $response;
        } else {
            log::add('scan_ip', 'debug', 'get_MacvendorsCom :. Pas Trouvé');
            return NULL;
        }
    } 
    
    public static function get_MacvendorsCo($_mac) {
        log::add('scan_ip', 'debug', 'get_MacvendorsCo :. Lancement de la recherche');
        sleep(2);
        $url = "https://macvendors.co/api/" . urlencode($_mac);
        $response = json_decode(file_get_contents($url))->result;
        if(!empty($response->company)){
            log::add('scan_ip', 'debug', 'get_MacvendorsCo :. Trouvé '.$response->company);
            return $response->company;
        } else {
            log::add('scan_ip', 'debug', 'get_MacvendorsCo :. Pas Trouvé');
            return NULL;
        }
    }
    
}
