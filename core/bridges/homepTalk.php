<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_googlecast {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    */
    public static $plug = "homepTalk";
    public static $ip = "ip";
    
    /**
    * getAllElements sert à récupérer les infos des éléments liés au plugin
    *
    * @return array 
    * -> $return[idEquipement]["plugin"] = l'id du Plugin
    * -> $return[idEquipement]["plugin_print"] = Comment afficher l'id du plugin (ex. pour préciser le sous élément d'un plugin)
    * -> $return[idEquipement]["name"] = Nom de l'équipement
    * -> $return[idEquipement]["id"] = Id de l'équipement
    * -> $return[idEquipement]["ip_v4"] = l'ip enregistré au format v4
    * -> $return[idEquipement]["device"] = précise si il s'agit d'un élément device pour éviter les conflits (il ne peut pas y avoir plusieurs device pour une même adresse MAC
    */
    public function getAllElements(){

        $eqLogics = eqLogic::byType(self::$plug); 
        
        foreach ($eqLogics as $eqLogic) { 
            if ($eqLogic->getConfiguration('type') == 'Group') continue;
            
            $thisID=$eqLogic->getId();
            $return[$thisID]["plugin"] = self::$plug;
            $return[$thisID]["plugin_print"] = self::$plug . " :: " . $eqLogic->getConfiguration('type');
            $return[$thisID]["name"] = $eqLogic->getName();
            $return[$thisID]["id"] = $thisID;
            $return[$thisID]["ip_v4"] = $eqLogic->getConfiguration(self::$ip);

        }
        return $return;
    }
    
    /**
    * majIpElement sert à mettre à jour l'ip de l'élément si celui-ci est différent
    *
    * @param $_ip ip de l'adresse MAC à mettre à jour si différent
    * @param $_id identifiant de l'équipement associé au plugin
    * 
    */
    public function majIpElement($_ip ,$_id){
        
        $eqLogics = eqLogic::byType(self::$plug); 

        $replace=[];
        foreach ($eqLogics as $eqLogic) {
            $hasChanged=false;
            
            if ($eqLogic->getConfiguration('type') != 'Group') {
                if ($eqLogic->getId() != $_id) continue; 
                $thisIP=$eqLogic->getConfiguration(self::$ip);
                if ($thisIP == $_ip) continue;
                
                $replace[$thisIP]=$_ip;
                $eqLogic->setConfiguration(self::$ip, $_ip);
                $hasChanged=true;
            }
            if($hasChanged) $eqLogic->save(true);
        }
        
        foreach ($eqLogics as $eqLogic) {
            $hasChanged=false;
            
            if ($eqLogic->getConfiguration('type') == 'Group') {
                $newMembers=[];
                foreach ($eqLogic->getConfiguration('members') as $memb=>$act) {
                    $cp=explode(':',$memb);
                    if(array_key_exists($cp[0],$replace)) {
                        $newMembers[$replace[$cp[0]].':'.$cp[1]]=$act;
                        $hasChanged=true;
                    } else {
                        $newMembers[$memb]=$act;
                    }
                }
                $eqLogic->setConfiguration('members', $newMembers);
            }
            if($hasChanged) $eqLogic->save(true);
        }
        
    }
    
}
