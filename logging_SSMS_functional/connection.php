<?php
$serverName = "UK-DIET-SQL-T1"; //serverName\instanceName

//Database name = Enter username (UID) and password (PWD) for your group
$connectionInfo = array( "Database"=>"Group6_DB", "UID"=>"UserGroup6", "PWD"=>"UpqrxGOkJdQ64MFC");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established to database.<br />"; 
	 $server_info = sqlsrv_server_info($conn);
	 if( $server_info )
		{			
			/**foreach( $server_info as $key => $value) {
			   echo $key.": ".$value."<br />";
			}**/
		} else {
			  die( print_r( sqlsrv_errors(), true));
		}																			
	 
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
?>