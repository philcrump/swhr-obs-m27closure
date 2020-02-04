<?php
  $dbc = null;

  $observations_apc_name = "m27-observations";

  // Check cache for these results
  $observations_json = apcu_fetch($observations_apc_name, $_success);
  if($_success == false)
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
      *
      FROM `observations`
      ORDER BY `location_id`, `time_id` ASC;
EOD;
    $stmt = $dbc->prepare($stream_query);

    $observations=[];

    // Members
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
        $observations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $stmt->closeCursor();

    $observations_json = json_encode($observations);

    // Store results with TTL of 1s
    apcu_store($observations_apc_name, $observations_json, 1);
  }

  print $observations_json;

  $dbc = null;
?>
