<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_cmd extends eqLogic {
    
    public static function getCommande($_ComName, $_this){
        $tmp_cmd = $_this->getCmd(null, $_ComName);
        return (is_object($tmp_cmd)) ? $tmp_cmd->execCmd() : '';
    }
    
    public static function cmdRefresh($_eqlogic, $_mapping = NULL){

        log::add('scan_ip', 'debug', 'cmdRefresh :. Lancement');
        
        if($_mapping == NULL){
            $mapping = scan_ip_json::getJson(scan_ip::$_jsonMapping);
        } else {
            $mapping = $_mapping;
        }
        
        switch (scan_ip_widgets::getWidgetType($_eqlogic)) {
            case "normal":
                scan_ip_widget_normal::cmdRefreshWidgetNormal($_eqlogic, $mapping);
                break;
            case "new_equipement":
                scan_ip_widget_alerte::cmdRefreshWidgetAlerte($_eqlogic, $mapping);
                break;
        }
        
        $_eqlogic->toHtml('dashboard');
        $_eqlogic->refreshWidget();
    }
    
}
