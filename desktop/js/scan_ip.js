
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

// Sur la partie NetWork
$('#bt_scanIpNetwork').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Afficher le Réseau}}"});
  $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=network').dialog('open');
});

// Sur la partie Equipement
$('#bt_scanIpEquipement').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Afficher les équipements}}"});
  $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=equipement').dialog('open');
});

// Sur la partie Debug
$('#bt_scanIpDebug').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Debug}}"});
  $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=debug').dialog('open');
});

// Synchro
$('#bt_syncEqLogic').off('click').on('click', function () {
  syncEqLogicWithOpenScanId();
});

// Au changement du menu de sélection on reproduit la valeur dans le champ
$('#scan_ip_mac_select').change(function(){
    var scan_ip_CopyPaste = $('#scan_ip_mac_select').find(":selected").val();
    $("#scan_ip_adressMacTemp").val(scan_ip_CopyPaste);
});

////////

function syncEqLogicWithOpenScanId() {
    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
        data: {
            action: "syncEqLogicWithOpenScanId",
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

function verif_nombre(champ)
{
    var chiffres = new RegExp("[0-9]");
    var verif;
    var points = 0;

    for (x = 0; x < champ.value.length; x++)
    {
        verif = chiffres.test(champ.value.charAt(x));
        if (champ.value.charAt(x) == ".") {
            points++;
        }
        if (points > 1) {
            verif = false;
            points = 1;
        }
        if (verif == false) {
            champ.value = champ.value.substr(0, x) + champ.value.substr(x + 1, champ.value.length - x + 1);
            x--;
        }
    }
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<div class="row">';
    tr += '<div class="col-sm-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

function verifEquipement(nb){
    var cpt = 0;
    var nbElement = [];
    
    for (x = 1; x <= nb; x++) {
        var val = $('#plug_element_plugin_' + x).find(":selected").val();
        var split = val.split("|");
        if(split[0] != "") { nbElement.push(split[0]); }
    }
    
    red = nbElement.reduce((p,c) => (p[c]++ || (p[c]=1),p),{});
    
    $.each(red, function( index, value ) {
        if(value > 1){ 
            $('#div_alert').showAlert({message: "{{Attention cet équipement est associé "+value+" fois au plugin "+index+" ! Il est fort probable que cela génère un conflit dans le plugin "+index+".}}", level: 'danger'}); 
        } 
        else {
             $('#div_alert').hide();
        }
    });
    
}

function hideSelect(NbSelect){
    $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.associations.php", function(result){

        for (let plug = 0; plug <= NbSelect; plug++) {
            $.each( result, function( mac, value ) {
                var current = $('#scan_ip_adressMacTemp').val();
                if(mac != current){
                    $("#plug_element_plugin_"+plug+" option[value='" + value + "']").hide();
                }
            });  
        }

    });
}

function timeCron(){
    $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.time_cron.php", function(result){
        $("#cronPass").attr('data-cron', result); 
        $("#cronPass").val("La cadence de rafraichissment se fait toutes les " +result + " minutes");
        verifCadence();
    });
}


function verifCadence(){
    var offline_time = $("#offline_time").val();
    var cron_pass = $("#cronPass").attr('data-cron');
    var delta = offline_time / cron_pass;
    
    if(delta < 2){ 
        $('#div_alert_config').showAlert({message: "{{Si vous valider cette configuration, il est possible que certains de vos équipements soient indiqués comme hors-ligne alors qu'ils ne le sont pas.}}", level: 'warning'});
    } 
    else {
        $('#div_alert_config').hide();
    }
}

$('#offline_time').change(function(){
    verifCadence();
});
    
