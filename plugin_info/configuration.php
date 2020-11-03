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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
// Brigde affachés par paquet de ...
$paquetBridges = ceil(count(scan_ip::$_allBridges)/3);

scan_ip::cleanAfterUpdate();

?>
<form class="form-horizontal">
    <fieldset>
        
<?php
        scan_ip::vueSubTitle("{{Base de données OUI}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Fichier présent}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Ce fichier sert à récupérer le nom des constructeurs de matériel}}"></i></sup>
            </label>
            <div class="col-lg-2"><?php echo scan_ip::printFileOuiExist() ?> <sup><i class="fa fa-question-circle tooltips" title="{{Mise à jour le}} <?php echo scan_ip::getDateFile(scan_ip::$_file_oui) ?>"></i></sup>
            </div>
        </div>
        
<?php
        scan_ip::vueSubTitle("{{Cadence de rafraichissement}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Cadence de rafraichissement}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Il est recommendé de laisser ce paramètre à }} <?php echo scan_ip::$_defaut_cron_pass ?> {{minute}}"></i></sup>
            </label>
            <div class="col-lg-2">
                <select class="configKey form-control" id="cron_pass" data-l1key="cron_pass">
                    <option value="1">{{1 minute (recommandé)}}</option>
                    <option value="2">{{2 minutes}}</option>
                    <option value="3">{{3 minutes}}</option>
                </select> 
            </div>
        </div>
        
    <?php
        scan_ip::vueSubTitle("{{Plage(s) à scanner}}", "config");
        
        echo scan_ip::printInputSubConfig(); 
    
        scan_ip::vueSubTitle("{{Bridges : Plugins compatibles}}", "config");
    ?> 
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Liste des Plugins pris en compte}}</label>
            <div class="col-lg-2">
                <?php echo scan_ip::bridges_printPlugs($paquetBridges, 0); ?>
            </div>
            <div class="col-lg-2">
                <?php echo scan_ip::bridges_printPlugs($paquetBridges, $paquetBridges); ?>
            </div>
            <div class="col-lg-2">
                <?php echo scan_ip::bridges_printPlugs($paquetBridges, ($paquetBridges*2)); ?>
            </div>
        </div>
    </fieldset>
    <br />
</form>

<script>

function recordBtMac() {
    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
        data: {
            action: "recordMacBouton",
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
}

$('#cron_pass').change(function(){
    verifCadence('change');
});

</script>
