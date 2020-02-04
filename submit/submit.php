<?php

  if(!(
    isset($_POST["location"])
    && isset($_POST["time"])
    && isset($_POST["report"])
    && isset($_POST["count_ne"])
    && isset($_POST["count_sw"])
  ))
  {
    http_response_code(400); 
    die("Invalid request, form fields missing.");
  }

  $datetime_now = new DateTime();
  $datetime_endOfEvent = new DateTime("2020-02-03 00:00:00");

  if($datetime_now > $datetime_endOfEvent)
  {
    http_response_code(403); 
    die("Event has finished, no further reports permitted.");
  }

  // Query them
  require_once("../credentials.php");
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


  $stmt = $dbc->prepare("INSERT INTO `observations` (`location_id`, `time_id`, `report`, `count_ne`, `count_sw`) VALUES ( ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `report` = ?, `count_ne` = ?, `count_sw` = ?;");
  $stmt->bindValue(1, $_POST["location"], PDO::PARAM_INT);
  $stmt->bindValue(2, $_POST["time"], PDO::PARAM_INT);
  $stmt->bindValue(3, $_POST["report"], PDO::PARAM_INT);
  $stmt->bindValue(4, $_POST["count_ne"], PDO::PARAM_INT);
  $stmt->bindValue(5, $_POST["count_sw"], PDO::PARAM_INT);
  $stmt->bindValue(6, $_POST["report"], PDO::PARAM_INT);
  $stmt->bindValue(7, $_POST["count_ne"], PDO::PARAM_INT);
  $stmt->bindValue(8, $_POST["count_sw"], PDO::PARAM_INT);
  $stmt->execute();
  $stmt->closeCursor();

  print("Submission success");

  $dbc = null;
?>
