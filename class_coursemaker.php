<?
/**
* this class handles the creation of all course objects
* This handles the interface between SIS for creating
* courses, or creating generic courses or jobs
**/

class CourseMaker {

	var $connection;
	
	var $too_many_users;

	/**
	* CourseMaker Constructor
	**/
	function CourseMaker() {
		$this->connection = true;
		$this->too_many_users = false;
	}

	/**
	* this function makes non-course based events
	**/
	function make_event( $s_time, $e_time, $e_text, $darray) {

		$the_course = new Course();

		$s_time = str_replace( ":", "", $s_time);
		$s_time = str_replace( "M", "", $s_time);
		$s_time = str_replace( " ", "", $s_time);
		$e_time = str_replace( ":", "", $e_time);
		$e_time = str_replace( "M", "", $e_time);
		$e_time = str_replace( " ", "", $e_time);

		$e_text = str_replace( "\n", "<BR>", $e_text);

		$e_text = stripslashes( $e_text );

		$the_course->set_name( $e_text );
		$the_course->set_start_time($s_time);
		$the_course->set_end_time($e_time);


		for ( $i = 0; $i<sizeof( $darray ); $i++ ) {
			if ( $darray[$i] == 1 ) {
				$the_course->set_day_true($i);
			}
		}

		return( $the_course );
	}

	#this sends a command to delete the current token
	function del_token( $token ) {
	
		if ( $token != "") {
			$query = "http://ritmvs.rit.edu:83/XWEBCONV/CWBA/XSMBWEBM/XSTDEND.STR?CONVTOKEN=".$token."&NAVTO=EXIT";
			$sisfile = fopen( $query, r );
			fclose( $sisfile );
		}
	}

	/**
	* get_course
	*
	* This function takes in a coursenumber, year, and quater.
	* It connects to the SIS system, aquires a new token, and then
	* searches for for specified course.
	*
	* Courses are put into an array for processing
	*/
	function grabcourse( $quarter, $year, $coursenumber ) {

		# set all parameters that will be needed in the SIS url
		$sisaddress = "http://ritmvs.rit.edu:83/XWEBCONV/CWBA/XSMBWEBM/SR085.STR";
		$param1 = "CONVTOKEN=";
		$param2 = "QUARTER=".$quarter;
		$param3 = "YEAR=".$year;
		$param4 = "DISCIPLINE=";
		$param5 = "INIT=NO";
		$param6 = "PAGE=";

		# get the new token and
		# complete each of the parameters for the sis url
		$token = $this->newtoken();
		$param1 = $param1.$token;
		$page = 1;
		$discipline = substr( $coursenumber,0,4 );
		$param4 = $param4.$discipline;
		$coursenum = substr ($coursenumber, 5);

		# this is what will be returned
		$class_holder_array = array();

		# flag to know when to stop
		$stop = false;

		# loop through many times, so that we check each page
		while ( (!$stop) && ($page < 6) ) {

			# compile the sis url
			$sislink = $sisaddress."?".$param1."&".$param2."&".$param3."&".
						$param4."&".$param5."&".$param6."0".$page;

			# make the connection
			$courselist = fopen( $sislink, r );

			# make sure connection exists
			if ( $courselist ) {

				# loop until we are told to stop, or reach end of file
				while (!feof ($courselist) && (!$stop)) {

					# get an entire row of data
					$new_array = $this->get_entire_row( $courselist );

					# reset courselist var, so that it will terminate correctly
					$courselist = $new_array[0];
					$an_entire_row = $new_array[1];

					# if our current HTML row contains the course number we are looking for,
					# add it to the found courses, and then
					# check to see if there are additional rows after it
					if (strpos ($an_entire_row[1], $coursenum)) {

						array_push( $class_holder_array, $an_entire_row );

						# continue checking to see if additional times exist
						while ( (!$stop) && (!feof( $courselist)) ) {

							# now that we have a course number, keep on getting
							# more rows until we reach the next class (used to get
							# classes that have more than one row listings
							$second_array = $this->get_entire_row( $courselist );
							$courselist = $second_array[0];
							$an_entire_row2 = $second_array[1];

							# if we find a link with no text, then it is
							# an additional course we need to add
							if ( (strpos( $an_entire_row2[1], "></a>")) ) {
								array_push( $class_holder_array, $an_entire_row2 );
							}
							else {
								$stop = true;
							}
						}
					}
				}
			}
			else {
				$stop = true;
				$status = "BADCONNECTION";
			}

			$page++;
		}

		# if connection wasn't bad
		# actually create the course objects and return them
		if ( $status != "BADCONNECTION" ) {
			$ret_array = $this->string_to_course( $class_holder_array, $this->get_long_course_name($quarter, $year, $coursenumber, $token), $coursenumber );
		}

		# delete old token
		$this->del_token( $token );
		
		# close the connection
		fclose( $courselist );

		return( $ret_array );
	}


