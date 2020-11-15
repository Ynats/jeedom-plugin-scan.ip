
//$(document).ready(function ($) {
//    $("#scan_ip_network").stupidtable();
//});

function btSaveCommentaires(nb)
        {

                var commentaires = [];
        for (var i = 1; i <= nb; i++) {
            var val = $("#input_" + i).val();
            if (val) {
                var mac = $("#input_" + i).attr('data-mac');
                commentaires.push([{mac: mac}, {val: val}]);
        }
    }

    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
        data: {
            action: "recordCommentaires",
            data: commentaires,
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
            reloadModal("network");
        }
    });
}