<?
/**
* A main utility class that manages the days and courses
* it also keeps track of all templates
**/

class System {

	var $course_holder = array();	// array holding all courses
	var $day_holder = array();	// array holding all the days
	var $year;			// the year of this schedule
	var $quarter;		// the year of this quarter

	var $EL_start_day;    // the first day to be display in schedule, set to -1 if schedule is elastic
	var $EL_end_day;// the last day to be display in schedule, set to -1 if schedule is elastic
	var $EL_start_time; // the first time to be display in schedule, set to -1 if schedule is elastic
	var $EL_end_time;// the last time to be display in schedule, set to -1 if schedule is elastic

	var $just_created; // this is set to true if this schedule was just created now, and not stored online

	# these variables deal with what text to display
	var $display_course_name;
	var $display_course_number;
	var $display_professor;
	var $display_building_room;
	var $display_time;

	/**
	* System constructor
	**/
	function System() {

		$this->EL_start_day = -1;
		$this->EL_end_day = -1;
		$this->EL_start_time = -1;
		$this->EL_end_time = -1;

		$this->display_course_name = -1;
		$this->display_course_number = -1;
		$this->display_professor = -1;
		$this->display_building_room = -1;
		$this->display_time = -1;
	}

	/**
	* this sets what should be displayed for each course
	*/
	function set_displays ( $course_name, $course_number, $professor, $building_room, $time ) {

		$this->display_course_name = $course_name;
		$this->display_course_number = $course_number;
		$this->display_professor = $professor;
		$this->display_building_room = $building_room;
		$this->display_time = $time;
	}

	/**
	* this sets the variables used when non-elastic mode is used
	* by the user
	*/
	function set_ELS( $start_day, $end_day, $start_time, $end_time ) {

		global $CFG_DAYS_OF_WEEK,$CFG_TIMES;

		$start_day = search_index( $start_day, $CFG_DAYS_OF_WEEK);
		$end_day = search_index( $end_day, $CFG_DAYS_OF_WEEK) + 1;

		$start_time = search_index( $start_time, $CFG_TIMES);
		$end_time = search_index( $end_time, $CFG_TIMES);

		$this->EL_start_day = $start_day;
		$this->EL_end_day = $end_day;
		$this->EL_start_time = $start_time;
		$this->EL_end_time = $end_time;
	}

	/**
	* This goes through each day of the week,
	* it will iterate though each course one day at
	* a time.  If the course meets on the checked day, it
	* will add the course time data to the approperiate day
	* class
	**/
	function process_days_of_week() {

		while ( sizeof( $this->course_holder) > 0 ) {

			$temp_course = array_pop($this->course_holder);

			for( $i = 0; $i < 8; $i++ ){

				$course_days = $temp_course->class_on_day( $i );

				if( $course_days ){

					$it = $this->day_holder[$i];
					$it->add_course($temp_course);

					$this->day_holder[$i] = $it;
				}
			}
		}
	}

	/**
	* This function traverses each day, and processes
	* the template files, and returns the generated schedule
	* as a string
	**/
	function make_schedule( $serialize_object ) {
		global $CFG_TIMES,$CFG_DAYS_OF_WEEK;

		# this keeps track of how many courses have been processed
		$courses_processed = array();

		$retval = "";

		$tpl = new TemplatePower( "templates/main_table.tpl" );

		if ($this->just_created) {
			$tpl->assignInclude( "save_online", "templates/save_online_form.tpl" );
		}
		else {
			$tpl->assignInclude( "save_online", "templates/blank_advanced2.tpl" );
		}

		$tpl->prepare();

		if ( $serialize_object == -1 ) {
			$tpl->assign("SCHEDULE_SER", "");
		}
		else {
			$tpl->assign("SCHEDULE_SER", $serialize_object);
		}

		# put in the days at the top of the file
		for ( $j = $this->find_start_day(); $j < $this->find_end_day(); $j++ ) {

			$tpl->newBlock("days");
			$tpl->assign("DAY_WIDTH", $this->generate_width());
			$tpl->assign("THEDAY", $CFG_DAYS_OF_WEEK[$j]);
		}

		for ( $i = $this->find_start_time(); $i < $this->find_end_time(); $i++ ) {

			$tpl->newBlock("timerow");
			$tpl->assign("TIMETEXT", $CFG_TIMES[$i]);

			for ( $j = $this->find_start_day(); $j < $this->find_end_day(); $j++ ) {

				if (($this->day_holder[$j]->get_time($i)) == 0) {

					#do a blank row

					$tpl_empty_course = new TemplatePower( "templates/td_blank.tpl" );
					$tpl_empty_course->prepare();
					$tpl->assign(strtoupper($CFG_DAYS_OF_WEEK[$j]),
					$tpl_empty_course->getOutPutContent());

				}
				else if ( ($this->day_holder[$j]->get_time($i)) == 1) {

					$tpl_empty_course = new TemplatePower( "templates/td_blank.tpl" );
					$tpl_empty_course->prepare();
					$tpl->assign(strtoupper($CFG_DAYS_OF_WEEK[$j]),
					"");

				}
				else {

					$tpl_course = new TemplatePower( "templates/td_course.tpl" );
					$tpl_course->prepare();

					$theclass = $this->day_holder[$j]->get_time($i);

					$courselength = $theclass->get_course_length();
					$classnumber = $theclass->number;

					#used for non-course objects
					if ( $classnumber == "" ) {
						$classnumber = $theclass->name;
					}

					$coursetext = $this->make_course_text( $theclass );

					$tpl_course->assign("COURSELENGTH",$courselength);

					# find which out of all the courses it's CSS number will be
					$courses_processed_index = search_index( $classnumber ,$courses_processed);

					if ( $courses_processed_index == -1 ) {
						$courses_processed_index = array_push( $courses_processed, $classnumber ) - 1;
					}

					$tpl_course->assign("CLASSNUMBER",$courses_processed_index);
					$tpl_course->assign("COURSETEXT",$coursetext);
					
					# make text centered
					$tpl_course->assign("CENTERED", "align=\"center\"");

					$tpl->assign(strtoupper($CFG_DAYS_OF_WEEK[$j]),
									$tpl_course->getOutPutContent() );



				}

			}

		}




		$retval = $tpl->getOutPutContent();

		return( $retval );
	}

