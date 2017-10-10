<?
	include('class.TemplatePower.inc.php');
	include('config_times.php');

	$tpl = new TemplatePower( "templates/index.tpl" );


	if ( $HTTP_POST_VARS['advanced_submit'] ) {

		$tpl->assignInclude( "advanced_submit", "templates/blank_advanced.tpl" );
		$tpl->assignInclude( "advanced", "templates/advanced.tpl" );
		$tpl->prepare();

		include('counter/counter.php');
		$counter_string = get_counter();
		$tpl->assign("COUNTER", $counter_string);

		$tpl->assign("COURSE_NUMBER_0", $HTTP_POST_VARS['course_number_0']);
		$tpl->assign("COURSE_NUMBER_1", $HTTP_POST_VARS['course_number_1']);
		$tpl->assign("COURSE_NUMBER_2", $HTTP_POST_VARS['course_number_2']);
		$tpl->assign("COURSE_NUMBER_3", $HTTP_POST_VARS['course_number_3']);
		$tpl->assign("COURSE_NUMBER_4", $HTTP_POST_VARS['course_number_4']);
		$tpl->assign("COURSE_NUMBER_5", $HTTP_POST_VARS['course_number_5']);
		$tpl->assign("COURSE_NUMBER_6", $HTTP_POST_VARS['course_number_6']);
		$tpl->assign("COURSE_NUMBER_7", $HTTP_POST_VARS['course_number_7']);
		$tpl->assign("COURSE_NUMBER_8", $HTTP_POST_VARS['course_number_8']);

		$tpl->assign("20011", "");
		$tpl->assign("20012", "");
		$tpl->assign("20013", "");
		$tpl->assign("20014", "");
		$tpl->assign($HTTP_POST_VARS['quarter'], "selected");

		if ( $HTTP_POST_VARS['display_course_name'] == "ON" ) {
			$tpl->assign("CHECKED_NAME", "checked");
		}
		else {
			$tpl->assign("CHECKED_NAME", "");
		}


		if ( $HTTP_POST_VARS['display_professor_name'] == "ON" ) {
			$tpl->assign("CHECKED_PROF", "checked");
		}
		else {
			$tpl->assign("CHECKED_PROF", "");
		}

		if ( $HTTP_POST_VARS['display_building_room'] == "ON" ) {
			$tpl->assign("CHECKED_LOCATION", "checked");
		}
		else {
			$tpl->assign("CHECKED_LOCATION", "");
		}

		if ( $HTTP_POST_VARS['display_course_number'] == "ON" ) {
			$tpl->assign("CHECKED_NUMBER", "checked");
		}
		else {
			$tpl->assign("CHECKED_NUMBER", "");
		}

		if ( $HTTP_POST_VARS['display_time'] == "ON" ) {
			$tpl->assign("CHECKED_TIME", "checked");
		}
		else {
			$tpl->assign("CHECKED_TIME", "");
		}


		if ( $HTTP_POST_VARS['ELASTIC_OPTIONS'] == "elastic" ) {
			$tpl->assign("ELASTIC_CHECKED", "checked");
			$tpl->assign("NON_ELASTIC_CHECKED", "");
		}
		else {
			$tpl->assign("ELASTIC_CHECKED", "");
			$tpl->assign("NON_ELASTIC_CHECKED", "checked");
		}

		$tpl->assign("START_DAY_0", "");
		$tpl->assign("START_DAY_1", "");
		$tpl->assign("START_DAY_2", "");
		$tpl->assign("START_DAY_3", "");
		$tpl->assign("START_DAY_4", "");
		$tpl->assign("START_DAY_5", "");
		$tpl->assign("START_DAY_6", "");

		if ( $HTTP_POST_VARS['start_day'] == "Monday" ) {
			$tpl->assign("START_DAY_0", "selected");
		}
		else if ( $HTTP_POST_VARS['start_day'] == "Tuesday" ) {
			$tpl->assign("START_DAY_1", "selected");
		}
		else if ( $HTTP_POST_VARS['start_day'] == "Wednesday" ) {
			$tpl->assign("START_DAY_2", "selected");
		}
		else if ( $HTTP_POST_VARS['start_day'] == "Thursday" ) {
			$tpl->assign("START_DAY_3", "selected");
		}
		else if ( $HTTP_POST_VARS['start_day'] == "Friday" ) {
			$tpl->assign("START_DAY_4", "selected");
		}
		else if ( $HTTP_POST_VARS['start_day'] == "Saturday" ) {
			$tpl->assign("START_DAY_5", "selected");
		}
		else if ( $HTTP_POST_VARS['start_day'] == "Sunday" ) {
			$tpl->assign("START_DAY_6", "selected");
		}

		$tpl->assign("END_DAY_0", "");
		$tpl->assign("END_DAY_1", "");
		$tpl->assign("END_DAY_2", "");
		$tpl->assign("END_DAY_3", "");
		$tpl->assign("END_DAY_4", "");
		$tpl->assign("END_DAY_5", "");
		$tpl->assign("END_DAY_6", "");

		if ( $HTTP_POST_VARS['end_day'] == "Monday" ) {
			$tpl->assign("END_DAY_0", "selected");
		}
		else if ( $HTTP_POST_VARS['end_day'] == "Tuesday" ) {
			$tpl->assign("END_DAY_1", "selected");
		}
		else if ( $HTTP_POST_VARS['end_day'] == "Wednesday" ) {
			$tpl->assign("END_DAY_2", "selected");
		}
		else if ( $HTTP_POST_VARS['end_day'] == "Thursday" ) {
			$tpl->assign("END_DAY_3", "selected");
		}
		else if ( $HTTP_POST_VARS['end_day'] == "Friday" ) {
			$tpl->assign("END_DAY_4", "selected");
		}
		else if ( $HTTP_POST_VARS['end_day'] == "Saturday" ) {
			$tpl->assign("END_DAY_5", "selected");
		}
		else if ( $HTTP_POST_VARS['end_day'] == "Sunday" ) {
			$tpl->assign("END_DAY_6", "selected");
		}


	for ( $i = 0; $i < sizeof( $CFG_TIMES ); $i++) {

	    $tpl->newBlock("start_time_options");

	    $tpl->assign("IS_START_SELECTED", "" );

	    if ($HTTP_POST_VARS['start_time'] == $CFG_TIMES[$i]) {
	    	$tpl->assign("IS_START_SELECTED", "selected" );
	    }

		$tpl->assign("START_TIME", $CFG_TIMES[$i] );
	}

	for ( $i = 0; $i < sizeof( $CFG_TIMES ); $i++ ) {

	    $tpl->newBlock("end_time_options");

	    $tpl->assign("IS_END_SELECTED", "" );

	    if ($HTTP_POST_VARS['end_time'] == $CFG_TIMES[$i]) {
	    	$tpl->assign("IS_END_SELECTED", "selected" );
	    }


	    $tpl->assign("END_TIME", $CFG_TIMES[$i] );
	}

	}
	else {
		$tpl->assignInclude( "advanced_submit", "templates/submit_button.tpl" );
		$tpl->assignInclude( "advanced", "templates/blank_advanced2.tpl" );
		$tpl->prepare();

		include('counter/counter.php');
		$counter_string = get_counter();
		$tpl->assign("COUNTER", $counter_string);

		$tpl->assign("COURSE_NUMBER_0", "");
		$tpl->assign("COURSE_NUMBER_1", "");
		$tpl->assign("COURSE_NUMBER_2", "");
		$tpl->assign("COURSE_NUMBER_3", "");
		$tpl->assign("COURSE_NUMBER_4", "");
		$tpl->assign("COURSE_NUMBER_5", "");
		$tpl->assign("COURSE_NUMBER_6", "");
		$tpl->assign("COURSE_NUMBER_7", "");
		$tpl->assign("COURSE_NUMBER_8", "");

		$tpl->assign("20011", "");
		$tpl->assign("20012", "");
		$tpl->assign("20013", "selected");
		$tpl->assign("20014", "");

		$tpl->assign("CHECKED_NAME", "checked");
		$tpl->assign("CHECKED_PROF", $HTTP_POST_VARS['display_professor_name']);
		$tpl->assign("CHECKED_LOCATION", "checked");
		$tpl->assign("CHECKED_NUMBER", $HTTP_POST_VARS['display_course_number']);
		$tpl->assign("CHECKED_TIME", $HTTP_POST_VARS['display_time']);

		$tpl->assign("NON_ELASTIC_CHECKED", "");
		$tpl->assign("ELASTIC_CHECKED", "checked");

		$tpl->assign("START_DAY_0", "selected");
		$tpl->assign("START_DAY_1", "");
		$tpl->assign("START_DAY_2", "");
		$tpl->assign("START_DAY_3", "");
		$tpl->assign("START_DAY_4", "");
		$tpl->assign("START_DAY_5", "");
		$tpl->assign("START_DAY_6", "");


		$tpl->assign("END_DAY_0", "");
		$tpl->assign("END_DAY_1", "");
		$tpl->assign("END_DAY_2", "");
		$tpl->assign("END_DAY_3", "");
		$tpl->assign("END_DAY_4", "selected");
		$tpl->assign("END_DAY_5", "");
		$tpl->assign("END_DAY_6", "");

	for ( $i = 0; $i < sizeof( $CFG_TIMES ); $i++) {

    $tpl->newBlock("start_time_options");

	    $tpl->assign("IS_START_SELECTED", "" );

	    if ( $CFG_TIMES[$i] == "10:00 AM") {
	    $tpl->assign("IS_START_SELECTED", "selected" );
	    }

		$tpl->assign("START_TIME", $CFG_TIMES[$i] );
	}

	for ( $i = 0; $i < sizeof( $CFG_TIMES ); $i++ ) {

    $tpl->newBlock("end_time_options");
	    $tpl->assign("IS_END_SELECTED", "" );

	    if ( $CFG_TIMES[$i] == "6:00 PM") {
		   $tpl->assign("IS_END_SELECTED", "selected" );
	    }

	    $tpl->assign("END_TIME", $CFG_TIMES[$i] );
	}

	}

	$tpl->printToScreen();


?>