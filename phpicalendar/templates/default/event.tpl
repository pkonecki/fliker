<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset={CHARSET}">
	<title>{CAL}</title>
	<link rel="stylesheet" type="text/css" href="../templates/{TEMPLATE}/default.css">
</head>
<body>
<center>
	<table border="0" width="430" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{CAL_TITLE_FULL}</div></td>
		</tr>
		<tr>
			<td align="left" class="V12">
				<div style="margin-left: 10px; margin-bottom:10px;">
					<p><b>{L_SUMMARY}</b>:{EVENT_TEXT} - <span class="V9">(<i>{EVENT_TIMES}</i>)</span></p>
					<!-- switch description on -->
					<p><b>{L_DESCRIPTION}</b>:{DESCRIPTION}</p>
					<!-- switch description off -->
					<p>
					<!-- switch organizer on -->
					<b>{L_ORGANIZER}</b>: {ORGANIZER}<br />
					<!-- switch organizer off -->
					<!-- switch attendee on -->
					<b>{L_ATTENDEE}</b>: {ATTENDEE}<br />
					<!-- switch attendee off -->
					<!-- switch status on -->
					<b>{L_STATUS}</b>: {STATUS}<br />
					<!-- switch status off -->
					<!-- switch location on -->
					<b>{L_LOCATION}</b>: {LOCATION}<br />
					<!-- switch location off -->
					<!-- switch url on -->
					<b>{L_URL}</b>: {URL}<br />
					<!-- switch url off -->
					<!-- switch event download on -->
					{EVENT_DOWNLOAD}<br />
					<!-- switch event download off -->
					</p>
				</div>
			</td>
		</tr>
	</table>
</center>
</body>
</html>

