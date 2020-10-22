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
$list = 0;

?>

<style>
    .scanTd{
        padding : 3px 0 !important;
    }
   
</style>

<div class="col-md-12">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Etat de tous les équipements enregistrés</h3>
        </div>
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th style="text-align: center; width:20px;">#</th>
                        <th style="width:40px;"></th>
                        <th style="width:250px;" class="scanTd">{{Nom}}</th>
                        <th style="width:200px;" class="scanTd">{{Adresse MAC}}</th>
                        <th style="width:150px;">{{ip}}</th>
                        <th style="width:150px;">{{Dernière ip}}</th>
                        <th style="width:200px;">{{Mis à jour}}</th>
                        <th>{{Statut}}</th>
                    </tr>
                </thead>
                <tbody>
<?php
                    foreach ($allEquipements as $equipement) {
                        
                        if($equipement["ip_v4"] == ""){ 
                            $color = "red"; 
                            $equipement["ip_v4"] = "..."; 
                            $statut = "Hors ligne"; 
                            $statutColor = "color:red"; 
                        } else { 
                            $color = "#50aa50"; 
                            $statut = "En ligne";
                            $statutColor = "color:#50aa50";
                        }
                        
                        if($equipement["ip_v4"] != $equipement["last_ip_v4"] AND $equipement["ip_v4"] != "..."){
                            $style_last = "color: orange";
                            $statut = "Changement d'Ip";
                            $statutColor = "color:orange";
                        } else { $style_last = ""; }
                        
                        echo '<tr>'
                            . '<td style="text-align:center;" class="">' . $list++ . '</td>'
                            . '<td style="padding-left:10px;">' . scan_ip::printCycle("15px", $color) . '</td>'
                            . '<td class="scanTd">' . $equipement["name"] . '</td>'
                            . '<td class="scanTd">' . $equipement["mac"] . '</td>'
                            . '<td class="scanTd">' . $equipement["ip_v4"] . '</td>'
                            . '<td class="scanTd" style="'.$style_last.'">' . $equipement["last_ip_v4"] . '</td>'
                            . '<td class="scanTd">' . $equipement["update_date"] . '</td>'
                            . '<td class="scanTd" style="'.$statutColor.'">' . $statut . '</td>'
                            . '</tr>';
                    }
?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php include_file('core', 'plugin.template', 'js'); ?>