<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SWHR M27 Closure Report View</title>
    <meta name="author" content="South West Hampshire RAYNET">
    <link rel="icon" href="/favicon.ico">
    <link href="../lib/bootstrap-4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
    <script src="../lib/jquery-3.4.1.min.js"></script>
    <script src="../lib/bootstrap-4.4.1/js/bootstrap.min.js"></script>
    <script src="index.js"></script>
  </head>
  <body class="bg-light">
<?php
  $dbc = null;

  $times_apc_name = "m27-times";
  $times = null;

  $locations_apc_name = "m27-locations";
  $locations = null;

  // Check cache for these results
  $times_json = apcu_fetch($times_apc_name, $_success);
  if($_success)
  {
    $times = json_decode($times_json, true);
  }
  else
  {
    // Query them
    require_once("../credentials.php");
    if($dbc == null)
    {
      try
      {
        $dbc = new PDO($db_string, $db_username, $db_password, array(
          PDO::ATTR_PERSISTENT => true
        ));
      }
      catch (PDOException $e)
      {
        exit;
      }
    }

    if(date("Y-m-d") == "2020-02-01" or date("Y-m-d") == "2020-02-02")
    {
      $stream_query = <<<'EOD'
       SELECT
       `id`, `time`
       FROM `times`
       WHERE `time` >= CURDATE() AND `time` < CURDATE() + INTERVAL 1 DAY
       ORDER BY `id` ASC;
EOD;
    }
    else
    {
      $stream_query = <<<'EOD'
       SELECT
       `id`, `time`
       FROM `times`
       ORDER BY `id` ASC;
EOD;
    }
    
    $stmt = $dbc->prepare($stream_query);

    $times=[];

    // Members
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
        $times = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $stmt->closeCursor();

    // Store results with TTL of 1s
    apcu_store($times_apc_name, json_encode($times), 1);
  }

  // Check cache for these results
  $locations_json = apcu_fetch($locations_apc_name, $_success);
  if($_success)
  {
    $locations = json_decode($locations_json, true);
  }
  else
  {
    // Query them
    require_once("../credentials.php");
    if($dbc == null)
    {
      try
      {
        $dbc = new PDO($db_string, $db_username, $db_password, array(
          PDO::ATTR_PERSISTENT => true
        ));
      }
      catch (PDOException $e)
      {
        exit;
      }
    }

    $stream_query = <<<'EOD'
     SELECT
      `id`, `name` 
      FROM `locations`
      ORDER BY `id` ASC;
EOD;
    $stmt = $dbc->prepare($stream_query);

    $locations=[];

    // Members
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $stmt->closeCursor();

    // Store results with TTL of 1s
    apcu_store($locations_apc_name, json_encode($locations), 1);
  }

?>
<center><h3>South-West Hants RAYNET - M27 Closure Observations</h3></center>
<b id="span-updated"></b><br>
<div class="table-responsive">
<table class="table table-sm table-striped">
<?php
// Table
// Time Headers
print "<tr>";
print "<th></th>";
foreach($times as $time)
{
  $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $time["time"] );

  print "<th>";
  print $new_datetime->format('H:i');
  print "</th>";
}
print "</tr>";

foreach($locations as $location)
{
  print "<tr>";

  print "<th class=\"loccell\">";
  print $location['name'];
  print "</th>";

  foreach($times as $time)
  {
    printf("<td id=\"datacell-%d-%d\" class=\"datacell\">", $location['id'], $time['id']);
    print "</td>";
  }

  print "<th class=\"loccell\">";
  print $location['name'];
  print "</th>";

  print "</tr>";
}

print "<tr>";
print "<th></th>";
foreach($times as $time)
{
  $new_datetime = DateTime::createFromFormat ( "Y-m-d H:i:s", $time["time"] );

  print "<th>";
  print $new_datetime->format('H:i');
  print "</th>";
}
print "</tr>";

?>
</table>
</div>

</body>
</html>

<?php
  if($dbc != null)
  {
    $dbc = null;
  }
?>
