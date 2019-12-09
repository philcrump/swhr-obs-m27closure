<?php

// Check cache for these results
if (apcu_exists($_SERVER["SCRIPT_NAME"]))
{
    print apcu_fetch($_SERVER["SCRIPT_NAME"]);
    exit(0);
}

// Else generate it
require_once("credentials.php");
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

$stream_query = <<<'EOD'
 SELECT
  wp_usermeta.user_id, wp_usermeta.meta_key, wp_usermeta.meta_value 
  FROM wp_usermeta
  INNER JOIN
    (SELECT user_id
     FROM wp_usermeta
     WHERE meta_key=? AND meta_value="1"
    ) AS uid_category ON wp_usermeta.user_id=uid_category.user_id
  INNER JOIN
    (SELECT user_id
     FROM wp_usermeta
     WHERE meta_key="stream_listed" AND meta_value="1"
    ) AS uid_listed ON wp_usermeta.user_id=uid_listed.user_id
  WHERE meta_key LIKE "stream%" AND meta_key != "stream_rtmp_input_url"
  ORDER BY wp_usermeta.user_id ASC;
EOD;
$stmt = $dbc->prepare($stream_query);

$member_streams=[];
$repeater_streams=[];
$event_streams=[];

$member_array=[];
$repeater_array=[];
$event_array=[];

// Members
$stmt->execute(array("stream_type_member"));
if($stmt->rowCount()>0)
{
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row)
    {
        if(!array_key_exists($row['user_id'], $member_streams))
        {
            $member_streams[$row['user_id']] = [];
        }
        $member_streams[$row['user_id']][$row['meta_key']] = $row['meta_value'];
        if($row['meta_key']=="stream_output_url")
        {
             $duration = apc_fetch("stream_active:{$row['meta_value']}", $active);
             if($active)
             {
                 $member_streams[$row['user_id']]['active'] = $duration;
             }
        }
    }
    $row = null;
    foreach($member_streams as $member_stream)
    {
        array_push($member_array, $member_stream);
    }
}
$stmt->closeCursor();

// Repeaters
$stmt->execute(array("stream_type_repeater"));
if($stmt->rowCount()>0)
{
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row)
    {
        if(!array_key_exists($row['user_id'], $repeater_streams))
        {
            $repeater_streams[$row['user_id']] = [];
        }
        $repeater_streams[$row['user_id']][$row['meta_key']] = $row['meta_value'];
        if($row['meta_key']=="stream_output_url")
        {
             $duration = apc_fetch("stream_active:{$row['meta_value']}", $active);
             if($active)
             {
                 $repeater_streams[$row['user_id']]['active'] = $duration;
             }
        }
    }
    $row = null;
    foreach($repeater_streams as $repeater_stream)
    {
        array_push($repeater_array, $repeater_stream);
    }
}
$stmt->closeCursor();

// Events
$stmt->execute(array("stream_type_event"));
if($stmt->rowCount()>0)
{
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row)
    {
        if(!array_key_exists($row['user_id'], $event_streams))
        {
            $event_streams[$row['user_id']] = [];
        }
        $event_streams[$row['user_id']][$row['meta_key']] = $row['meta_value'];
        if($row['meta_key']=="stream_output_url")
        {
             $duration = apc_fetch("stream_active:{$row['meta_value']}", $active);
             if($active)
             {
                 $event_streams[$row['user_id']]['active'] = $duration;
             }
        }
    }
    $row = null;
    foreach($event_streams as $event_stream)
    {
        array_push($event_array, $event_stream);
    }
}
$stmt->closeCursor();

$dbc = null;

$results = json_encode(
    Array(
        "members" => $member_array,
        "repeaters" => $repeater_array,
        "events" => $event_array
    )
);
// Store results with TTL of 1s
apcu_store($_SERVER["SCRIPT_NAME"], $results, 1);
print $results;
?>