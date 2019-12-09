<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SWHR M27 Closure Report Submission</title>
    <meta name="author" content="South West Hampshire RAYNET">
    <link rel="icon" href="/favicon.ico">
    <link href="../lib/bootstrap-4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
    <script src="../lib/jquery-3.4.1.min.js"></script>
    <script src="../lib/bootstrap-4.3.1/js/bootstrap.min.js"></script>
    <script src="index.js"></script>
  </head>
  <body class="bg-light text-center">

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
    $times = json_decode($times_json);
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
      `id`, `time` 
      FROM `times`
      ORDER BY `id` ASC;
    EOD;
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
    apcu_store($times_apc_name json_encode($times), 1);
  }

  // Check cache for these results
  $locations_json = apcu_fetch($locations_apc_name, $_success);
  if($_success)
  {
    $locations = json_decode($locations_json);
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

<form>
<?php
// Print Location dropdown
print "<select>";
foreach($locations as $location)
{
  printf("<option value=\"%s\">%s</option>", $location['id'], $location['name']);
}
print "</select>";

print "<br><br>";

// Print Time dropdown
print "<select>";
foreach($times as $time)
{
  printf("<option value=\"%s\">%s</option>", $time['id'], $location['time']);
}
print "</select>";

?>
Traffic Report:
<select>
  <option value="0">0 - None</option>
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4</option>
  <option value="5">5 - Heavy but flowing</option>
  <option value="6">6</option>
  <option value="7">7</option>
  <option value="8">8</option>
  <option value="9">9</option>
  <option value="10">10 - gridlocked</option>
</select>
<br>
Vehicle Count North/East: <input type="text"></input><br>
Vehicle Count South/West: <input type="text"></input>
<br>
<button type="submit">Submit</button>
</form>

</body>
</html>

<?php
  if($dbc != null)
  {
    $dbc = null;
  }
?>