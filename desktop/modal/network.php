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

$ipsReseau = scan_ip::getJson(scan_ip::$_jsonMapping);
if (empty($ipsReseau)) {
    scan_ip::syncScanIp();
    $ipsReseau = scan_ip::getJson(scan_ip::$_jsonMapping);
}

$savingMac = scan_ip::getAlleqLogics();

$list = 1;
?>

<style>
    .scanTd{
        padding : 3px 0 3px 15px !important;
    }
    .scanHender{
        cursor: pointer !important;
        width: 100%;
    }
    .macPresentActif{
        color: green;
    }
    .macPresentInactif{
        color: #FF4500;
    }
    .macAbsent{
        color: grey;
    }
    .spanScanIp{
        display: block;
        width: 78x !important;
        padding : 2px 5px;
        color : white;
        text-align: center;
    }
    .EnableScanIp{
        color: green;
    }
    .DisableScanIp{
        color: #FF4500;
    }
    .NoneScanIp{
        color: grey;
    }
    
</style>

<div class="col-md-9">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Les plages ip et adresses MAC du réseau (<?php echo $ipsReseau["infos"]["date"] ?>)</h3>
        </div>
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;" id="scan_ip_network">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th data-sort="int" style="cursor: pointer; text-align: center;"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="int" class="scanTd"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="string" style="width:130px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Adresse MAC}}</span></th>
                        <th data-sort="int" class="scanTd"><span class="scanHender"><b class="caret"></b> {{ip}}</span></th>
                        <th data-sort="string" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Nom}}</span></th>
                        <th data-sort="string" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Date de mise à jour}}</span></th>
                    </tr>
                </thead>
                <tbody>
<?php         
                    foreach ($ipsReseau["sort"] as $device) {
 
                        if(empty($savingMac[$device["mac"]]["offline_time"])){
                            $offline_time = NULL;
                        } else {
                            $offline_time = $savingMac[$device["mac"]]["offline_time"];
                        }
                        
                        if (scan_ip::isOffline($offline_time, $device["time"]) == 0) {
                            
                            if (isset($savingMac[$device["mac"]]["name"])) {
                                $name = $savingMac[$device["mac"]]["name"];
                                $nameSort = scan_ip::getCleanForSortTable($savingMac[$device["mac"]]["name"]);
                            } else {
                                $name = "| ". $device["equipement"];
                                $nameSort = scan_ip::getCleanForSortTable($device["sort_table"]["equipement"]);
                            }
                            
                            $ipSort = scan_ip::getCleanForSortTable($device["ip_v4"]);

                            if (isset($savingMac[$device["mac"]]["enable"])) {
                                if ($savingMac[$device["mac"]]["enable"] == 1) {
                                    $classPresent = "macPresentActif";
                                    $textPresent = '<i class="fas fa-check"></i>';
                                    $classSuivi = "spanScanIp EnableScanIp";
                                    $title = "Cet équipement est enregistré et activé";
                                    $lineSort = 2;
                                } else {
                                    $classPresent = "macPresentInactif";
                                    $textPresent = '<i class="fas fa-exclamation-circle"></i>';
                                    $classSuivi = "spanScanIp DisableScanIp";
                                    $title = "Cet équipement est enregistré mais pas activé";
                                    $lineSort = 1;
                                }
                            } else {
                                $classPresent = "macAbsent";
                                $textPresent = '<i class="fas fa-info-circle"></i>';
                                $classSuivi = "spanScanIp NoneScanIp";
                                $title = "Cet équipement n'est pas enregistré";
                                $lineSort = 0;
                            }

                            echo '<tr>'
                            . '<td style="text-align:center;" class="' . $classPresent . '">' . $list++ . '</td>'
                            . '<td class="' . $classPresent . '" title="' . $title .'"><span style="display:none;">' . $lineSort . '</span><span class="' . $classSuivi . '">' . $textPresent . '</span></td>'
                            . '<td class="scanTd ' . $classPresent . '">' . $device["mac"] . '</td>'
                            . '<td class="scanTd ' . $classPresent . '"><span style="display:none;">' . $ipSort . '</span>' . $device["ip_v4"] . '</td>'
                            . '<td class="scanTd ' . $classPresent . '" style="text-overflow: ellipsis;"><span style="display:none;">' . $nameSort . '</span>' . $name . '</td>'
                            . '<td class="scanTd ' . $classPresent . '">' . date("d/m/Y H:i:s", $device["time"]) . '</td>'
                            . '</tr>';
                        }
                    }
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Votre Jeedom</h3>
        </div>
        <div class="panel-body">
        <?php if($ipsReseau["jeedom"]["name"] != "") { ?>
            <div>
                <label class="col-sm-5 control-label">Nom : </label>
                <div><?php echo $ipsReseau["jeedom"]["name"] ?></div> 
            </div>
        <?php } ?>
            <div>
                <label class="col-sm-5 control-label">ip : </label>
                <div><?php echo $ipsReseau["jeedom"]["ip_v4"] ?></div> 
            </div>
            <div>
                <label class="col-sm-5 control-label">Adresse MAC : </label>
                <div><?php echo $ipsReseau["jeedom"]["mac"]?></div>
            </div>
            <?php if(gethostbyaddr($ipsReseau["jeedom"]["ip_v4"]) != $ipsReseau["jeedom"]["ip_v4"]) { ?>            
                <div>
                    <label class="col-sm-5 control-label">Host Name : </label>
                    <div><?php echo gethostbyaddr($ipsReseau["jeedom"]["ip_v4"]) ?></div>
                </div>
            <?php } ?> 
        </div>
        <br />
    </div>
</div>

<div class="col-md-3">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Votre routeur</h3>
        </div>
        <div class="panel-body">
            <div>
                <label class="col-sm-5 control-label">ip : </label>
                <div><?php echo $ipsReseau["route"]["ip_v4"] ?></div>
            </div>
            <div>
                <label class="col-sm-5 control-label">Adresse MAC : </label>
                <div><?php echo $ipsReseau["route"]["mac"] ?></div>
            </div>
        </div>
        <br />
    </div>
</div>

 <script>
   $(document).ready(function($) { 
    $("#scan_ip_network").stupidtable();
   }); 
  </script>  

<?php include_file('desktop', 'lib/stupidtable.min', 'js', 'scan_ip'); ?>