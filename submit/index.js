var storageSupport = false;
var init_location = null;

var report_to_submit = null;

if(typeof(Storage) !== "undefined")
{
  storageSupport = true;

  if(localStorage.Location)
  {
    init_location = localStorage.Location;
  }
}

var input_select_location;

$(function()
{
  input_select_location = $("#input-select-location");

  if(init_location != null)
  {
    input_select_location.val(init_location);
  }

  input_select_location.change(function()
  {
    init_location = $(this).children("option:selected").val();
    if(storageSupport)
    {
      localStorage.Location = init_location;
    }
  });
  
  $("#form-observation").on('submit', function(e)
  {
    e.preventDefault();

    report_to_submit = {
      location: $("#input-select-location").val(),
      time: $("#input-select-time").val(),
      report: $("#input-select-report").val(),
      count_ne: $("#input-number-count-ne").val(),
      count_sw: $("#input-number-count-sw").val(),
    };

    submit_report();
  });
});


function submit_report()
{
  if(report_to_submit == null)
  {
    console.log("Error: Tried to submit but report is null");
    exit();
  }
  $.post("submit.php", report_to_submit, function( data )
  {
    /* Success! */
    report_to_submit = null;
    $("#submit-message").text("Report Submitted.");
    $("#submit-message").css('opacity', '1.0');
    $("#form-observation").trigger('reset');
    if(init_location != null)
    {
      input_select_location.val(init_location);
    }
    setTimeout(function(){
      $("#submit-message").fadeTo( 500, 0 );
    }, 1500);
    setTimeout(function(){
      $("#submit-message").text('');
      $("#submit-message").css('opacity', '1.0');
    }, 2100);
  }).fail(function(res) {
    console.log("Form submit failed:");
    console.log(res);
    if(res.status != 400 && res.status != 403)
    {
      $("#submit-message").text("Submission failed, retrying..");
      $("#submit-message").css('opacity', '1.0');
      setTimeout(submit_report, 1000);
    }
    else
    {
      $("#submit-message").text("Submission failed.");
      $("#submit-message").css('opacity', '1.0');
    }
  });
  $("#submit-message").text("Submitting...");
  $("#submit-message").css('opacity', '1.0');
}
