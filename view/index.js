var updated_timestamp = 0;
var updating = false;

$(function()
{
  updateData();
});

function updateData()
{
  updating = true;
  $.getJSON(
    "data.php",
    function(data)
    {
      updated_timestamp = (Date.now() / 1000.0);
      updating = false;
      $("#span-updated").text("Last updated: 0 seconds ago");
      console.log(data);
      $.each(data, function(i,v)
      {
        $("#datacell-"+v.location_id+"-"+v.time_id).html("Traffic: "+v.report+"/10<br>N or E: "+v.count_ne+"/1m<br>S or W: "+v.count_sw+"/1m");
      });
    }
  );
  $("#span-updated").html("Last updated: <i>Updating</i>");
  setTimeout(updateData, 5*1000);
  setInterval(updateUpdatedDisplay, 1000);
}

function updateUpdatedDisplay()
{
  if(updated_timestamp != 0 && updating == false)
  {
    var interval = Math.round((Date.now() / 1000.0) - updated_timestamp);
    $("#span-updated").text("Last updated: "+interval+" seconds ago");
  }
}
