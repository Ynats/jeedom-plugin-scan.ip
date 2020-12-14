<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_bridges extends eqLogic {
    
    public static $_jsonBridges = __DIR__ . "/../../../../plugins/scan_ip/data/json/bridges.json";
    public static $_folderBridges = __DIR__ . "/../../../../plugins/scan_ip/core/class/bridges/";
    public static $_defaut_bridges_by_equipement = 10;
    
    public static function bridges_printPlugs($_nb = 100, $_start = 0){     
        $i =1;
        $allBridges = self::getJsonBridges();
        natcasesort($allBridges);
        foreach ($allBridges as $gridge) {      
            if($i > $_start AND $i <= ($_start + $_nb)) {
                if(self::bridges_pluginExists($gridge)){
                    echo "<div><span style='font-weight: bold;'>".$gridge."</span> <span style='color:green;'>(Installé)</span></div>";
                } else {
                    echo "<div><span style='font-weight: bold;'>".$gridge."</span> <span style='color:orange;'>(Non installé)</span></div>";
                }
            }
            $i++;
        }
    }
    
    public static function getJsonBridges(){
        return json_decode(file_get_contents(self::$_jsonBridges),true);
    }
    
    public static function bridges_require($_gridge){
        require_once(self::$_folderBridges . $_gridge . ".php");
    }
    
    public static function bridges_getPlugsElements($_bridges){
        self::bridges_require($_bridges);
        $class = "scan_ip_".$_bridges;
        $Get = new $class;
        return $Get->getAllElements();
    }
    
    public static function bridges_getElements(){
        $array = NULL;
        $i = 0; 
        foreach (self::getJsonBridges() as $bridges) {
            if(self::bridges_pluginExists($bridges) == TRUE){
                $mergeArray = self::bridges_getPlugsElements($bridges);
                if(is_array($mergeArray)){
                    $i++;
                    $array = scan_ip_tools::arrayCompose($array, $mergeArray);
                }
            }
        }
        if(!is_array($array) AND $array != NULL){
            return FALSE;
        } else {
            $return["array"] = $array;
            $return["nb"] = $i;
            return $return;
        }
    }
    
    public static function bridges_majElement($_ip, $_element){
        $plug = explode("|", $_element);
        self::bridges_require($plug[0]);
        $class = "scan_ip_".$plug[0];
        $Maj = new $class;
        
        $return = array();
        $return["ip"] = $_ip;
        $return["id"] = $plug[1];

        if(!empty($plug[2])){
            $return["champ"] = $plug[2];
        } else {
            $return["champ"] = NULL;
        }

        return $Maj->majIpElement($return);
    }
    
    public static function bridges_printSelectOptionEquiements(){
        
        $allBridges = self::bridges_getElements(); 
        
        if($allBridges != FALSE){
            $print = $oldEquip = "";
            foreach ($allBridges["array"] as $equipement) {
                if(!empty($equipement["id"])){ 
                    $print .= "<option value=\"". $equipement["plugin"] ."|".$equipement["id"];
                    if(!empty($equipement["champ"])){ $print .= "|".$equipement["champ"]; }
                    $print .= "\">[ " . $equipement["plugin_print"] . " ][ ". $equipement["ip_v4"] ." ] " . $equipement["name"] . "</option>";
                    $oldEquip = $equipement["plugin"];
                }
            }
            return $print;
        } else {
            return FALSE;
        }
        
    }
    
    public static function bridges_printOptionEquiements(){
        
        $selection = self::bridges_printSelectOptionEquiements();
        
        if($selection != FALSE){
            for ($index = 1; $index <= self::$_defaut_bridges_by_equipement; $index++) {
                echo '<div class="form-group">';
                echo '<label class="col-sm-3 control-label">{{Association '.$index.'}}</label>';
                echo '<div class="col-sm-5">';
                echo '<select class="form-control eqLogicAttr plug_element_plugin plug_element_plugin_'.$index.'" onchange="verifEquipement('. self::$_defaut_bridges_by_equipement .');" data-l1key="configuration" data-l2key="plug_element_plugin_'.$index.'">';
                echo '<option value="">Sélectionnez un élément</option>';
                echo $selection;
                echo '</select>';
                echo '</div>';
                echo '</div>';
            }
        } else {
                echo '<div class="form-group">';
                echo '<label class="col-sm-3 control-label">{{Association 1}}</label>';
                echo '<div class="col-sm-5">';
                echo '<input class="form-control" style="color:var(--al-warning-color) !important;" type="text" value="Aucun élément compatible n\'est associé aux bridges." readonly="">';
                echo '</div>';
                echo '</div>';
        }
        
    }
                        
    public static function bridges_getEquiementsById(){
        log::add('scan_ip', 'debug', 'bridges_getEquiementsById :. Lancement'); 
        $all = self::bridges_getElements();
        foreach ($all["array"] as $equipement) { 
            $return[$equipement["id"]]["name"] = $equipement["name"];
            $return[$equipement["id"]]["ip_v4"] = $equipement["ip_v4"];
            $return[$equipement["id"]]["plugin"] = $equipement["plugin"];
        }  
        return $return;
    }
    
    public static function bridges_existId($_id){
        $bridgeExist = TRUE;
        try {
            $return = eqLogic::byId($_id);
        } catch (Exception $e) {
            $bridgeExist = FALSE;
        }
        return $bridgeExist;
    }
    
    public static function bridges_getAllAssignEquipement($_ouput = NULL){ 
        log::add('scan_ip', 'debug', 'bridges_getAllAssignEquipement :. Lancement');
        $eqLogics = eqLogic::byType('scan_ip');
        
        $return = NULL;
        
        foreach ($eqLogics as $scan_ip) { 
            $macId = $scan_ip->getConfiguration("mac_id");
            for($i = 1; $i <= self::$_defaut_bridges_by_equipement; $i++){ 
                $plug = $scan_ip->getConfiguration("plug_element_plugin_".$i); 
                if(!empty($plug)) {
                    $plugTest = explode("|", $plug); //var_dump($plugTest);
                    if(eqLogic::byId($plugTest[1]) != FALSE OR $plugTest[0] == "core"){ 
                        $return[$macId][] = $plug;
                    } else {
                        $scan_ip->setConfiguration("plug_element_plugin_".$i, NULL);
                        $scan_ip->save();
                    }
                } 
            }  
        }
        
        if($_ouput == "json" and !empty($return)){
            return json_encode($return);
        } else {
            return $return;
        }
    }
    
    public static function bridges_pluginExists($_name) {
        //log::add('scan_ip', 'debug', 'bridges_pluginExists :. Lancement');
        $bridgeExists = TRUE;
        if($_name !== 'core') {
            try {
                $plugin = plugin::byId($_name);
            } catch (Exception $e) {
                $bridgeExists = FALSE;
            }
        }
        return $bridgeExists;
    }
    
    public static function bridges_startDeamons($_deamons = NULL){
        if($_deamons != NULL){
            foreach ($_deamons as $deamon) {
                log::add('scan_ip', 'debug', 'bridges_startDeamons :. Lancement du deamon "'.$deamon.'"');
                $deamon::deamon_start();
            }
        }
    }
    
    public static function majElementsAssocies($_eqlogic, $_device){
        log::add('scan_ip', 'debug', 'majElementsAssocies :. Lancement');
        $deamons = NULL;
        
        $bridges = self::bridges_getElements();
        
        if($bridges != FALSE){ 
            for ($index = 1; $index <= self::$_defaut_bridges_by_equipement; $index++) {
                
                $plug_element_plugin = $_eqlogic->getConfiguration("plug_element_plugin_".$index);
                
                if($plug_element_plugin != ""){
                    
                    $testBridge = explode("|", $plug_element_plugin);
                    
                    if(self::bridges_pluginExists($testBridge[0])){
                        if(self::bridges_existId($testBridge[1]) == TRUE){
                            if($_device["ip_v4"] != "" AND $plug_element_plugin != ""){ 
                                $add_deamon = self::bridges_majElement($_device["ip_v4"], $plug_element_plugin);
                                $deamons = scan_ip_tools::arrayCompose($deamons, $add_deamon);
                            }
                        } else {
                            $_eqlogic->setConfiguration("plug_element_plugin_".$index, "");
                            $_eqlogic->save();
                        }
                    } else {
                        log::add('scan_ip', 'debug', 'majElementsAssocies :. Suppression du bridge car le plugin "'.$testBridge[0].'" n\'est pas installé');
                        $_eqlogic->setConfiguration("plug_element_plugin_".$index, "");
                        $_eqlogic->save();
                    } 
                }
                
            }  
            
            self::bridges_startDeamons($deamons);
        }
    }
    
}
