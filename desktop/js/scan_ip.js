
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
$('#bt_scanIpEquipementYes').off('click').on('click', function () {
    $('#md_modal').dialog({title: "{{Vos équipements enregistrés}}"});
    $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=yes_equipement').dialog('open');
});

$('#bt_scanIpEquipementNo').off('click').on('click', function () {
    $('#md_modal').dialog({title: "{{Vos équipements non enregistrés}}"});
    $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=no_equipement').dialog('open');
});

// Sur la partie Debug
$('#bt_scanIpDebug').off('click').on('click', function () {
    $('#md_modal').dialog({title: "{{Debug}}"});
    $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=debug').dialog('open');
});

// Sur la partie Développeur
$('#bt_scan_ip_dev').off('click').on('click', function () {
    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.dev.php",
        data: {
            action: "reset",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            $('#div_alert').showAlert({message: "Mode développeur : Reset Ok (Penser à réactiver le CRON)", level: 'warning'});
        }
    });
});

function reloadModal(idModal) {
    $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=' + idModal).dialog('close');
    setTimeout(function () {
        $('#md_modal').load('index.php?v=d&plugin=scan_ip&modal=' + idModal).dialog('open');
    }, 200);
}
 