
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

$('#bt_syncEqLogic').off('click').on('click', function () {
  syncEqLogicWithOpenScanId();
});

// Au changement du menu de sélection on reproduit la valeur dans le champ
$('#scan_ip_mac_select').change(function(){
    var scan_ip_CopyPaste = $('#scan_ip_mac_select').find(":selected").val();
    $("#scan_ip_adressMacTemp").val(scan_ip_CopyPaste);
    $("#scan_ip_adressMac").val(scan_ip_CopyPaste);
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

function recordBtMac(input_mac) {
    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
        data: {
            action: "recordMacBouton",
            mac: input_mac,
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
