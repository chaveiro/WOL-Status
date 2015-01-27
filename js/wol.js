// Use http://javascript-minifier.com/ to minify this js.

function wol(idMachine){
    var host = idMachine.split('_');
    var nId = host[0];
    var mId = host[1];
    $.ajax({
        url: 'api/wol',
        type: 'POST',
        data: {nId: nId, mId: mId},
        dataType: 'JSON',
        error: handleAjaxError,
        success: function(response) {
            if (response["success"]) {
                alertInfo("WOL " + response["mName"] + " : " + response["reason"]);
                for (i=0; i < 24; i++){  
                  setTimeout(function(){ getStatus(idMachine)}, i * 10000);
                }
            } else {
                alertError("WOL " + response["mName"] + " : " + response["reason"]);
            }
        }
    });
}

function getStatus(idMachine){
    $.ajax({
        url: 'api/status/' + idMachine.replace("_","/") ,
        type: 'GET',
        dataType: 'JSON',
        error: handleAjaxError,
        success: function(data) {
            //console.log(data);
            var htmlOut = '';
            $.each( data, function( portId, port )
            {
                if (port["success"]) {
                    htmlOut += '<span class="label label-success">' + port["port"] + '</span> ';
                } else {
                    if(port["errorcode"] == 110 ){ // timeout
                        htmlOut += '<span class="label label-default">' + port["port"] + '</span> ';
                    } else {
                        htmlOut += '<span class="label label-danger">' + port["port"] + '</span> ';
                    }
                }
            });
            $("#status_" + idMachine).html(htmlOut);
        }
    });
}

function refreshStatus (){
    for (i=0; i < statusConfig.length; i++){ getStatus(statusConfig[i]); } 
}

var statusConfig = [];

function getConfig() {
    $.ajax({
        url: 'api/config',
        type: 'GET',
        dataType: 'JSON',
        error: handleAjaxError,
        success: function(data) {
            processConfig(data);
            refreshStatus();
			$('#feedback').html("&nbsp;");
        }
    });
}

function processConfig(networks){
    var htmlOut = '';
    statusConfig = [];
    //foreach($networks as $networkId => $network){
    $.each(networks, function( networkId, network ) {
        htmlOut += '<div class="list-group">';
        htmlOut += '<a href="#" class="list-group-item active">' + network["name"] + '</a>';
        //foreach($network->machines as $machineId => $machine){
        $.each( network["machines"], function( machineId, machine )
        {
            statusConfig.push(networkId + "_" + machineId );
            htmlOut += '<a class="list-group-item" href="#">';
                    htmlOut += '<div class="row">';
                        htmlOut += '<div class="col-xs-10">';
                            htmlOut += '<div class="row">';
                                htmlOut += '<div class="col-xs-4">' + machine["name"] + '</div>';
                                htmlOut += '<div id="status_' + networkId + "_" + machineId + '" class="col-xs-8">';
                                    //foreach($machine->servicePorts as $portName => $portVal)
                                    $.each( machine["servicePorts"], function( portId, portName )
                                    {
                                        htmlOut += '<span class="label label-warning">' + portName + '</span> ';
                                    });
                                htmlOut += '</div>';
                            htmlOut += '</div>';
                        htmlOut += '</div>';
                        if (machine["hasWol"]) {
                        htmlOut += '<div class="col-xs-2">';
                            htmlOut += '<button id="actionWOL_' + networkId + "_" + machineId + '" type="button" class="btn btn-default pull-right">';
                                htmlOut += '  <span class="glyphicon glyphicon-off pull-right" aria-hidden="true"></span>';
                            htmlOut += '</button>';
                        htmlOut += '</div>';
                        } 
                    htmlOut += '</div>';
            htmlOut += '</a>';
        });
        htmlOut += "</div>";
    });
    $("#mainBody").html(htmlOut);
    
    $("button[id^='actionWOL_']").on("click", function() {
        wol(this.id.replace("actionWOL_",""));
    });
}

function alertInfo(message){
    $('#feedback').html("<div class='alert alert-info' role='alert'><span class='glyphicon glyphicon glyphicon-info-sign'></span> " + message + "</div>");
    setTimeout(function(){ $('#feedback').html("&nbsp;")}, 5000);
}

function alertError(message){
    $('#feedback').html("<div class='alert alert-danger' role='alert'><span class='glyphicon glyphicon-exclamation-sign'></span> " + message + "</div>");
    setTimeout(function(){ $('#feedback').html("&nbsp;")}, 10000); 
}   

function handleAjaxError(err) {
    if (err.status == 0) {
        alertError("No internet connection");
    }else if (err.status != 200) { 
        alertError(err.status + " " + err.statusText);
    } else {
        alertError("AJAX unknown error");
        //alertError(err.responseText);
    }       
}

$("body").ready(function _onbodyready() {
    getConfig();
	setInterval(function(){ refreshStatus() }, 30000); //Refresh all every 30secs

    var $scrollPercentage = 0;
    $("body").pullToRefresh()
    .on("end.pulltorefresh", function (e){
        if ($scrollPercentage > 25) {
            $('#feedback').html("<div class='alert alert-info' role='alert'><center><span class='glyphicon glyphicon-refresh'></span></center></div>");
            getConfig();
        }
        else { $('#feedback').html("&nbsp"); }
        $scrollPercentage = 0;
    })
    
    .on("move.pulltorefresh", function (e, percent){
        var htmlOut = '';
        htmlOut += "<center>";
        if (percent > 25) {
            htmlOut += "<div class='alert alert-info' role='alert'><span class='glyphicon glyphicon-refresh'></span> release to refresh</div>";
        } else {
            htmlOut += "<div class='alert alert-info' role='alert'><span class='glyphicon glyphicon-hand-down'></span> keep scrolling down to refresh</div>";
        }
        htmlOut += "</center>";
        $('#feedback').html(htmlOut);
        $scrollPercentage = percent;
    })
});
