/*
* (c) CShield 2019
*/

$(function() {
    var commandinputenabled = true;
    $( "#commandinput" ).submit(function( event ) {
      if (!commandinputenabled)
      {
        event.preventDefault();
        return;
      }
      commandinputenabled = false;
      $("#commandinput").hide();
      $(".command-sending").fadeIn('fast',function(){$(this).html('Running...')});
      var cmd = $("#commandinput input[type='text']").val();
      var cid = $("#commandinput #consoleid").val();
      var data =  { 'cmd':cmd,'step':1 }
      var cel = $("#console");
      cel.append('<div><span style="color:#ccffcc">Console R'+cid+'&gt;</span> '+cmd+'</div>');

      SendCommand(data,cel,cid);
      event.preventDefault();
    });
    function SendCommand(data,cel,cid)
    {
        var jqxhr = $.post( "/console/", data)
          .done(function(response) {

            $.each(response.data, function( index, value ) {
                if (value.format == 'line')
                    cel.append('<div><span style="color:#ccffcc">Console R'+cid+'&gt;</span> '+value.v+'</div>');
                else
                    cel.append(value.v);
            });
            //special

            if (!response.islast){
                data.step = data.step+1;
                SendCommand(data,cel,cid);
            }
            else{
                if (response.redirect)
                {
                    window.location.replace(response.redirect);
                }
                commandinputenabled = true;
                $("#commandinput input[type='text']").val('');
                $(".command-sending").hide().html('Sending...');
                $("#commandinput").show();
            }
          })
          .fail(function() {
            cel.append('<div><span style="color:#ccffcc">Console R'+cid+'&gt;</span> <span style="color:red">Communication error</span></div>');
            commandinputenabled = true;
            $("#commandinput input[type='text']").val('');
            $(".command-sending").hide().html('Sending...');
            $("#commandinput").show();
          })
          //.always(function() {});
    }
});
function SetCommand(cmd)
{
    $("#commandinput input[type='text']").val(cmd).focus();
    return false;
}