	/**
	* This generates what the course text should look like
	*/
	function make_course_text( $theclass ) {

		$retval = "";

		if ( ($theclass->instructor == "") && ($theclass->building == "") && ($theclass->room == "")) {
			$retval = "<B>".$theclass->name."</B>";
		}
		else {

			if ($this->display_course_name == "ON") {

				$newtext = ucwords($theclass->name);
				# fix mistakes with roman numberals being displayed
				$newtext = str_replace( " Ii", " II", $newtext);
				$newtext = str_replace( " IIi", " III", $newtext);
				$newtext = str_replace( " Iv", " IV", $newtext);

				$retval = $retval."<B>".$newtext."</B>";
				$retval = $retval."<BR>";
			}

			if ($this->display_time == "ON") {

				$newtext = $theclass->get_good_start_time()." - ".$theclass->get_good_end_time();

				$retval = $retval.$newtext;
				$retval = $retval."<BR>";
			}

			if ($this->display_course_number == "ON" ) {

				$newtext = strtoupper($theclass->number);

				$retval = $retval.$newtext;
				$retval = $retval."<BR>";
			}

			if ($this->display_professor == "ON") {

				$newtext = ucfirst($theclass->instructor);

				$retval = $retval.$newtext;
				$retval = $retval."<BR>";
			}

			if ($this->display_building_room == "ON" ) {

				$newtext = strtoupper($theclass->building)."-".strtoupper($theclass->room);

				$retval = $retval.$newtext;
				$retval = $retval."<BR>";
			}
		}
		return $retval;
	}

	/**
	* add a new course for system to keep track of
	**/
	function add_course( $course ) {

		array_push( $this->course_holder, $course );
	}

	/**
	* add a new day for system to keep track of
	**/
	function add_day( $day ) {
		array_push( $this->day_holder, $day );
	}

	/**
	* This sets the quarter/year information
	**/
	function set_year_quarter( $quarter, $year) {
		$this->quarter = $quarter;
		$this->year = $year;
	}


	/**
	* this determines the starting day that should be
	* shown on the schedule.  if there are no classes on monday - wednesday,
	* it would return thursday as the starting day
	*/
	function find_start_day() {
		global $CFG_DAYS_OF_WEEK;
		$retval = 0;

		if ( $this->EL_start_day != -1 ) {
			$retval = $this->EL_start_day;
		}
		else {

			$flag = false;

			$i = 0;
			while ( ($i < sizeof( $this->day_holder)) && !$flag ) {

				$day_object = $this->day_holder[$i];

				if ( ($day_object->has_nothing()) == -1 ) {
					$retval = $i;
					$flag = true;
				}
				$i++;
			}
		}
		return $retval;
	}

	/**
	* this returns the day that the schedule should end
	*/
	function find_end_day() {

		global $CFG_DAYS_OF_WEEK;
		$retval = 0;

		if ( $this->EL_end_day != -1 ) {
			$retval = $this->EL_end_day;
		}
		else {
			$flag = false;

			$i = sizeof($this->day_holder);
			while ( ($i > 0) && !$flag ) {

				$i--;
				$day_object = $this->day_holder[$i];

				if ( ($day_object->has_nothing()) == -1 ) {
					$retval = $i;
					$flag = true;
				}

			}

		$retval++;
		}
		return $retval;

	}

	/**
	* this returns the time the schedule should begin
	*/
	function find_start_time() {
		global $CFG_TIMES;

		if ( $this->EL_start_time != -1 ) {
			$retval = $this->EL_start_time;
		}
		else {

			# set start time to latest possible
			$retval = sizeof ( $CFG_TIMES) - 1;

			for ( $i = 0; $i < sizeof ($this->day_holder); $i++ ) {

				$this_start_time = $this->day_holder[$i]->first_time();

				if ( ($this_start_time != -1) && ($this_start_time < $retval)  ) {
					$retval = $this_start_time;
				}
			}
		}
		return $retval;
	}

	/**
	* this returns the time the schedule should end
	*/
	function find_end_time() {
		global $CFG_TIMES;

		if ( $this->EL_end_time != -1 ) {
			$retval = $this->EL_end_time;
		}
		else {
			$retval = 0;

			for ( $i = 0; $i < sizeof ($this->day_holder); $i++ ) {

				$this_end_time = $this->day_holder[$i]->last_time();						

				if ( ($this_end_time != -1) && ($this_end_time >= $retval)  ) {
					$retval = $this_end_time + 1;
				}
			}
		}
		
		return $retval;
	}

	/**
	* This figures out what the width of each column should be, it depends on
	* knowing how many days there are
	*/
	function generate_width() {
		$retval = 0;

		$start_day = $this->find_start_day();
		$end_day = $this->find_end_day();

		$retval = 90/($end_day - $start_day);

		return ( $retval );
	}


}

?>