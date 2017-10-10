<html>
<head>
<title>RIT Schedule Maker</title>
<link rel="stylesheet" href="http://schedule.gregbender.com/templates/style.css" type="text/css">
</head>
<body>
<table width="90%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="2%">&nbsp;</td>
    <!-- START BLOCK : days -->
     <td width="{DAY_WIDTH}%"><center><b>{THEDAY}</b></center></td>
    <!-- END BLOCK : days -->
</tr>

    <!-- START BLOCK : timerow -->
     <tr>
     	<td width="2%" nowrap>{TIMETEXT}</td>
     	{MONDAY}
     	{TUESDAY}
     	{WEDNESDAY}
     	{THURSDAY}
     	{FRIDAY}
     	{SATURDAY}
     	{SUNDAY}
    </tr>
  <!-- END BLOCK : timerow -->
  
</table>

<!-- INCLUDE BLOCK : save_online -->
          


</body>
</html>