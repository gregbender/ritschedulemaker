<html>
<head>
<title>RIT Schedule Maker</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META NAME="keywords" CONTENT="RIT, schedule, make, maker, html, graphical, course, SIS">
<META NAME="description" CONTENT="RIT Schedule Maker produces a graphical HTML schedule based on SIS course numbers.">
</head>
<body bgcolor="#FFA239">
<p align="center"><img src="logo.gif" width="660" height="88" alt="RIT Schedule Maker"></p>
<form method="POST" action="form_main.php">
  <div align="center">
    <center>
    <table border="1" width="90%" bordercolor="#000000" cellpadding="3" cellspacing="0">
      <tr>
        <td width="100%" bgcolor="#FFFFFF" bordercolor="#000000">

<table border="0" width="100%" cellspacing="5" cellpadding="0">
  <tr>
    <td width="50%" valign="top"><b><font color="#ff0000" size="6">1.</font><font color="#ff0000">
      <font color="#000000">Enter Course Numbers<br>
      </font></font></b>Enter your courses in the following fields, making sure
      they are in XXXX-XXX-XX format. For example: 4003-232-01
      <table border="0" width="100%" cellspacing="3" cellpadding="0">
        <tr>
          <td><input type="text" name="course_number_0" size="11" value="{COURSE_NUMBER_0}"></td>
          <td><input type="text" name="course_number_1" size="11" value="{COURSE_NUMBER_1}"></td>
          <td><input type="text" name="course_number_2" size="11" value="{COURSE_NUMBER_2}"></td>
        </tr>
        <tr>
          <td><input type="text" name="course_number_3" size="11" value="{COURSE_NUMBER_3}"></td>
          <td><input type="text" name="course_number_4" size="11" value="{COURSE_NUMBER_4}"></td>
          <td><input type="text" name="course_number_5" size="11" value="{COURSE_NUMBER_5}"></td>
        </tr>
        <tr>
          <td><input type="text" name="course_number_6" size="11" value="{COURSE_NUMBER_6}"></td>
          <td><input type="text" name="course_number_7" size="11" value="{COURSE_NUMBER_7}"></td>
          <td><input type="text" name="course_number_8" size="11" value="{COURSE_NUMBER_8}"></td>
        </tr>
      </table>
    </td>
    <td width="50%" valign="top"><b><font color="#ff0000" size="6">2.</font></b> <b>Choose
      Quarter and Schedule contents<br>
      </b>Choose the quarter of the schedule and the information you want
      displayed for each class.
      <table border="0" width="80%" cellspacing="0" cellpadding="3">
        <tr>
          <td width="100%" colspan="6">Quarter: <select size="1" name="quarter">
                <option value="20011" {20011}>Fall (20011)</option>
                <option value="20012" {20012}>Winter (20012)</option>
                <option value="20013" {20013}>Spring (20013)</option>
                <option value="20014" {20014}>Summer (20014)</option>
              </select></td>
        </tr>
        <tr>
          <td><input type="checkbox" name="display_course_name" value="ON" {CHECKED_NAME}></td>
          <td width="100%" nowrap>Course Name</td>
          <td><input type="checkbox" name="display_professor_name" value="ON" {CHECKED_PROF}></td>
          <td width="100%" nowrap>Professor</td>
          <td><input type="checkbox" name="display_time" value="ON" {CHECKED_TIME}></td>
          <td width="100%" nowrap>Course Time</td>          
        </tr>
        <tr>
          <td><input type="checkbox" name="display_building_room" value="ON" {CHECKED_LOCATION}></td>
          <td width="100%" nowrap>Location</td>
          <td><input type="checkbox" name="display_course_number" value="ON" {CHECKED_NUMBER}></td>
          <td width="100%" nowrap>Course Number</td>
          <td>&nbsp;</td>
          <td width="100%" nowrap>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="50%" valign="top"><font color="#ff0000" size="6"><b>3.</b></font> <b>Choose
      Elastic or Non-Elastic Schedule</b><br>
      An elastic schedule only shows you the time blocks pertaining to your
      classes. The non-elastic schedule allows you to control the output.
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><input type="radio" value="elastic" {ELASTIC_CHECKED} name="ELASTIC_OPTIONS"></td>
          <td width="100%" colspan="3">Choose Elastic (<i>recommended</i>)</td>
        </tr>
        <tr>
          <td><input type="radio" {NON_ELASTIC_CHECKED} name="ELASTIC_OPTIONS" value="not_elastic"></td>
          <td width="100%" colspan="3">Choose Non-Elastic</td>
        </tr>
        <tr>
          <td>Days:&nbsp;&nbsp;</td>
          <td width="25%"><select size="1" name="start_day">
                <option {START_DAY_0}>Monday</option>
                <option {START_DAY_1}>Tuesday</option>
                <option {START_DAY_2}>Wednesday</option>
                <option {START_DAY_3}>Thursday</option>
                <option {START_DAY_4}>Friday</option>
                <option {START_DAY_5}>Saturday</option>
                <option {START_DAY_6}>Sunday</option>
              </select> </td>
          <td width="25%" align="center">TO</td>
          <td width="100%"><select size="1" name="end_day">
                <option {END_DAY_0}>Monday</option>
                <option {END_DAY_1}>Tuesday</option>
                <option {END_DAY_2}>Wednesday</option>
                <option {END_DAY_3}>Thursday</option>
                <option {END_DAY_4}>Friday</option>
                <option {END_DAY_5}>Saturday</option>
                <option {END_DAY_6}>Sunday</option>
              </select></td>
        </tr>
        <tr>
          <td>Time:&nbsp;&nbsp;</td>
          <td width="25%"><select size="1" name="start_time">
          
          
          <!-- START BLOCK : start_time_options -->
	      <option {IS_START_SELECTED}>{START_TIME}</option>
       	  <!-- END BLOCK : start_time_options -->
          
          </select> </td>
          <td width="25%" align="center">TO</td>
          <td width="100%"><select size="1" name="end_time">

          <!-- START BLOCK : end_time_options -->
	      <option {IS_END_SELECTED}>{END_TIME}</option>
       	  <!-- END BLOCK : end_time_options -->

              </select>
          </td>
        </tr>
      </table>
    </td>
    <td width="50%" valign="top"><b><font color="#ff0000" size="6">4.</font></b> <b>Make
      Schedule or go to Advanced Options</b><br>
      Advanced options allow you to insert any other content into the scheduler
      on the basis of time and day.<BR><BR><BR>
      <div align="center">
        <center>
        <table border="0" width="80%" cellspacing="0" cellpadding="3">
          <tr>
            <td width="50%"><input type="submit" value="Make Schedule" name="submit"></td>
            <td width="50%">
            <!-- INCLUDE BLOCK : advanced_submit -->
            </td>
          </tr>
          <tr>
            <td width="100%" colspan="2"><font size="2"><b>NOTE</b>: May take
              several seconds to process.</font></td>
          </tr>
        </table>
        </center>
      </div>
    </td>
  </tr>
</table>

        </td>
      </tr>
    </table>
    </center>
  </div>
<!-- INCLUDE BLOCK : advanced -->
</form>
<hr noshade color="#000000" size="1" width="90%">
<div align="center">
  <center>
  <table border="0" width="90%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="100%">
        <p align="center"><b>RIT Schedule Maker brought to you by the <a href="http://www.ritwd.org">RIT
      Web Development Club</a>.<br>
      Project Members: <a href="http://www.gregbender.com">Greg Bender</a> - <a href="http://myenigmaself.gaiden.com">Michael
      Krauklis</a> - <a href="http://www.billknitter.com">Bill Knitter</a></b><br>
      <center><a href="mailto:gjb6676@rit.edu"><b>Questions or Comments?</b></a></center>
      <center><font color="#FFA239">
      <B>{COUNTER}</B></font></center>
      </td>
    </tr>
  </table>
  </center>
</div>
