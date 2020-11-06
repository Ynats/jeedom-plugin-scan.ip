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

$allEquipements = scan_ip::showEquipements();

$list = 1;

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

<div class="col-md-12">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Etat de tous les équipements enregistrés</h3>
        </div>
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;" id="scan_ip_equipement">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th data-sort="int" style="text-align: center; width:30px;"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="string" style="width:40px;"></th>
                        <th data-sort="string" style="width:250px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Nom}}</span></th>
                        <th data-sort="string" style="width:150px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Adresse MAC}}</span></th>
                        <th data-sort="int" style="width:125px;"><span class="scanHender"><b class="caret"></b> {{ip}}</span></th>
                        <th data-sort="int" style="width:125px;"><span class="scanHender"><b class="caret"></b> {{Dernière ip}}</span></th>
                        <th data-sort="string" style="width:170px;"><span class="scanHender"><b class="caret"></b> {{Mis à jour}}</span></th>
                        <th data-sort="string" style="width:150px;"><span class="scanHender"><b class="caret"></b> {{Statut}}</span></th>
                        <th>{{Elément plugin associé}}</th>
                    </tr>
                </thead>
                <tbody>
<?php
                    foreach ($allEquipements as $equipement) {
           
                        if($equipement["on_line"] == 0 AND $equipement["ip_v4"] == ""){ 
                            $color = "red"; 
                            $equipement["ip_v4"] = "..."; 
                            $statut = "Hors ligne"; 
                            $statutColor = "color:red"; 
                        } else { 
                            $color = "#50aa50"; 
                            $statut = "En ligne";
                            $statutColor = "color:#50aa50";
                        }
                        
                        if($equipement["ip_v4"] != $equipement["last_ip_v4"] AND $equipement["on_line"] == 1 AND $equipement["last_ip_v4"] != ""){
                            $style_last = "color: orange";
                            $statut = "Changement d'Ip";
                            $statutColor = "color:orange";
                        } else { $style_last = ""; }
                        
                        $ipSort = scan_ip::getCleanForSortTable($equipement["ip_v4"]);
                        $last_ipSort = scan_ip::getCleanForSortTable($equipement["last_ip_v4"]);
                        $statutSort = scan_ip::getCleanForSortTable($statut);
                        $plug_element_pluginSort = scan_ip::getCleanForSortTable($equipement["plug_element_plugin"]);
                        
                        echo '<tr>'
                            . '<td style="text-align:center;" class="">' . $list++ . '</td>'
                            . '<td style="padding-left:10px;">' . scan_ip::getCycle("15px", $color) . '</td>'
                            . '<td class="scanTd">' . $equipement["link"] . '</td>'
                            . '<td class="scanTd">' . $equipement["mac"] . '</td>'
                            . '<td class="scanTd"><span style="display:none;">' . $ipSort . '</span>' . $equipement["ip_v4"] . '</td>'
                            . '<td class="scanTd" style="'.$style_last.'"><span style="display:none;">' . $last_ipSort . '</span>' . $equipement["last_ip_v4"] . '</td>'
                            . '<td class="scanTd">' . $equipement["update_date"] . '</td>'
                            . '<td class="scanTd" style="'.$statutColor.'"><span style="display:none;">' . $statutSort . '</span>' . $statut . '</td>'
                            . '<td class="scanTd""><span style="display:none;">' . $plug_element_pluginSort . '</span>' . $equipement["plug_element_plugin"] . '</td>'
                            . '</tr>';
                        
                    }
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function($) { 
     $("#scan_ip_equipement").stupidtable();
    }); 
</script>  

<?php include_file('desktop', 'lib/stupidtable.min', 'js', 'scan_ip'); ?>