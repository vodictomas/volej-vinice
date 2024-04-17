<?php

	$dbuser = "root";
	$dbpass = "root";
	$dbhost = "localhost";
	$dbdatabase = "volejbalek";
	
	define("TEST_QUERIES",0);
	define("LOG_QUERIES",0);
	
	//define("DB_MYSQL_ASSOC",MYSQL_ASSOC);

  $queryhistory = array();

function DB_Query($query, $ignoreBadResult = 0)
{

	global $DBHandle,$dbuser,$dbpass,$dbdatabase,$dbhost;
	
	if (!isset($DBHandle)) {
		$DBHandle = mysqli_connect($dbhost,$dbuser,$dbpass, $dbdatabase);
		if ($DBHandle==false) {
echo 'AAAAAA';die;
			//SaveError("Nelze se p�ipojit k datab�zov�mu serveru");
		}
		//if (@mysqli_select_db($dbdatabase,$DBHandle)==false) SaveError("Nelze vybrat p��slu�nou datab�zi");
	}
	$rs = @mysqli_query($DBHandle, $query);
	if ($rs == false && !$ignoreBadResult) {
		global $userid;
		SaveError("�patn� SQL dotaz!","\"$query\", Chyba: \"".
				mysql_error()."\"");
	}/* else if (TEST_QUERIES && strtolower(substr($query,0,6)) == "select") {
		$rs2 = @mysql_query("explain ".$query,$DBHandle);
		$rw = db_fetch($rs2);
		db_fr($rs2);
		$prochazel = $rw["rows"];
		$pocet = DB_NumRows($rs);
		$xxx = "";
		if ($pocet != 0 && $prochazel/$pocet > 10) $xxx = "!!!";

		$f = @fopen("d:/queries.txt","a");
		fputs($f,"$xxx ROWS:$prochazel,FOUND:$pocet,KEYS:{$rw["key"]}, ".$query.($rs == false ? "" : " (".@DB_NumRows($rs).")")."\n\n");
		fclose($f);
	}*/

	if (LOG_QUERIES) {
		$f = @fopen("d:/queries.txt","a");
		fputs($f,$query.($rs == false ? "" : " (".@DB_NumRows($rs).")")."\n\n");
		fclose($f);
	}

/*	global $queryhistory;
	
	$p["time"] = TimeCounterEnd(CNTDBQuery);
	$p["query"] = $query;

	$queryhistory[] = $p;*/

	return $rs;

}

function DB_Fetch(&$result)
{
	return mysqli_fetch_array($result,MYSQLI_ASSOC);
}

function DB_InsertID($res = false)
{
	return mysqli_insert_id();
}

function DB_NumRows(&$result)
{
	return mysqli_num_rows($result);
}

function DB_DataSeek(&$result, $rownumber)
{
	return mysqli_data_seek($result,$rownumber);
}

function DB_One_Row($query)
{

	$res = DB_Query($query);
	if ($res == false) $row = false; else {
		$row = DB_Fetch($res);
		DB_FR($res);
	}
	return $row;
	
}

/* free result */
function DB_FR(&$result)
{

	if ($result != false) {
		mysqli_free_result($result);	
	}
	
}

?>
