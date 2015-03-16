var DKApp = {
    request: function(method, params, successCallback){
        $.ajax({
            url: 'service.php', 
            data: JSON.stringify({jsonrpc:'2.0',method:method, params:params, id:"jsonrpc"}),
            type:"POST",
            dataType:"json",
            success:  function(data){ successCallback(data); },
            error: function(err){ alert("Error"); }
         });
    }
};