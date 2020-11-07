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
$arrayCommentMac = scan_ip::getJson(scan_ip::$_jsonCommentairesEquipement);

foreach ($arrayCommentMac as $tempCommentMac) {
    $commentMac[$tempCommentMac[0]["mac"]] = $tempCommentMac[1]["val"];
}

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

<div class="col-md-6">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Votre Jeedom</h3>
        </div>
        <div class="panel-body">
        <?php if($ipsReseau["jeedom"]["name"] != "") { ?>
            <div>
                <label class="col-sm-3 control-label">Nom : </label>
                <div><?php echo $ipsReseau["jeedom"]["name"] ?></div> 
            </div>
        <?php } ?>
            <div>
                <label class="col-sm-3 control-label">ip : </label>
                <div><?php echo $ipsReseau["jeedom"]["ip_v4"] ?></div> 
            </div>
            <div>
                <label class="col-sm-3 control-label">Adresse MAC : </label>
                <div><?php echo $ipsReseau["jeedom"]["mac"]?></div>
            </div>
            <?php if(gethostbyaddr($ipsReseau["jeedom"]["ip_v4"]) != $ipsReseau["jeedom"]["ip_v4"]) { ?>            
                <div>
                    <label class="col-sm-3 control-label">Host Name : </label>
                    <div><?php echo gethostbyaddr($ipsReseau["jeedom"]["ip_v4"]) ?></div>
                </div>
            <?php } ?> 
        </div>
        <br />
    </div>
</div>

<div class="col-md-6">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Votre routeur</h3>
        </div>
        <div class="panel-body">
            <div>
                <label class="col-sm-3 control-label">ip : </label>
                <div><?php echo $ipsReseau["route"]["ip_v4"] ?></div>
            </div>
            <div>
                <label class="col-sm-3 control-label">Adresse MAC : </label>
                <div><?php echo $ipsReseau["route"]["mac"] ?></div>
            </div>
        </div>
        <br />
    </div>
</div>

<div class="col-md-12">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Les plages ip et adresses MAC du réseau (<?php echo $ipsReseau["infos"]["date"] ?>)
            <a id="btSaveCommentaires" class="btn btn-success btn-xs pull-right" style="top: -2px !important; right: -6px !important;"><i class="far fa-check-circle icon-white"></i> {{Sauvegarder les commentaires}}</a>
            </h3>
        </div>
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;" id="scan_ip_network">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th data-sort="int" class="scanTd" style="text-align: center; width:30px;"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="string" class="scanTd" style="text-align: center; width:30px;" class="scanTd"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="int" style="text-align: center; width:30px;" class="scanTd"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="string" style="width:130px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Adresse MAC}}</span></th>
                        <th data-sort="int" class="scanTd" style="width:110px;"><span class="scanHender"><b class="caret"></b> {{ip}}</span></th>
                        <th data-sort="string" class="scanTd" style="width:375px;"><span class="scanHender"><b class="caret"></b> {{Nom}}</span></th>
                        <th data-sort="string" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Commentaire}}</span></th>
                        <th data-sort="string" class="scanTd" style="width:170px;"><span class="scanHender"><b class="caret"></b> {{Date de mise à jour}}</span></th>
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
 
                        if (isset($savingMac[$device["mac"]]["name"])) {
                            $name = $savingMac[$device["mac"]]["name"];
                            $nameSort = scan_ip::getCleanForSortTable($savingMac[$device["mac"]]["name"]);
                        } else {
                            $name = "| ". $device["equipement"];
                            $nameSort = scan_ip::getCleanForSortTable($device["sort_table"]["equipement"]);
                        }

                        $ipSort = scan_ip::getCleanForSortTable($device["ip_v4"]);

                        if (scan_ip::isOffline($offline_time, $device["time"]) == 0) {
                            $colorOnLine = "#50aa50";
                            $titleOnLine = "En ligne";
                            $lineSortOnline = 1;
                        } else {
                            $colorOnLine = "red";
                            $titleOnLine = "Hors ligne";
                            $lineSortOnline = 0;
                        }
                        
                        if(!empty($commentMac[$device["mac"]])){
                            $printComment = $commentMac[$device["mac"]];
                            $printCommentSort = scan_ip::getCleanForSortTable($printComment);
                        } else {
                            $printComment = NULL;
                            $printCommentSort = "ZZZZZZZZZZZZZZZZZZZZ";
                        }

                        if (isset($savingMac[$device["mac"]]["enable"])) {
                            if ($savingMac[$device["mac"]]["enable"] == 1) {
                                $classPresent = "macPresentActif";
                                $textPresent = '<i class="fas fa-check"></i>';
                                $classSuivi = "spanScanIp EnableScanIp";
                                $titleEquipement = "Cet équipement est enregistré et activé";
                                $lineSortEquipement = 2;
                            } else {
                                $classPresent = "macPresentInactif";
                                $textPresent = '<i class="fas fa-exclamation-circle"></i>';
                                $classSuivi = "spanScanIp DisableScanIp";
                                $titleEquipement = "Cet équipement est enregistré mais désactivé";
                                $lineSortEquipement = 1;
                            }
                        } else {
                            $classPresent = "macAbsent";
                            $textPresent = '<i class="fas fa-info-circle"></i>';
                            $classSuivi = "spanScanIp NoneScanIp";
                            $title = "Cet équipement n'est pas enregistré";
                            $lineSortEquipement = 0;
                        }

                        echo '<tr>'
                        . '<td class="scanTd ' . $classPresent . '" style="text-align:center;">' . $list++ . '</td>'
                        . '<td class="scanTd" title="' . $titleOnLine .'"><span style="display:none;">' . $lineSortOnline . '</span>' . scan_ip::getCycle("15px", $colorOnLine) . '</td>'
                        . '<td class="scanTd ' . $classPresent . '" style="style="text-align:center !important;" title="' . $titleEquipement .'"><span style="display:none;">' . $lineSortEquipement . '</span><span class="' . $classSuivi . '">' . $textPresent . '</span></td>'
                        . '<td class="scanTd ' . $classPresent . '">' . $device["mac"] . '</td>'
                        . '<td class="scanTd ' . $classPresent . '"><span style="display:none;">' . $ipSort . '</span>' . $device["ip_v4"] . '</td>'
                        . '<td class="scanTd ' . $classPresent . '" style="text-overflow: ellipsis;"><span style="display:none;">' . $nameSort . '</span>' . $name . '</td>'
                        . '<td class="scanTd ' . $classPresent . '"><span style="display:none;">' . $printCommentSort . '</span><input type="text" id="input_' . $list . '" data-mac="' . $device["mac"] . '" value="'.$printComment.'" class="form-control" style="width:100%;"></td>'
                        . '<td class="scanTd ' . $classPresent . '">' . date("d/m/Y H:i:s", $device["time"]) . '</td>'
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
       $("#scan_ip_network").stupidtable();
   });
   
   $("#btSaveCommentaires").click(function() {
        var commentaires = [];
        for (var i=1; i<=<?php echo $list ?>; i++) {
            var val = $("#input_" + i).val();
            if(val){
                var mac = $("#input_" + i).attr('data-mac'); 
                commentaires.push([{mac : mac}, {val : val}]); 
            }
        }
        
        $.ajax({
            type: "POST",
            url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
            data: {
                action: "recordCommentaires",
                data : commentaires,
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) {
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                window.location.reload();
            }
        });
   });

</script>

<?php include_file('desktop', 'lib/stupidtable.min', 'js', 'scan_ip'); ?>