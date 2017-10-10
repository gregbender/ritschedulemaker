<?
include('config.php');

$id_lookup = $id;
$query = "SELECT * from $CFG_DATABASE_TABLE where schid = '".$id_lookup."'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

$the_schedule = unserialize(urldecode($row['schedule']));

echo $the_schedule->make_schedule($the_schedule);




?>