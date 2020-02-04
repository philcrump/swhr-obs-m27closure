<?php
  $dbc = null;

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

  $filename = "export.csv";
  header('Content-type: text/csv');
  header("Content-Disposition: attachment; filename=\"{$filename}\"");
  $file = fopen('php://output', 'w');

    $times_query = <<<'EOD'
SELECT
id, time
FROM `times` ORDER by `id` ASC;
EOD;
    $stmt = $dbc->prepare($times_query);
    $times=[];
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
      $times = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $stmt->closeCursor();

    $header_fields = array('Location');
    foreach($times as $time)
    {
        $header_fields[] = $time['time'];
    }
    fputcsv($file, $header_fields);

    $locations_query = <<<'EOD'
SELECT
id, name
FROM `locations` ORDER by `id` ASC;
EOD;
    $stmt = $dbc->prepare($locations_query);
    $locations=[];
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
      $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $stmt->closeCursor();

    $current_time = 0;
    $current_location = 0;

    $observation_query = <<<'EOD'
SELECT `report`, `count_ne`, `count_sw` FROM observations
WHERE
`time_id` = ? and `location_id` = ?;
EOD;
  $stmt = $dbc->prepare($observation_query);
  $stmt->bindParam(1, $current_time, PDO::PARAM_INT);
  $stmt->bindParam(2, $current_location, PDO::PARAM_INT);
  
    foreach($locations as $location)
    {
      $row = [];
      $row[] = $location['name'];

      $current_location = $location['id'];

      foreach($times as $time)
      {
        $current_time = $time['id'];
        $stmt->execute();
        if($stmt->rowCount()==1)
        {
          $observation = $stmt->fetch(PDO::FETCH_ASSOC);
	  $row[] = "Traffic: ".$observation['report']."/10\nN or E: ".$observation['count_ne']."/1m\nS or W: ".$observation['count_sw']."/1m";
        }
        else
        {
          $row[] = "-";
        }
      }
      fputcsv($file, $row);
    }
    $stmt->closeCursor();
        
    fclose($file);
?>  