	/**
	* This takes in the arrays, and turns them into course objects
	*/
	function string_to_course ($the_array, $long_course_name, $course_number) {

		global $CFG_TIMES;

		# needed because the first object coming in comtains this items, but the rest dont
		$master_class_name = "";
		$master_professor = "";

		$ret_val = array();

		# initilize everything
		for ( $i = 0; $i < sizeof ( $the_array ); $i++ ) {

			if ( is_array($the_array[$i]) ) {

				$sub_array = $the_array[ $i ];

				for ($j = 0; $j < sizeof( $sub_array); $j++ ) {

					$sub_array[$j] = trim( $sub_array[ $j ] );
					$sub_array[$j] = strtolower($sub_array[ $j ]);
					$sub_array[$j] = strip_tags($sub_array[ $j ]);
				}
				$the_array[$i] = $sub_array;
			}
			else {
				$the_array[$i] = trim( $the_array[ $i ] );
				$the_array[$i] = strtolower($the_array[ $i ]);
				$the_array[$i] = strip_tags($the_array[ $i ]);
			}
		}


		for ( $i = 0; $i < sizeof ($the_array ); $i++ ) {

			if ( is_array($the_array[$i]) ) {

				$sub_array = $the_array[$i];

					// make the new course
					$course = new Course();

					$course->set_number( $course_number );

					if ( $sub_array[3] != "" ) {
						$master_professor = $sub_array[3];
					}

					$course->set_name( strtolower($long_course_name) );
					$course->set_instructor( $master_professor );

					//set days
					$daystring = $sub_array[13];

					for ( $k = 0; $k < strlen($daystring); $k++ ) {

						if ( $daystring[$k] == "m" ) {
							$course->set_day_true(0);
						}
						else if ( $daystring[$k] == "t" ) {
							$course->set_day_true(1);
						}
						else if ( $daystring[$k] == "w" ) {
							$course->set_day_true(2);
						}
						else if ( $daystring[$k] == "r" ) {
							$course->set_day_true(3);
						}
						else if ( $daystring[$k] == "f" ) {
							$course->set_day_true(4);
						}
						else if ( $daystring[$k] == "s" ) {
							$course->set_day_true(5);
						}
						else if ( $daystring[$k] == "u" ) {
							$course->set_day_true(6);
						}
					}
					$course->set_start_time( $sub_array[14] );
					$course->set_end_time( $sub_array[15] );
					$course->set_building( $sub_array[16] );
					$course->set_room( $sub_array[17] );

					array_push( $ret_val, $course );

			}
		}
		return( $ret_val );
	}

