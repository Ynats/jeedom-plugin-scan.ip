<style>
    .scanTd{
        padding : 3px 0 !important;
    }
    .scanHender{
        cursor: pointer !important;
        width: 100%;
    }
</style>
<div>
    <div class="col-md-12" id="show_scan_ip_add" style="padding: 15px !important; display:none;">
        <div class="form-group">
                    <a class="btn btn-success pull-right" id="add_scan_ip_equipement"><i class="fas fa-check-circle"></i> Ajouter les équipements sélectionnés</a>
        </div>
    </div>
    
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;" id="scan_ip_no_equipement">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th style="text-align: center; width:30px;"></th>
                        <th data-sort="int" style="text-align: center; width:30px;"><span class="scanHender"><b class="caret"></b></span></th>
                        <th data-sort="string" style="width:375px;;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Nom}}</span></th>
                        <th data-sort="string" style="width:375px;"><span class="scanHender"><b class="caret"></b> {{Commentaire}}</span></th>
                        <th data-sort="string" style="width:130px;" class="scanTd"><span class="scanHender"><b class="caret"></b> {{Adresse MAC}}</span></th>
                        <th data-sort="int" style="width:110px;"><span class="scanHender"><b class="caret"></b> {{ip}}</span></th>
                        <th data-sort="string" class="scanTd" style="width:170px;"><span class="scanHender"><b class="caret"></b> {{Date de mise à jour}}</span></th>
                    </tr>
                </thead>
                <tbody>
<?php
                    $list = 1;
                    foreach (scan_ip::showNoEquipements() as $equipement) {
                        echo '<tr>'
                            . '<td style="text-align:center;"><input type="checkbox" onclick="is_checked_scan_ip()" id="add_input_' . $list . '" data-mac="' . $equipement["mac"] . '" style="border: 1px solid var(--link-color) !important; margin-bottom: 5px;" class="form-control add_element_scan_ip"></td>'
                            . '<td style="text-align:center;">' . $list++ . '</td>'
                            . '<td class="scanTd">' . $equipement["name"] . '</td>'
                            . '<td class="scanTd""><span style="display:none;">' . scan_ip::getCleanForSortTable($equipement["comment"]) . '</span>' . $equipement["comment"] . '</td>'
                            . '<td class="scanTd">' . $equipement["mac"] . '</td>'
                            . '<td class="scanTd"><span style="display:none;">' . scan_ip::getCleanForSortTable($equipement["ip_v4"]) . '</span>' . $equipement["ip_v4"] . '</td>'
                            . '<td class="scanTd">' . date("d/m/Y H:i:s", $equipement["time"]) . '</td>'
                            . '</tr>';
                    }
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
      
    $("#add_scan_ip_equipement").click(function() {
        addEquipement(<?php echo $list ?>);
    });

</script>  
