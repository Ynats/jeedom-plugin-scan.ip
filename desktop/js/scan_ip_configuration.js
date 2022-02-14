
getDataConfig();

function getDataConfig() {
    $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.ajax.conf.php", function (result) {
        $("#dataConfig").html("");
        $.each(result, function (key, value) {
            $("#dataConfig").append("<div id='"+key+"' data-config='"+value+"'></div>");
        });
    });
}

function showVersion(){
    var giveVersionByJeedom = $("#span_plugin_install_date").html();
    var versionPlugin = $("#version_plugin").attr("data-config");
    $("#span_plugin_install_date").html("");
    $("#span_plugin_install_date").append( "v" + versionPlugin + " ("+giveVersionByJeedom+")");
}

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
        $('#show_debug').hide();
        $('#show_sous_reseau').hide();
        $('#show_avance').hide();
    } 
    else if(mode == "advanced"){
        $('#show_debug').hide();
        $('#show_sous_reseau').show();
        $('#show_avance').show();
    }
    else if(mode == "debug"){
        $('#show_debug').show();
        $('#show_sous_reseau').show();
        $('#show_avance').show();
    }
}

setTimeout(function(){
    showVersion();
    scan_ip_mode_plugin();
}, 150);

$("#reloadMajPlugin").click(function() {
    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.maj.php",
        data: {
            action: "reloadMaj",
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
