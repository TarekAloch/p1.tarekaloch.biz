<?php
# Report information about our environment
if(!IN_PRODUCTION && !Utils::is_ajax()) {
	echo "<div onClick='this.style.display = \"none\";' style='cursor:pointer; position:fixed; z-index:999; background-color:yellow; padding:3px; bottom:0px; left:0px;'>";
	
		echo Time::display(Time::now());
		
		if(ENABLE_OUTGOING_EMAIL) echo "OUTGOING EMAILS ENABLED";
		else echo "&nbsp;&nbsp;No outgoing emails";
		
		if(REMOTE_DB) echo "&nbsp;&nbsp;<span style='color:red; font-weight:bold'>LIVE DB</span> ";
		else echo "&nbsp;&nbsp;Local DB;";		

	echo "</div>";
}
	
	