<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

require_once dirname(__FILE__) . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

?>

<style>
    .scanTd{
        padding : 3px 0 !important;
    }
    .scanHender{
        cursor: pointer !important;
        width: 100%;
    }
</style>

<div class="col-md-12" style="padding-left: 0px !important; padding-right: 0px !important;">
    <div class="panel panel-primary" id="div_scan_ip_equipement_yes">
        <div class="panel-body">
            <table class="table-bordered table-condensed" style="width: 100%; margin: -5px -5px 10px 5px;" id="scan_ip_equipement">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th data-sort="int" style="width:40px; text-align: center;"><span class="scanHender"><b class="caret"></b></th>
                        <th data-sort="string" style="width:250px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Nom}}</span></th>
                        <th data-sort="string" style="width:150px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Adresse MAC}}</span></th>
                        <th data-sort="int" style="width:125px;"><span class="scanHender"><b class="caret"></b> {{ip}}</span></th>
                        <th data-sort="int" style="width:125px;"><span class="scanHender"><b class="caret"></b> {{Dernière ip}}</span></th>
                        <th data-sort="string" style="width:170px;"><span class="scanHender"><b class="caret"></b> {{Mis à jour}}</span></th>
                        <th data-sort="string">{{Elément plugin associé}}</th>
                    </tr>
                </thead>
                <tbody>
<?php
                    $allEquipements = scan_ip_eqLogic::showEquipements();
                    
                    if(!empty($allEquipements)){
                        
                        foreach ($allEquipements as $equipement) {

                            if($equipement["on_line"] == 0 AND $equipement["ip_v4"] == ""){ 
                                $color = "red"; 
                                $equipement["ip_v4"] = "..."; 
                                $statut = "Hors ligne"; 
                                $statutColor = "color:red"; 
                                $sortOnLine = 0;
                                $sortip_v4 = 0;
                            } else { 
                                $color = "#50aa50"; 
                                $statut = "En ligne";
                                $statutColor = "color:#50aa50";
                                $sortOnLine = 1;
                                $sortip_v4 = scan_ip_tools::getCleanForSortTable($equipement["ip_v4"]);
                            }

                            if(empty($equipement["last_ip_v4"])){ 
                                $equipement["last_ip_v4"] = "..."; 
                                $sortlast_ip_v4 = 0;
                            } else {
                                $sortlast_ip_v4 = scan_ip_tools::getCleanForSortTable($equipement["last_ip_v4"]);
                            }
                            if(empty($equipement["update_date"])){ $equipement["update_date"] = "..."; }
                            if(empty($equipement["plug_element_plugin"])){ $equipement["plug_element_plugin"] = "..."; }

                            if($equipement["ip_v4"] != $equipement["last_ip_v4"] AND $equipement["on_line"] == 1 AND $equipement["last_ip_v4"] != ""){
                                $style_last = "color: orange";
                                $statut = "Changement d'Ip";
                                $statutColor = "color:orange";
                            } else { $style_last = ""; }


                            echo '<tr>'
                                . '<td class="scanTd" style="width:40px; padding-left:14px !important;"><span style="display:none;">' . $sortOnLine . '</span>' . scan_ip_tools::getCycle("15px", $color) . '</td>'
                                . '<td class="scanTd">' . $equipement["link"] . '</td>'
                                . '<td class="scanTd">' . $equipement["mac"] . '</td>'
                                . '<td class="scanTd"><span style="display:none;">' . $sortip_v4 . '</span>' . $equipement["ip_v4"] . '</td>'
                                . '<td class="scanTd" style="'.$style_last.'"><span style="display:none;">' . $sortlast_ip_v4 . '</span>' . $equipement["last_ip_v4"] . '</td>'
                                . '<td class="scanTd"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($equipement["update_date"], "date") . '</span>' . $equipement["update_date"] . '</td>'
                                . '<td class="scanTd""><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($equipement["plug_element_plugin"]) . '</span>' . $equipement["plug_element_plugin"] . '</td>'
                                . '</tr>';

                        }
                    
                    } else {
                        echo "<script>$('#div_scan_ip_equipement_yes').hide();$('#div_alert_scan_ip').show();</script>";
                    }
?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="div_alert_scan_ip" class="jqAlert alert-warning" style="display:none;"><span class="displayError">Pour le moment, vous n'avez pas enregistré d'équipement MAC.</span></div>
</div>
  
<?php include_file('desktop', 'stupidtable.min', 'js', 'scan_ip'); ?>
<?php include_file('desktop', 'scan_ip_yes_equipements', 'js', 'scan_ip'); ?>
