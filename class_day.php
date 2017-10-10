<?

/**
* This class represents one of the days of the week
* it also holds 48 half-hour slots of course information
**/

class Day {

	var $name;	// this days name
	var $times = array(); // array that holds the 48 possible times

	/**
	* The constructor for day, argument is the days name
	**/
	function Day( $name ) {

		$this->name = $name;

		// fill the times array with 48 zeros
		for ( $i = 0; $i <48; $i++ ) {
			array_push( $this->times, 0);
		}
	}

	/**
	* this asks for the time id, and will return a
	* course object or a 1
	**/
	function get_time( $id ) {

			return( $this->times[$id] );
	}

	/**
	* returns true if this day contains nothing (all zeros)
	**/
	function has_nothing() {
		$retval = 1;

		for ( $i = 0; $i < sizeof( $this->times); $i++ ) {

			if ( $this->times[$i] != 0 ) {
				$retval = -1;
			}
		}
		return ($retval);
	}

	/**
	* returns the integer timeslot that cooresponds to this
	* days first time that is used.  it is -1 if no time is used.
	**/
	function first_time() {
		$retval = -1;
		$flag = true;
		$i = 0;

		while ( ($i < sizeof ( $this->times )) && $flag ) {

			if ( $this->times[$i] != 0 ) {
				$retval = $i;
				$flag = false;
			}
			$i++;
		}

		return ( $retval );
	}

	/**
	* returns the integer timeslot that cooresponds to this days
	* last time that is used.  it is -1 if no time is used
	**/
	function last_time() {
		$retval = -1;
		$flag = true;
		$i = sizeof ( $this->times ) - 1;

		while ( ( $i >= 0 ) && $flag ) {

			if ( $this->times[$i] != 0 ) {
				$retval = $i;
				$flag = false;
			}
			$i--;
		}
		
		return ( $retval );
	}

	/**
	* this inserts a reference to a course object
	* into the approperiate index in the times array
	* this is used for the 'start time'
	* also passed is course length
	**/
	function add_course( $course ) {
		global $CFG_TIMES;

		$course_hour = $course->get_time_hour(
							$course->get_good_start_time() );



		$course_minute = $course->get_time_minute(
							$course->get_good_start_time() );


		$course_AMPM = $course->get_time_AMPM(
							$course->get_good_start_time() );

		// round the start and end times either down or up
		// accordingly
		if ( ( $course_minute != "00") && ($course_minute != "30") ) {

			if ( $course_minute < 30 ) {
				$course_minute = "00";
			}
			else {
				$course_minute = "30";
			}
		}

		$course_start = "".$course_hour.":".$course_minute." ".
			$course_AMPM;
		$course_start_num = search_index( $course_start, $CFG_TIMES);

		$this->times[ $course_start_num ] = $course;
		$course_end = $course_start_num + $course->get_course_length()-1;

		for( $temp = ($course_start_num + 1); $temp <= $course_end; $temp++ ) {
			$this->times[$temp] = 1;
		}
	}
}

?>