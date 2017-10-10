<?

	function search( $item, $arr ) {
		$result = false;

		for ($i = 0; $i < count($arr); $i++) {
			if (strcmp($arr[$i], $item) == 0) {
				$result = true;
			}
		}

		return($result);
	}

/**
*returns the index of where item is found
**/
	function search_index( $item, $arr ) {
		$result = -1;

		for ($i = 0; $i < count($arr); $i++) {
			if (strcmp($arr[$i], $item) == 0) {
				$result = $i;
			}
		}

		return($result);
	}


// mysql server hostname
$CFG_MYSQL_HOSTNAME = "localhost";

// database name
$CFG_MYSQL_DATABASE = "schedule";

// database username
$CFG_MYSQL_USERNAME = "************";

// database psasword
$CFG_MYSQL_PASSWORD = "************";

// database table
$CFG_DATABASE_TABLE = "schedule";




// days of week strings in the order to be
// used by the system
$CFG_DAYS_OF_WEEK = array( "Monday", "Tuesday", "Wednesday",
							"Thursday", "Friday", "Saturday",
								"Sunday");

// time strins to be used in the correct order
// they are in a seperate file
include('config_times.php');

// Include all class and system files needed
include('class.TemplatePower.inc.php');
include('mysql_connect.php');
include('class_system.php');
include('class_course.php');
include('class_day.php');
include('class_coursemaker.php');



?>