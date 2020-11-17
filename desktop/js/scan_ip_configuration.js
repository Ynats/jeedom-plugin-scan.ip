
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

function scan_ip_mode_plugin() { 
    var mode = $( "#scan_ip_mode" ).val();
    if(mode == "normal"){
        $('#show_oui').hide();
        $('#show_sous_reseau').hide();
        $('#show_avance').hide();
    } 
    else if(mode == "advanced"){
        $('#show_oui').hide();
        $('#show_sous_reseau').show();
        $('#show_avance').show();
    }
    else if(mode == "debug"){
        $('#show_oui').show();
        $('#show_sous_reseau').show();
        $('#show_avance').show();
    }
}

setTimeout(function(){
    scan_ip_mode_plugin();
}, 150);