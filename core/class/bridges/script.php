<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_script {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "script";
    
    /**
    * getAllElements sert à récupérer les infos des éléments liés au plugin
    *
    * @return array 
    * -> $return[idEquipement]["plugin"] = l'id du Plugin
    * -> $return[idEquipement]["plugin_print"] = Comment afficher l'id du plugin (ex. pour préciser le sous élément d'un plugin)
    * -> $return[idEquipement]["name"] = Nom de l'équipement
    * -> $return[idEquipement]["id"] = Id de l'équipement
    * -> $return[idEquipement]["ip_v4"] = l'ip enregistré au format v4
    */
    public function getAllElements(){
        
        $return = NULL;
        $eqLogics = eqLogic::byType(self::$plug); 
        
        foreach ($eqLogics as $eqLogic) {
            $eqName = trim($eqLogic->getName());
            foreach ($eqLogic->getCmd() as $cmd) {
                $cliRequest = $cmd->getConfiguration('request');
                $cmdId = $cmd->getId();
		if (!empty($cmd->getConfiguration('request')) AND preg_match(scan_ip_tools::getRegex("ip_v4"),$cliRequest,$match)) {
                    $return[self::$plug.$cmdId]["plugin"] = self::$plug;
                    $return[self::$plug.$cmdId]["plugin_print"] = self::$plug . " :: " . $eqName;
                    $return[self::$plug.$cmdId]["name"] = $cmd->getName();
                    $return[self::$plug.$cmdId]["id"] = $cmdId;
                    $return[self::$plug.$cmdId]["ip_v4"] = $match[0];
                }
            }
        }
        return $return;
        
    }
    
    
    /**
    * majIpElement sert à mettre à jour l'ip de l'élément si celui-ci est différent
    *
    * @param $_array["ip"] ip de l'adresse MAC à mettre à jour si différent
    * @param $_array["id"] identifiant de l'équipement associé au plugin
    * 
    */
    public function majIpElement($_array){

        $eqLogics = eqLogic::byType(self::$plug); 

        foreach ($eqLogics as $eqLogic) {
            foreach ($eqLogic->getCmd() as $cmd) {
                if ($cmd->getId() == $_array["id"]) {
                    $cliRequest = $cmd->getConfiguration('request');
                    if (!empty($cliRequest) AND preg_match(scan_ip_tools::getRegex("ip_v4"),$cliRequest,$old)) {
                        if($old[0] != $_array["ip"]) {
                            $change_ip = preg_replace(scan_ip_tools::getRegex("ip_v4"), $_array["ip"], $cliRequest);
                            $cmd->setConfiguration('request',$change_ip);
                            try {
                                $cmd->save();
                            } catch (Exception $e) {
                                 log::add('scan_ip', 'error', 'Erreur lors de la sauvegarde du script : '.$cmd->getName().' ('.$_array["id"]. ').');
                            }                             
                        }
                        return NULL;
                    }
                }
            }
        }
    }
}