	/**
	* get_entire_row
	*
	* this function returns an entire html row, from <tr> to </tr>
	* as an array
	*/
	function get_entire_row( $courselist ) {

		# what we will return, contains a link to the courselist link
		# as well as the entire html row as an array
		$ret_array = array();

		# the entire html row as an array, goes from <tr> to </tr>
		$an_entire_row = array();
		
		# go until we find the start of a row
		while ( !$found ) {

			if (!feof ($courselist)) {

				$line = fgets( $courselist, 4096 );				
				$found = strpos ($line, "<tr");
			}
			else {
				$found = "true";
			}
		}
		$found = false;

		# now go until we find the end of a row
		while ( !$found ) {

			if (!feof ($courselist)) {
				array_push( $an_entire_row, $line );
				$line = fgets( $courselist, 4096 );
				$found = strpos ($line, "</tr>");
			}
			else {
				$found = "true";
			}
		}
			array_push( $an_entire_row, "</tr>");

		array_push( $ret_array, $courselist );
		array_push( $ret_array, $an_entire_row );

		return $ret_array;
	}

	###############################
	# getline
	# Searches through entire page, and returns the first line found with the word CONVTOKEN
	# sislink - the url to connect to
	# query_string - the string we are searching for, in this case CONVTOKEN
	function getline( $sislink, $query_str ) {

		$sisfile = fopen( $sislink, r );

		# if connection was made
		if ( $sisfile ) {

			while (!feof ($sisfile) && !$found) {

				$line = fgets($sisfile, 4096);
				
				// check for too many users on sis
				if ( strpos( $line, "Too many Users on S*I*S" ))  {
					$this->too_many_users = true;
				}
				
				$found = strpos ($line, $query_str);
			}
		}
		else {
			$this->connection = false;
			$line = "0";
		}

		return $line;
	} # end getline

	################################
	# newtoken
	# the function connects to SIS and get's a new token
	# returns - a string of the new token
	function newtoken() {

		$sislink = "http://ritmvs.rit.edu:83/XWEBCONV/CWBA/XSMBWEBM/SR085.STR?INIT=YES&CONVTOKEN=INIT";
		$query = "<input type=\"hidden\" name=\"CONVTOKEN";

		$line = $this->getline( $sislink, $query );
		$token = $this->parseline( $line );

		return $token;
	} # end newtoken

	################################
	# parseline
	# Parses the line with CONVTOKEN to find the value associated with that variable
	# line - the string containing convtoken
	function parseline( $line ) {

		$line = trim($line);

		#constant used to determine where value starts
		$begconstant = 7;
		#constant used at end of number
		$endconstant = "\">";

		$search = "value=\"";

		$valposition = strpos( $line, $search );
		#$valposition = $valposition + $begconstant;
		$valendposition = strpos( $line, $endconstant );
		
		#$valendposition += 1; #correction to get after number

		$number = substr ( $line, $valposition + 7);
		$number = substr( $number, 0, -2);

		return $number;
	} # end parseline



	/**
	* This gets the longer course name on SIS
	*/
	function get_long_course_name($quarter, $year, $coursenumber, $token) {

		$HTML_LINK = "http://ritmvs.rit.edu:83/XWEBCONV/CWBA/XSMBWEBM/SR086D.STR";

		$HTML_LINK = $HTML_LINK."?YEAR=".$year;
		$HTML_LINK = $HTML_LINK."&QUARTER=".$quarter;

		$HTML_LINK = $HTML_LINK."&DISCIPLINE=".substr( $coursenumber, 0, strpos($coursenumber,"-"));
		$HTML_LINK = $HTML_LINK."&CRS=".substr( $coursenumber, strpos($coursenumber,"-") +1);
		$HTML_LINK = $HTML_LINK."&CONVTOKEN=".$token;

		$goodlist = fopen( $HTML_LINK, r );
		
		while ((!feof ($goodlist) && (!$line_to_return))) {


				$line = fgets( $goodlist, 4096 );
				$found = strpos($line, "le:</b>");

			if ( $found ) {
				$theend = strrpos( $line, "</font") - 1;
			}
			if ( $found ) {
				$line_to_return = substr( $line, $found+8);
			}
			
		}
		return ( strtolower($line_to_return) );
	}

}
?>