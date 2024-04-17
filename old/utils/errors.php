<?php

	set_error_handler("ErrorHandler");

	$warningsoff = 0;

	define("shownotices",1);
	$ignored_errors = array(0=> 2048);

function SaveError()
{
	
	global $myheadersaved,$userid,$insaveerror,$connid,$authenticated,$DecodedVars;

	if ($insaveerror) die();
	$insaveerror = true;
	
	$text = func_get_arg(0);
	if (func_num_args() > 1) $txt2 = func_get_arg(1); else $txt2 = "";
	SaveHtmlHead("Chyba scriptu!");
//	InsertTopLayout();

	if (! (DEBUG == 1)) $txt2 = "";
	echo "<div class=\"scripterror\">";
	if (strlen($txt2) > 0) $dst = $text." ($txt2)"; else $dst = $text;
	echo "<h2>Chyba scriptu</h2>";
	echo "<p>P�i prov�d�n� scriptu nastala bohu�el n�sleduj�c� chyba:<br><br>".$dst;
	if (DEBUG == 1) {
		global $HTTP_POST_VARS;
		if (is_array($DecodedVars)) {
			echo "<br><br><strong>Detaily:</strong>".$txt2."<br><br><strong>Intern� prom�nn�:</strong><br>";
			reset($DecodedVars);
			while (list($k,$v) = each($DecodedVars)) {
				echo $k." -&gt; ".$v."<br>";
			}
		}

		if (is_array($HTTP_POST_VARS)) {
			echo "<br><br><strong>POST prom�nn�:</strong><br>";
			reset($HTTP_POST_VARS);
			while (list($k,$v) = each($HTTP_POST_VARS)) {
				echo $k." -&gt; ".$v."<br>";
			}
		}

		$included_files = get_included_files();

		echo "<br><br><strong>Included files:</strong><br>";
		foreach($included_files as $filename) {
    			echo "$filename<br>";
		}

	}
	echo "<p>Pokud se situace bude opakovat, napi�te mi, pros�m,";
	echo "<a href=\"mailto:".antiSpamMail(AUTHOR_MAIL)."\">mail</a>. Neobnovujte tuto str�nku, chyba by ";
	echo "se toti� nejsp�e objevila znovu,";
	echo "zkuste rad�ji <a href=\"".BaseURL."\">znovu vstoupit do syst�mu</a>.";
	echo " Luk� Valenta";
	echo "</div>";
	flush();
	//WriteLog("Chybov� v�pis u�ivateli: $dst",LOGError);
	exit; 
	
}

function ErrorHandler ($errno, $errstr, $errfile, $errline)
{
	global $ignored_errors;
	$errrep = error_reporting();
	if ($errrep==0) return;
	if ((!shownotices) & ($errrep & E_NOTICE != 0)) return;
	
	if (in_array($errno,$ignored_errors)) {
		return;
	}
	
	if ($errrep & E_NOTICE != 0) {
		global $warningsoff;
		if ($warningsoff) return;
		echo "<div class=\"scripterror\">";
		echo "<strong>Upozorn�n� interpreteru PHP</strong>:  #$errno: \"$errstr\"",", soubor \"$errfile\", ".
	 		"��dka #$errline";
	 	echo "</div>";
		return;
	}
	
	global $inerrorhandler;
	if ($inerrorhandler) die();
	$inerrorhandler = true;
	
	SaveError("Chyba generovan� interpreterem PHP: #$errno: \"$errstr\"","soubor \"$errfile\", ".
	 "��dka #$errline");
	
}

?>
