<?

include('config.php');

$schedule = $HTTP_POST_VARS['schedule'];
$the_id = track_usage( $schedule );

$tpl = new TemplatePower( "templates/schedule_success.tpl" );
$tpl->prepare();

$tpl->assign("SCHEDULEID",$the_id);

$tpl->printToScreen();

	/**
	* Generate ID
	**/
	function generate_ID() {
		global $CFG_DATABASE_TABLE;
		$flag = true;

		while ( $flag ) {

		$the_id = array(0,0,0,0,0,0);
		$id = "";
		$letters = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
							"R","S","T","U","V","W","X","Y","Z");


		$numbers = array("0","1","2","3","4","5","6","7","8","9");


		$the_id[0] = $letters[rand( 0, sizeof( $letters ) - 1)];
		$the_id[1] = $letters[rand( 0, sizeof( $letters ) - 1)];
		$the_id[2] = $letters[rand( 0, sizeof( $letters ) - 1)];
		$the_id[3] = $numbers[rand( 0, sizeof( $numbers ) - 1)];
		$the_id[4] = $numbers[rand( 0, sizeof( $numbers ) - 1)];
		$the_id[5] = $numbers[rand( 0, sizeof( $numbers ) - 1)];

		for ( $i = 0; $i < sizeof( $the_id); $i++ ) {
			$id = $id.$the_id[$i];
		}

		if (mysql_num_rows(mysql_query("SELECT * from $CFG_DATABASE_TABLE where schid ='".$id."'")) == 0) {
			$flag = false;
		}

		}

		return ( $id );
	}

	/**
	* This records some data into a database to
	* track program usage
	**/
	function track_usage( $schedule ) {
		global $CFG_DATABASE_TABLE,$HTTP_REFERER,$REMOTE_ADDR;

		$today = getdate();
		$month = $today['month'];
		$mday = $today['mday'];
		$year = $today['year'];
		$schedule_id = generate_ID();


		$query = "INSERT INTO $CFG_DATABASE_TABLE (schid, time, ip, schedule) VALUES ('".$schedule_id."','"."$month $mday, $year"."','".$REMOTE_ADDR."','".$schedule."')";
		$result = mysql_query ( $query );
		return ($schedule_id );
	}

?>