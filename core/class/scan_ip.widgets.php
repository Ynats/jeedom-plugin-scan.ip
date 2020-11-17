<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_widgets extends eqLogic {
    
    public static function getWidgetType($_eq) {
        return $_eq->getConfiguration('type_widget', 'normal');
    }
    
    public static function getIdWidgetSpeciaux(){
        $return = NULL;
        $widgetDetect = 0;
        $widgetMax = 2;
        foreach ($eqLogics as $eqLogic) {
            if($eqLogic->getConfiguration('type_widget', 'normal') == "network"){
                $return["network"] = $eqLogic->getId();
                $widgetDetect++;
            } 
            elseif($eqLogic->getConfiguration('type_widget', 'normal') == "new_equipement"){
                $return["new_equipement"] = $eqLogic->getId();
                $widgetDetect++;
            }
            if($widgetDetect >= $widgetMax){
                break;
            }
        }
        return $return;
    }

}
