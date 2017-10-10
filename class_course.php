<?

/**
* Course objects represents a particular class at RIT.
* They are also used to represent 'events' such as a job.
**/
class Course {

	var $number;		// the courses number under SIS
	var $name;			// the courses full name
	var $instructor;	// course instructors last name
	var $days;			// array holding courses meeting days
	var $start_time;	// this courses start time
	var $end_time;		// this courses ending time
	var $building;		// the building this courses is in
	var $room;			// the room this course is in

	/**
	* Course constructor function
	**/
	function Course() {

		// initilize the days array
		// a zero represents this course does not
		// take place on that day, a 1 means it does
		// take place on this day.
		// index 0 is monday, index 6 is sunday
		$this->days = array(0,0,0,0,0,0,0);
	}


	# BELOW ARE THE SYSTEM FUNCTIONS FOR THE CLASS

	/**
	* This returns how many 30 minute blocks the course
	* takes up.  it returns at int
	**/
	function get_course_length() {

		$start_hour = $this->get_time_hour( $this->get_good_start_time() );
		$start_minute = $this->get_time_minute( $this->get_good_start_time() );
		$start_AMPM = $this->get_time_AMPM( $this->get_good_start_time() );

		$end_hour = $this->get_time_hour( $this->get_good_end_time() );
		$end_minute = $this->get_time_minute( $this->get_good_end_time() );
		$end_AMPM = $this->get_time_AMPM( $this->get_good_end_time() );

		// round the start and end times either down or up
		// accordingly
		if ( ( $start_minute != "00") && ($start_minute != "30") ) {

			if ( $start_minute < 30 ) {
				$start_minute = "00";
			}
			else {
				$start_minute = "30";
			}
		}

		if ( ( $end_minute != "00") && ($end_minute != "30") ) {

			if ( $end_minute < 30 ) {
				$end_minute = "30";
			}
			else {
				$end_minute = "00";

				if ( $end_hour == 12 ) {
					$end_hour = 1;
				}
				else {
					$end_hour++;
				}
			}
		}


		// convert to army time for easy addition and subtraction
		if ( $start_AMPM == "PM" && $start_hour != "12") {
			$start_hour += 12;
		}

		if ( $end_AMPM == "PM" && $end_hour != "12") {
			$end_hour += 12;
		}

		$total_time = $end_hour - $start_hour;

		// convert to 30 minute increments
		$total_time = $total_time * 2;

		if ( $start_minute == "30" ) {
			$total_time = $total_time - 1;
		}

		if ( $end_minute == "30" ) {
			$total_time = $total_time + 1;
		}
		
		return ( $total_time );
	}

	/**
	* this explodes the time string and returns just
	* the hour part
	*/
	function get_time_hour( $time ) {
		$time_pieces = explode(":", $time);

		return $time_pieces[0];
	}

	/**
	* this explodes the time string and returns just
	* the minute part
	**/
	function get_time_minute( $time ) {
		$time_pieces = explode(":", $time);

		$second_time_pieces = explode(" ", $time_pieces[1]);

		return ($second_time_pieces[0]);
	}

	/**
	* this explodes the time string and returns the am or
	* pm part
	**/
	function get_time_AMPM( $time ) {

		$retval = "";

		$time_pieces = explode(":", $time);

		$second_time_pieces = explode(" ", $time_pieces[1]);

		if ( $second_time_pieces[1] != "AM" && $second_time_pieces[1] != "PM" ) {
			$retval = $second_time_pieces[2];
		}
		else {
			$retval = $second_time_pieces[1];
		}

		return ($retval);
	}

	/**
	* gets the start time
	**/
	function get_start_time(){
		return $this->start_time;
	}

	/**
	* gets the start time formatted in hour:minute PM format
	**/
	function get_good_start_time() {

		$temp = $this->start_time;
		$temp = strtoupper( $temp );

		$temp = str_replace( "N", " P", $temp );
		$temp = str_replace( "A", " AM", $temp );
		$temp = str_replace( "P", " PM", $temp );



		$length = strlen( $temp );

		if ( $length == 6 ) {
			$temp = substr( $temp, 0, 1).":".substr( $temp, 1);
		}
		else if ( $length == 7 ) {
			$temp = substr( $temp, 0, 2).":".substr( $temp, 2);
		}
		else if ( $length == 8 ) {
			$temp = substr( $temp, 0, 2).":".substr( $temp, 2);
		}

		return $temp;
	}

	/**
	* gets the end time formatted in hour:minute PMformat
	**/
	function get_good_end_time() {

		$temp = $this->end_time;
		$temp = strtoupper( $temp );

		$temp = str_replace( "N", " P", $temp );
		$temp = str_replace( "A", " AM", $temp );
		$temp = str_replace( "P", " PM", $temp );

		$length = strlen( $temp );

		if ( $length == 6 ) {
			$temp = substr( $temp, 0, 1).":".substr( $temp, 1);
		}
		else if ( $length == 7 ) {
			$temp = substr( $temp, 0, 2).":".substr( $temp, 2);
		}

		return $temp;

	}


	# BOOLEAN DAY FUNCTION

	/**
	* this tells weither or not the class occurs on the given day
	**/
	function class_on_day( $day ){
		return $this->days[$day];
	}

	# BELOW ARE THE SET FUNCTIONS FOR THE CLASS

	/**
	* This sets the course number
	**/
	function set_number( $number ) {
		$this->number = $number;
	}

	/**
	* This sets the course name
	**/
	function set_name( $name ) {
		$this->name = $name;
	}

	/**
	* This sets the course instructor
	**/
	function set_instructor( $instructor ) {
		$this->instructor = $instructor;
	}

	/**
	* This sets the course days one by one
	* is passed in the index or day to mark as true
	**/
	function set_day_true( $day_to_set ) {
		$this->days[ $day_to_set ] = 1;
	}

	/**
	* This sets the courses starting time
	**/
	function set_start_time( $start ) {
		$this->start_time = $start;
	}

	/**
	* This sets the courses ending time
	**/
	function set_end_time( $end ) {
		$this->end_time = $end;
	}

	/**
	* This sets the courses building
	**/
	function set_building( $building ) {
		$this->building = $building;
	}

	/**
	* This sets the courses room number
	**/
	function set_room( $room ) {
		$this->room = $room;
	}

	/**
	* print out this course
	**/
	function toString() {
		echo "<br>Number: ".$this->number."<BR>";
		echo "Name: ".$this->name."<BR>";
		echo "Instructor: ".$this->instructor."<BR>";
		echo "Days: ";
		print_r($this->days);
		echo "<br>Start: ".$this->start_time."<BR>";
		echo "End: ".$this->end_time."<BR>";
		echo "Building: ".$this->building."-".$this->room."<BR>";
	}

}

 ?>