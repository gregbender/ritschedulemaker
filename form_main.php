<?
/**
* This is the main function for the schedule maker
**/

// keeps track if an error occured
$error_occured = false;


if ( $HTTP_POST_VARS['advanced_submit']) {
	include('index.php');
}
else {

include('config.php');

if ( check_input( $HTTP_POST_VARS ) && verify_some_data( $HTTP_POST_VARS) ) {

	// create the system class
	$the_system = new System();

	// create the SIS-interface class
	$cmaker = new CourseMaker();

	// pass the form data to the SIS-interface class

	# hold courses
	$course_array = array();

	#hold courses that couldn't be found
	$course_nums_not_found = array();

	# if schedule is non-elastic, then set the correct vars
	if ( $HTTP_POST_VARS['ELASTIC_OPTIONS'] == "not_elastic") {
		$the_system->set_ELS($HTTP_POST_VARS['start_day'],
								$HTTP_POST_VARS['end_day'],
									$HTTP_POST_VARS['start_time'],
										$HTTP_POST_VARS['end_time']
							);
	}


	# set things to be displayed (like course name, etc)
	$the_system->set_displays(

		$HTTP_POST_VARS['display_course_name'],
		$HTTP_POST_VARS['display_course_number'],
		$HTTP_POST_VARS['display_professor_name'],
		$HTTP_POST_VARS['display_building_room'],
		$HTTP_POST_VARS['display_time']
	);


	# if there are any non-course events, add them
	for ( $i = 0; $i < sizeof ( $HTTP_POST_VARS ); $i++ ) {

		if ( strlen( $HTTP_POST_VARS['event_text_'.$i]) != 0) {

			$event_day_array = array(0,0,0,0,0,0,0);

			for ( $k = 0; $k < sizeof( $CFG_DAYS_OF_WEEK ); $k++ ) {
				if ($HTTP_POST_VARS['event_'.$CFG_DAYS_OF_WEEK[$k].'_'.$i] == "ON") {
					$event_day_array[$k] = 1;
				}
			}

			$returned_course = $cmaker->make_event( $HTTP_POST_VARS['event_start_time_'.$i],
								 $HTTP_POST_VARS['event_end_time_'.$i],
								 $HTTP_POST_VARS['event_text_'.$i],
								 $event_day_array);

			array_push( $course_array, $returned_course);
		}
	}



	# check each posted variable for a dash, and if it is, then grab that course

	for ( $i = 0; $i < sizeof ( $HTTP_POST_VARS ); $i++ ) {

		if (substr_count( $HTTP_POST_VARS['course_number_'.$i], "-") == 2) {

			// set too many users to false
			$cmaker->too_many_users = false;

			$thecourse = $cmaker->grabcourse(substr($HTTP_POST_VARS['quarter'],4),
										substr($HTTP_POST_VARS['quarter'],0,4),trim($HTTP_POST_VARS['course_number_'.$i]));
										
	
			// if too many users on SIS, show error
			if ( $cmaker->too_many_users == true ) {
				error_too_many();
			}
			
			if ( !$thecourse ) {
				array_push( $course_nums_not_found, $HTTP_POST_VARS['course_number_'.$i]);
			}
			else {

				for ( $l = 0; $l < sizeof ( $thecourse ); $l++ ) {
					array_push( $course_array, $thecourse[$l]);
				}
			}
		}
	}

	# print out course errors if there were any
	if ( sizeof ( $course_nums_not_found) > 0 ) {
		notify_error( $course_nums_not_found );
	}

	for ( $i = 0; $i < sizeof($course_array); $i++) {
		$the_system->add_course($course_array[$i]);
	}

	#put days in right place
	for ( $i = 0; $i < sizeof( $CFG_DAYS_OF_WEEK ); $i++) {

		$newday = new Day( $CFG_DAYS_OF_WEEK[$i] );
		$the_system->add_day($newday);
	}

	$the_system->process_days_of_week();

	// call System->add_course() for each course returned
	// by the SIS-interface class

	// create each day fo the week and add it by calling the
	// System->add_day() class

	## GENERATE COMPLETE, NOW PROCESS DATA

	// call System->process_days_of_week to move the data from the
	// course class into the day classes

	// call System->make_schedule() which will actually process
	// the template files and return the entire schedule as a string
	$serialized_object = urlencode(serialize( $the_system ));

	// this is set so we know this schedule was
	$the_system->just_created = true;

	$schedule = $the_system->make_schedule( $serialized_object );

	// this is set so we know this schedule was
	$the_system->just_created = false;

	// echo the string on if an error hasn't occured
	if ( !$error_occured ) {
		echo $schedule;
	}
}
}

/**
*  Make sure person isn't just pushing make schedule, there needs to be some data
*/
function verify_some_data( $post_vars ) {

	$retval = false;

	for ( $i = 0; $i < sizeof ( $post_vars ); $i++ ) {

		if ( $post_vars['course_number_'.$i] || $post_vars['event_text_'.$i] ) {
			$retval = true;
		}
	}

	if ( !$retval ) {
		$tpl = new TemplatePower( "templates/no_courses_entered.tpl" );
		$tpl->prepare();

		$tpl->printToScreen();
	}


	return ( $retval );
}

/**
* This checkes the posted course numbers to make sure they contain 2 dashes,
* if it doens't have two dashes, it generates an error screen
* returns true if input is ok, false otherwise
*/
function check_input( $post_vars ) {

	$retval = true;

	$tpl = new TemplatePower( "templates/course_error.tpl" );
	$tpl->prepare();

	$tpl->assign("ERROR_TYPE", "Unknown Course");

	for ( $i = 0; $i < sizeof ( $post_vars ); $i++ ) {

		if ((substr_count( $post_vars['course_number_'.$i], "-") != 2)
			&& ($post_vars['course_number_'.$i] != "")) {

			$retval = false;

			$tpl->newBlock("course_error");
		 	$tpl->assign( "COURSE_NUMBER", $post_vars['course_number_'.$i] );
		}
	}

	if ( !$retval ) {
		$tpl->printToScreen();
	}

	return ( $retval );
}


/**
* tells the user that there are too many people on SIS
*/
function error_too_many() {

	global $error_occured;
	
	# only display error, if no other errors occured
	if ( !$error_occured ) {
	
		$tpl = new TemplatePower( "templates/course_error2.tpl" );
		$tpl->prepare();
	
		$tpl->assign("ERROR_TYPE", "There are too many users on SIS.  <BR>Please try again later.");
	
		$tpl->printToScreen();
			$error_occured = true;
	}
}


/**
* Tells the user that one of the course numbers could not be found
*/
function notify_error( $course_nums_not_found ) {

	global $cmaker,$error_occured;

	# only display error if no other errors occured
	if ( !$error_occured ) {

		$tpl = new TemplatePower( "templates/course_error.tpl" );
		$tpl->prepare();

		if ( $cmaker->connection ) {

			$tpl->assign("ERROR_TYPE", "The following courses do not exist:");
	
			for ( $i = 0; $i < sizeof( $course_nums_not_found ); $i++ ) {

				$tpl->newBlock("course_error");
		
				$tpl->assign("COURSE_NUMBER", $course_nums_not_found[$i]);
			}
		}
		else {

			$tpl->assign("ERROR_TYPE", "RIT SIS Service is currently down,
										please try again later.");
	
		}


		$tpl->printToScreen();
		$error_occured = true;
	}
}


?>