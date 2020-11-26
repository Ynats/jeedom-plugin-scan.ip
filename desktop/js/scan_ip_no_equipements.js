
$(document).ready(function ($) {
    $("#scan_ip_no_equipement").stupidtable();
});


function is_checked_scan_ip() {
    if ($(".add_element_scan_ip:checked").length == 0) {
        $("#show_scan_ip_add").hide();
    } else {
        $("#show_scan_ip_add").show();
    }
}

function removeEquipement(nb) {
    if(nb > 0){
    
        var equipements = [];
        for (var i = 1; i <= nb; i++) {
            if ($("#checked_input_" + i).is(':checked')) {
                var mac = $("#checked_input_" + i).attr('data-mac');
                equipements.push([{mac: mac}]);
            }
        }

        $.ajax({
            type: "POST",
            url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
            data: {
                action: "removeEquipement",
                data: equipements,
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
}

function addEquipement(nb) {
    if(nb > 0){
        var equipements = [];
        for (var i = 1; i <= nb; i++) {
            if ($("#checked_input_" + i).is(':checked')) {
                var mac = $("#checked_input_" + i).attr('data-mac');
                equipements.push([{mac: mac}]);
            }
        }

        $.ajax({
            type: "POST",
            url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
            data: {
                action: "addEquipement",
                data: equipements,
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
}

