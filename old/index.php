<?php

require_once __DIR__ . "/../old/utils/db.php";

	$dny = array(0=>"Neděle","Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota");
  
	$zakazaneDatumy = array("25.10","08.11","06.12","27.12","17.1","31.1","14.3","04.4"); //"02.6","09.6","16.6",
	$lidi = array(
  "A-Jarda",
  "A-Martin",
  "A-Tomáš V.",
  "A-Majda",
  "A-Štěpán",
  "A-Evča",
  "A-Katka Voč.",
  "A-Tomáš R.",
  "A-Zuzka",
  "A-Huďák",
  "B-Tereza",
  "B-Tomáš",
  "B-Vojta",
  "B-Jakub",
  "B-Míša",
  "B-Ivana",
  "B-Lucka",
  "B-Hory",
  "B-Bergy",
  "B-Michal",
  "ZA-Luďa", "ZA-Winky","ZA-Ondra","ZA-Michal","ZA-Kasička","ZA-Gali","ZA-Agáta","ZA-Hanka Ř.","ZA-Patrik","ZA-Mirka");
	

	/* vterina v tydnu, kdy se volejbal kona */
	$denVTydnuVeVterinach = 0*3600*24;
	$casVeVterinach = 20*3600+0*60;
  $casVTydnu = $casVeVterinach+$denVTydnuVeVterinach;
	$kolikDopreduPsat = 0; // o kolik dopredu psat nadpis 
	$dobaTrvani = 120;
	$kolikDopredu = 3;
	$kde = "na Vinicích, tělocvična 7. ZŠ.";//"na Lokádě";//"na Vinicích, tělocvična 7. ZŠ.";//"na Lokomotivě";//"antuka na Slavii na Borech.";//"na Borech - přímo u kolejí v Máchovce naproti Studni.";//"na Vinicích, tělocvična 7. ZŠ.";//"na Borech - přímo u kolejí v Máchovce naproti Studni.";//"antuka na Slavii na Borech.";//"na Borech - přímo u kolejí v Máchovce naproti Studni.";//"- hala ZČU, Bory.";//"- tělocvična 16. ZŠ a MŠ Plzeň, Americká třída 30.";//"tělocvična gymnázia Luďka Pika, Plzeň";//na Slavii na Borech;//"na Borech - přímo u kolejí v Máchovce naproti Studni";//tělocvična gymnázia Luďka Pika, Plzeň";na Slavii na Borech
	
	/* 7 hodin pred volejem je deadline */
	$deadlineBefore = 0;
	$minPlayers = 7;

	define("STAV_UNKNOWN",-1);
	define("STAV_NO",0);
	define("STAV_YES",1);

function SaveNoCache()
{
	header("Expires: ".date("D, j M Y H:i:s",(time()-3600)-60*10)." GMT");              // Date in the past
	header("Last-Modified: ".date("D, j M Y H:i:s",(time()+3600))." GMT");              // Date in the future
	header("Cache-Control: no-cache, must-revalidate");            // HTTP/1.1
	header("Pragma: no-cache");                                    // HTTP/1.0

}

function umazLetniCas($datum) {
  $pomDatum = getdate($datum);
		
	return ($datum-(($pomDatum["hours"]*3600+$pomDatum["minutes"]*60+$pomDatum["seconds"])));
}

function doplnNuly($str) {
	if (strlen($str) == 1) return "0".$str;
	 else if (strlen($str) >= 2) return $str;
	 else if (strlen($str) == 0) return "00";
}

function pridejTydenKDatumu($datum) {
 //return $datum += 3600*24*7;
 return strtotime(date("Y-m-d H:i:s", $datum). ' +1 week');
 //idate("U",$datum)

}

function denACas($casVTydnu) {

	global $dny;
	if ($casVTydnu < 0) {
		$casVTydnu = $casVTydnu + 3600*24*7;
    //$casVTydnu = strtotime($casVTydnu. ' + 1 days'); 
	}
	return $dny[ ($casVTydnu / (3600*24) + 1) % 7]." ".
		doplnNuly( ($casVTydnu / 3600) % 24 ).":".
		doplnNuly( ($casVTydnu / 60) % 60 );	

} 

function testDatum($datum) {

  global $zakazaneDatumy;

  $ok = true; 

  for($d = 0; $d < count($zakazaneDatumy); $d++) {
    if($zakazaneDatumy[$d] == date("j.n",$datum)) {
      $ok = false;
      break; 
    }
  }

  return $ok;
}

function vypisTabulku() {

	global $lidi,$casVTydnu,$kolikDopredu,$dny,$kde,$deadlineBefore,$minPlayers,$kolikDopreduPsat,$denVTydnuVeVterinach,$casVeVterinach;

	// kdy je dalsi volejbal
	$dateInfo = getdate(time());
	$prvniDatum = (time())-( ( ($dateInfo["wday"]+6)%7) * 3600*24 )-$dateInfo["hours"]*3600-$dateInfo["minutes"]*60-
		$dateInfo["seconds"] + $denVTydnuVeVterinach /*+$casVTydnu*/;
	/* ted je v "prvniDatum" volejbal TENTO tyden */
	//$prvniDatum -= 3600*24*7*2;
	if ($prvniDatum + (3600*24) < time()) {
		//$prvniDatum += 3600*24*7;
    $prvniDatum = pridejTydenKDatumu($prvniDatum);
		$prvniDatum = umazLetniCas($prvniDatum);
	}
  //$prvniDatum = $prvniDatum - (28*24*60*60);
	/* vynuluju */
	for ($clovek = 0; $clovek < count($lidi); $clovek++) {	
		$datum = $prvniDatum;
		for ($pocet = 0; $pocet < $kolikDopredu; $pocet++) {
			$tab[$clovek][$datum] = STAV_UNKNOWN;
			$tab_pivo[$clovek][$datum] = STAV_UNKNOWN;
			//$datum += 3600*24*7;
      $datum = pridejTydenKDatumu($datum);
			$datum = umazLetniCas($datum);
		}
	}
	

	$res = db_query("select * from volejbal where datum>=".$prvniDatum." order by datum, jmeno");
	while ($row = db_fetch($res)) {
	
	  $pomDatum = getdate($row["datum"]);
		//$tab[$row["jmeno"]][$row["datum"]] = $row["stav"];
		
		$tab[$row["jmeno"]][($row["datum"]-(($pomDatum["hours"]*3600+$pomDatum["minutes"]*60+$pomDatum["seconds"])))] = $row["stav"];
		  
		$tab_pivo[$row["jmeno"]][($row["datum"]-(($pomDatum["hours"]*3600+$pomDatum["minutes"]*60+$pomDatum["seconds"])))] = $row["stav_pivo"];
    
    //pocita se spatne
    $pocetZapisuVDen[($row["datum"]-(($pomDatum["hours"]*3600+$pomDatum["minutes"]*60+$pomDatum["seconds"])))] =+1; 
    
	}
	db_fr($res);

	/* prevedu na jmena, abych mohl seradit */
	for ($clovek = 0; $clovek < count($lidi); $clovek++) {	
		$datum = $prvniDatum;
		for ($pocet = 0; $pocet < $kolikDopredu; $pocet++) {
			$stab[$lidi[$clovek]][$datum] = $tab[$clovek][$datum]; 
			$stab[$lidi[$clovek]]["clovek"] = $clovek;
			//$datum += 3600*24*7;
      $datum = pridejTydenKDatumu($datum);
			$datum = umazLetniCas($datum);
		}
	}
	
	/* prevedu na jmena, abych mohl seradit */
	for ($clovek = 0; $clovek < count($lidi); $clovek++) {	
		$datum = $prvniDatum;
		for ($pocet = 0; $pocet < $kolikDopredu; $pocet++) {
			$stab_pivo[$lidi[$clovek]][$datum] = $tab_pivo[$clovek][$datum]; 
			$stab_pivo[$lidi[$clovek]]["clovek"] = $clovek;
			//$datum += 3600*24*7;
      $datum = pridejTydenKDatumu($datum);
			$datum = umazLetniCas($datum);
		}
	}
	
	ksort($stab);
	ksort($stab_pivo);
	
?>

<style>
body {
	
  font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 1pt;
	padding: 3pt;
	text-align: center;

}
/*lightgreen*/
.yes    {
	text-decoration:none; background-color: white; 
}
.no     {
	text-decoration:none; background-color: white; 
}
.nevim     {
	text-decoration:none; background-color: #FFFFFF; 
}

.yes_pivo    {
	text-decoration:none; background-color: white; 
}
.no_pivo     {
	text-decoration:none; background-color: white; 
}
.nevim_pivo  {
	text-decoration:none; background-color: white;
}

td	{
	text-align: center;
}
th	{
	padding-left: 3pt;
	padding-right: 3pt;
}
h2	{
	background-color: rgb(204,255,255);
	padding:4pt 0pt;
	border-left: 3ex solid rgb(102,255,255);
	color: rgb(0,0,153);
}
a, a:visited {
	color: blue;
}

img {border:0px solid gray;}
 
table.rozpis { border-width: 2px; border-style: solid; border-spacing: 0px; border-color: #000000; margin-bottom: 5px;}
table.rozpis td { border: 1px solid #000000; }
td.rozpis_dny { font-weight: bolder; background-color: lightblue; width: 75px; text-align: center; padding: 3px; padding-right: 6px; }
td.rozpis_dny_mimo {font-weight: bolder; background-color: #DD0000; width: 75px; text-align: center; padding-left: 6px; padding-right: 6px; }
td.rozpis_clovekA { background-color: orange;  width: 150px; text-align: center; padding: 4px;}
td.rozpis_clovekB { background-color: rgb(0,204,255);  width: 150px; text-align: center; padding: 4px;}
td.rozpis_clovekC { background-color: rgb(204,153,255);  width: 150px; text-align: center; padding: 4px;}
td.rozpis_clovekZ { background-color: rgb(153,255,102);  width: 150px; text-align: center; padding: 4px;}
td.rozpis_ct { width: 50px; text-align: center; padding: 0px; }
td.rozpis_pivo_ct { width: 25px; text-align: center; padding: 0px;font-size: 80%; background-color: white;}
td.volejbal_neni { width: 75px; text-align: center; padding: 4px; font-size: 80%;}

.main {

  margin: 0 auto;
  width: 985px;
  text-align: left;
}

table.rozpis td.hotovo {
	font-weight: bolder;
	border-right: 2px solid blue;
	border-left: 2px solid blue;
}

.info { padding: 5pt; }
.novinkaObr { margin: 2px; }


</style>

<?php

  $stavtxt = array(-1=>"<img src=\"zdroje/icons/otaznik2.jpg\"/>",0=>"<img src=\"zdroje/icons/ft_disabled.gif\"/>",1=>"<img src=\"zdroje/icons/fajfka3.jpg\"/>");
	$stavsetco = array(-1=>1, 0=>-1, 1=>0);
	$stavclass = array(-1=>"nevim", 0=>"no", 1=>"yes");
	
	$stav_pivo_txt = array(-8=>"<img id='img_-8' src=\"zdroje/icons/prace3.png\"/>",-7=>"<img id='img_-7' src=\"zdroje/icons/kocarek2.jpg\"/>",-6=>"<img id='img_-6' src=\"zdroje/icons/invalida3.png\"/>",-5=>"<img id='img_-5' src=\"zdroje/icons/nota.jpg\"/>",-4=>"<img id='img_-4' src=\"zdroje/icons/nemoc.jpg\"/>",-3=>"<img id='img_-3' src=\"zdroje/icons/ski2.jpg\"/>",-2=>"<img id='img_-2' src=\"zdroje/icons/kniha3.jpg\"/>",-1=>"<img id='img_-1' src=\"zdroje/icons/otaznik3.jpg\"/>",0=>"<img id='img_0' src=\"zdroje/icons/postel2.jpg\"/>",1=>"<img id='img_1' src=\"zdroje/icons/pivo3b.jpg\"/>",2=>"<img id='img_2' src=\"zdroje/icons/kofola3r.jpg\"/>",3=>"<img id='img_3' src=\"zdroje/icons/hranolky4.jpg\"/>",4=>"<img id='img_4' src=\"zdroje/icons/dortik.jpg\"/>",5=>"<img id='img_5' src=\"zdroje/icons/nonstop.jpg\"/>",6=>"<img id='img_6' src=\"zdroje/icons/moch2.jpg\"/>",7=>"<img id='img_7' src=\"zdroje/icons/mochnon.gif\"/>",8=>"<img id='img_8' src=\"zdroje/icons/invalidaSPivem3.jpg\"/>", 9=>"<img id='img_9' src=\"zdroje/icons/vino.png\"/>");
	//$stav_pivo_setco = array(-1=>1, 0=>-1, 1=>0);
	$stav_pivo_class = array(-8=>"nevim_pivo",-7=>"nevim_pivo",-6=>"nevim_pivo",-5=>"nevim_pivo",-4=>"nevim_pivo",-3=>"nevim_pivo",-2=>"nevim_pivo",-1=>"nevim_pivo", 0=>"no_pivo", 1=>"yes_pivo",2=>"yes_pivo",3=>"yes_pivo",4=>"yes_pivo",5=>"yes_pivo",6=>"yes_pivo",7=>"yes_pivo",8=>"nevim_pivo",9=>"yes_pivo");
	$a = false;$b=false;$c=false;$z=false;
	
	echo "<br /><h2>&nbsp;<img src=\"zdroje/icons/icon.png\"/>&nbsp; Volejbal $kde - ".denACas($casVTydnu-$kolikDopreduPsat)."</h2>";	
  echo "<span style=\"margin-left: 10px;\">Stránka s <a target=\"_blank\" href=\"/zdroje/html/dochazka.htm\">docházkou a platbama</a> - číslo konta <span style='font-weight:bold;'>51-9126890257/0100</span><br/><br/> </span>"; //Na četná přání sem dávám i číslo konta <span style='font-weight:bold;'>51-9126890257/0100</span>  <img width='15' height='15' border='0' src='zdroje/icons/smileys/4.gif'><br/><br/>
  //echo "<span style='font-size:170%; color:red; font-weight:bold;'>Michale nezapomeň na kámošku <img width='15' height='15' border='0' src='zdroje/icons/smileys/4.gif'/></span><br /><br />";
  echo "<span style=\"margin-left:10px;margin-bottom:10px;font-weight: bold;\">Novinka</span>:<br/><br/>";
  //echo "<div style='text-align: center;'><img width='55%' height='35%' src='zdroje/jpeg/koronak.jpeg' /></div>";
  //echo "<span style=\"margin-left:25px;\"><span style=\"font-weight:bold;color:red;\">!!!Pozor změna-antuka na Slavii přes léto bude až od 19:00,páč to dost lidí na šestou nestíhá,tak snad se aspoň parkrát sejdem <img width='15' height='15' border='0' src='zdroje/icons/smileys/4.gif'/.</span></span><br /><br />";
  //echo "<span style=\"margin-left:25px;\"><span style=\"font-weight:bold;color:blue;\">Jelikož nám zavřeli na léto koleje, tak budou čtvrteční volejbaly od 4.07.2013 na antuce na Slavii od 19:00.</span></span><br /><br />";
  //echo "<span style=\"margin-left:25px;\"><span style=\"font-weight:bold;color:red;\">V hale začínáme od pondělí 30.září</span></span><br />";
  
  //echo "<span style=\"margin-left:25px;\"><span style=\"font-weight:bold;\"><a href='zdroje/obrazky/hrajici_zegoni_vidov_2013_v2.jpg' target='_blank'>Hrající Zegoni dobyli jižní Čechy</a> a vyhráli turnaj Vidov 2013 - dalším 13ti jihočeským mančaftům pravděpodobně zhořkla \"plzeň\" <img width='15' height='15' border='0' src='zdroje/icons/smileys/4.gif'/> </span></span><br />";
  //echo "<span style=\"margin-left:25px;\"><span style=\"font-weight:bold;color:blue;\">Jelikož nám zavřeli na léto koleje, tak budou čtvrteční volejbaly od 12.07.2012 na antuce na Slavii od 18:00.</span></span><br />";
  //echo "<span style=\"margin-left:25px;\"><span style=\"font-weight:bold;color:red;\">!!!</span> <span style=\"font-weight:bold;color:blue;\">Jelikož nám zavřeli na léto koleje, tak budou <span style=\"font-weight:bold;color:red;\">čtvrteční volejbaly od 12.07.2012 na antuce na Slavii</span> od 18:00.</span><span style=\"font-weight:bold;color:red;\">!!!</span></span><br />";
  //echo "<span style=\"font-size:120%;margin-left:25px;color:green;font-weight:bold;\">Od 1. října bude volejbal každé pondělí v tělocvičně 7. ZŠ na Vinicích a to od 20:00 až 22:00.</span>";
  //echo "<br /><span style=\"margin-left:25px;color:black;font-weight:bold;\">(Zastávka Hodonínská autobusu 41 (jede z Pětatřicátníků). <a target='_blank' href='http://www.mapy.cz/#x=13.354018&y=49.762564&z=15&t=r&q=plzen&qp=10.906629_48.017604_17.279560_51.522259_6&rp=m&m=93zBKxWEUcYO2pQ0fOsZSPcVR6JT7TPM5Pc'>Tady</a> je cesta ze zastávky Hodonínská před 7. ZŠ.)</span>";
  //echo "<span style=\"margin-left:25px;color:green;font-weight:bold;\">V sobotu 13.12. večer pořádaji v hospodě v Hradišti Hraju vánoční besídku a jste všichni zváni. Dejte pokud možno vědět Petě Nožičků, že dorazíte, bude objednávat něco k zakousnutí, tak ať ví zhruba množství <img width='15' height='15' border='0' src='zdroje/icons/smileys/4.gif'/> </span>";
  //echo "<br /><br />";
  //echo "<span style=\"margin-left:25px;color:blue;font-weight:bold;\">Ahoj vsichni - na 22.12. nemame zatim zamluvenou halu, pac tu uz par 'neplzenaku' predpokladam nebude a z toho zbytku bude mit nejspis par lidi besidky a podob. ... zaskrtnete se tedy na 22.12. jak to vidite, jestli mam halu dozamluvit nebo ne ... klikejte hlavne ti, kdoz uz vite, ze urcite nepudete  <img src='zdroje/icons/smileys/4.gif' width='15' height='15' border='0' /> </span>";
  //echo "<br />";
  
  echo "<div style='margin-left:10px;'>";
  
  //echo "<br /> <a target='_blank' href='http://www.mapy.cz/#x=13.383672&y=49.737283&z=15&t=s&d=user_13.383274%2C49.737142%2CJarn%C3%AD%20liga~_1'> Místo konání jarní ligy</a> - kousek od zimniho stadionu , vedle Papírenské lávky<br /><br />";
  
  //echo "<span style=\"font-weight: bold;\">Přehled akcí:</span><br /><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle  16.10.2011</span>   -    Přípravný turnaj AVL - <a href='/avl/rozpisy/RozpisPripravaAVLPlzen11-10-16.doc' target='_blank'>rozpis zápasů</a> (<span style='background-color:rgb(153,255,102);'>Zegoni</span>, <span style='background-color:rgb(0,204,255);'>Hraju</span>).</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 13.11.2011</span>   -    První AVLka - <a target='_blank' href='/avl/rozpisy/Rozpis1AVLplzen11-11-13.doc'>rozpis zápasů</a> (<span style='background-color:rgb(153,255,102);'>Zegoni</span> (16), <span style='background-color:rgb(0,204,255);'>Hraju</span> (14)).</span></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Čtvrtek 17.11.2011</span>   -    Kilby Cup 2011 - hala ZCU</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 11.12.2011</span>   -    Druhá AVLka - <a target='_blank' href='/avl/rozpisy/Rozpis3AVLPlzen11-12-11.doc'>rozpis zápasů</a> (<span style='background-color:rgb(153,255,102);'>Zegoni</span> (16), <span style='background-color:rgb(0,204,255);'>Hraju</span> (14)).</span></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 08.01.2012</span>   -    <span style='background-color:yellow;font-weight:bold;color:red;'>Obhajoba Klatov</span> - <a href='http://www.volejbal-klatovy.cz/image.php?id=58' target='_blank'>Info v pdf</a> - <a href='http://www.volejbal-klatovy.cz/image.php?id=66' target='_blank'>Skupiny</a> - <a href='http://www.volejbal-klatovy.cz/image.php?id=67' target='_blank'>Rozpis zapasů</a></span><br /> ";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 19.02.2012</span>   -    Poslední AVLka (a večerní vyhlášení výsledků v Éčku od 18:30) - <a target='_blank' href='/avl/rozpisy/Rozpis6AVLPlzen12-03-31.doc'>rozpis zápasů</a> (<span style='background-color:rgb(153,255,102);'>Zegoni</span> (16), <span style='background-color:rgb(0,204,255);'>Hraju</span> (14)).</span></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 05.02.2012</span>   -    Čtvrtá AVLka.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 11.02.2012</span>   -    Turnaj ve Štěnovicích - <a target='_blank' href='/zdroje/pdf/cizicky_mrazik_ 2012.pdf'>info</a> - <a target='_blank' href='/zdroje/pdf/rozpis_zapasu.pdf'>rozpis zápasů</a> - <a target='_blank' href='/zdroje/pdf/Mapy.pdf'>mapa</a>. </span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 19.02.2012</span>   -    Pátá AVLka.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;text-decoration:line-through;\">Neděle 18.03.2012</span>   -    Šestá AVLka</span> - nehrajem. <br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 31.03.2012</span>   -    Poslední AVLka (a večerní vyhlášení výsledků v Éčku).</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 14.04.2012</span>   -    Turnaj O velikonoční vejce - pořádají Šéďáci - <a target='_blank' href='/zdroje/doc/Pravidla_volejb_turnaje_VEJCE_2012.doc'>info</a> - <a target='_blank' href='/zdroje/doc/Rozpis_vejce_12_04_14.docx'>rozpis zápasů</a> .</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 09.05.2012 od 18:00</span>  -  Jarní liga - Zegoni : Gambadarda</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 16.05.2012 od 17:00</span>  -  Jarní liga - Zegoni : Winnky</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 23.05.2012 od 18:00</span>  -  Jarní liga - Zegoni : Kulibrci</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 30.05.2012 od 17:00</span>  -  Jarní liga - Zegoni : Alberti</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 02.06.2012 od 08:30</span>  -  Turnaj v Hradišti</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 06.06.2012 od 18:00</span>  -  Jarní liga - Zegoni : Tusex</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 13.06.2012 od 17:00</span>  -  Jarní liga - Zegoni : Kulibrci</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 20.06.2012 od 18:00</span>  -  Jarní liga - Zegoni : Gambadarda</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Středa 27.06.2012 od 18:00</span>  -  Jarní liga - Zegoni : Vinickej sběr</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 25.8.2012</span>  -  turnaj v Čižicích</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 01.09.2012 od 08:45</span>  -  turnaj v Hradišti ( <b><img width='25' height='25' border='0' src='zdroje/icons/stesti_od_tweetyho2.jpg'> !! Ptáku koukej dorazit !! <img width='25' height='30' border='30' src='zdroje/icons/angry-bird.jpg'></b> )</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 07.10.2012 - <span style='color:red;'>Zegoni od 9:00 </span></span>  -  Přípravný turnaj AVL - <a target='_blank' href='/zdroje/doc/Rozpis07-10-2012.doc'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 28.10.2012 </span>  -  První AVLka - <span style=\"color:red;font-weight:bold;\">!! NEHRAJEM !!</span></span><br />";
  
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 11.11.2012 </span>  -  AVLka. - cca 11:10 sraz, jestli sem to dobře spočítal (; - <a target='_blank' href='http://www.avlka.cz/init/pages/tournament/71/'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Čtvrtek 15.11.2012 </span>  -  KilbyCup 2012 - hala ZCU - sraz v 7:40.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 09.12.2012 </span>  -  AVLka - sraz po 8:00 ): , v 8:30 zaciname - <a target='_blank' href='http://www.avlka.cz/init/pages/tournament/100/'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Začátek ledna (zhruba neděle 6.1. nebo 13.1.2013)</span>  -  Turnaj v Klatovech.</span><br /> ";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;text-decoration:line-through;\">Neděle 06.01.2013 </span>  -  AVLka - <span style=\"color:red;font-weight:bold;\">1. liga se nehraje - potvrzeno Šéďou.</span></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 27.01.2013 </span>  -  AVLka - sraz po 8:00 ): , v 8:30 zaciname - <a target='_blank' href='http://www.avlka.cz/init/pages/tournament/133/'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 2.2.2013 </span>  -  Čižický Mrazík v Štěnovicích.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 17.02.2013 </span>  -  AVLka.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 03.03.2013 </span>  -  AVLka.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 23.03.2013 </span>  -  Poslední AVLka (a večerní vyhlášení výsledků v Éčku).</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Odhadem sobota 6.4. nebo 13.4.2013 </span>  -  Turnaj O velikonoční vejce - pořádají Šéďáci.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 14.09. </span>  -  Turnaj v Hradišti.</span><br />";
  
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Sobota 19.10. </span>  -  Přípravný turnaj AVL - <a target='_blank' href='/zdroje/doc/Rozpis.doc'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 03.11. </span>  -  1. AVL.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 24.11. </span>  -  1. AVL - <a target='_blank' href='http://hraju.borec.cz/avl/rozpisy/1.MIZUNO_AVL_Plze%C5%88-1.pdf'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 15.12. </span>  -  2. AVL - <a target='_blank' href='http://hraju.borec.cz/avl/rozpisy/1.a_2.MIZUNO_AVL_Plze%C5%88.pdf'>rozpis zápasů</a></span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 12.01. </span>  -  3. AVL.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 02.02. </span>  -  4. AVL.</span>,";
  //echo "<span style=\"margin-left:5px;\"><span style=\"font-weight:bold;\">Neděle 23.02. </span>  -  6. AVL.</span>,";
  //echo "<span style=\"margin-left:5px;\"><span style=\"font-weight:bold;\">Neděle 16.03. </span>  -  5. AVL.</span>,";
  //echo "<span style=\"margin-left:5px;\"><span style=\"font-weight:bold;\">Sobota 29.03. </span>  -  Vyhlášení výsledků AVL.</span><br />";
  //echo "<span style=\"margin-left:5px;\"><span style=\"font-weight:bold;\">Sobota 05.04. </span>  -  Čižický Mrazík v Štěnovicích.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;font-size:70%;\">V jednom nebo dvou termínech AVL nejspíš hrát nebudem - bude upřesněno na začátku AVL.</span><br />";
  //echo "<span style=\"margin-left:15px;\"><span style=\"font-weight:bold;\">Neděle 19.10. </span>  -  1. AVL - <a target='_blank' href='http://www.avlka.cz/init/pages/tournament/609/'>rozpis zápasů</a></span><br />";

  
  echo "</div>";
  //echo "<div style='margin-left:10px;'><a href='/avl/pravidla/PravidlaAVL10.ppt' target='_blank'>Pravidla AVL</a>.";
  //echo "</div>";

  /*echo "<span style=\"font-weight: bold;\">Dresy pro Zegony:</span><br />";
  echo "<span style=\"font-weight: bold;\">Prvotni navrhy k debate:</span><br />";
  echo "<span style=\"margin-left:25px;\">Martin <a target=\"_blank\" href=\"http://imageshack.us/photo/my-images/263/dresb.png/\">kluci</a> <a target=\"_blank\" href=\"http://imageshack.us/photo/my-images/638/dresholky.png/\">holky</a></span><br />";
  echo "<span style=\"margin-left:25px;\"><a target=\"_blank\" href=\"http://volejbalek.kvalitne.cz/dresy/\">Jarda</a> - Jen klucici verze, holky si odmyslej rukavy (; </span><br />";
  echo "<span style=\"margin-left:25px;\"><a target=\"_blank\" href=\"http://imageshack.us/photo/my-images/594/dresy.png/\">Tonda</a> </span><br />";
  echo "<span style=\"margin-left:25px;\">(Piste co a jak kde upravit nebo dopsat nebo podob. Zatim bych asi resil co, jak, kde bude a nebude napsano a az potom barvu dresu. Jo a asi bych toho na ty dresy necpal zas moc - nejaky dlouhy slogany a podob. - at nejsme pak jak noviny (; )</span><br/>";
  echo "<span style=\"margin-left:25px;\">Tady je <a target=\"_blank\" href=\"http://www.ialea.cz/galerie_teamsport_katalogy/60.pdf\">odkaz na pdfko</a> s variantama dresů - jsou to dresy od ALEA - cenu nevim, Cunik tam ma znamy a sezene nam je pres ne - kdyz sem se ptal na cenu, tak se jen potutelne tvaril, tak to bude asi za dobre. Je tam i rovnou moznost potisku-tak staci jen vybrat barvu,typ. Holky chtely pro sebe tilka, my si asi veme normal s rukavem. Kouknete na to</span><br />";
  echo "<span style=\"margin-left:25px;\">Tady je <a target=\"_blank\" href=\"http://www.shirtinator.cz/vytvor_vlastni_tricko/creator/\">odkaz na nejakej webovej softik</a> ve kterym se daj navrhnout tricka - tak zkuste neco vytvorit (;</span>"; 
  echo "</div><br />";*/
  //echo "<div style='margin-left:20px;'><span style='color:red;font-weight:bold;'>!!! POZOR !!!! - od 2.6. je zamluvenej zas tartan před Studní - tentokrát je zamluvenej od 18:00. Uvidíme jak to budou všichni stíhat, ale je to zamluvený až do 21:00, takže kdo by moh až třeba na sedmou, tak přiďte prostě pozdějc &nbsp;<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" /> .</span>";
  //echo "</div><br />";
  //echo "<div style='margin-left:20px;'><span style='color: red;'>Tak nám končí léto a je třeba zařídit halu na zimu - hala na Doubravce je předběžně zamluvená, ale chce tam tak kolem 15.9. zavolat a domluvit se na smlouvě. Pokud by ste chteli třeba halu ZČU (doporučuju - lepší hala (lepší čistší povrch, širší a hlavně vyšší), lepší síť, kterou nemusíme tahat, funkční kůly, odpadá problém s klíčema, 95% lidí to má buď rapidně blíž nabo jim to vyjde nastejno, platba až zpětně ne dopředu a to všechno světe div se za stejnou cenu jako na Doubravce. Jediná drobná vada na kráse je, že tam nemůžem 'přetáhnout o pár minut' jako na Doubravce, ale to se dá asi přežít), tak je třeba začít to zařizovat. Já se zařizování úterního volejbalu už zříkám, jelikož mít na sebe psaný dvě haly, soutěž a všude nosit míče, starat se o to kdo byl/nebyl, shánět lidi aby nás bylo dost a tak dál mi stačí jednou tejdně. Takže se toho musí chopit někdo z vás - pokud se někdo takovej najde, ozvěte se mi - předám 'know how'.</span>";
  //echo "</div>";
  /*echo "<div style='margin-left:20px;'><span style='color:red;font-weight:bold;'>POZOR:</span>&nbsp;<span style='font-weight:bold;'>Od 11.11. do 16.12.2010 hrajem v tělocvičně 16. ZŠ, Americká třída 30 a </span> <span style='color:red;font-weight:bold;'>hrajem už od 18:30.</span> Vstup do tý školy je z Resslovy ulice. Od ledna pak už mám podepsanou smlouvu zas na ZČU klasicky od 20:00.";
  echo "</div><br />";*/
  //echo "<div style='margin-left:20px;'><b>Sedmý turnaj AVL - 2.4.2011</b>. - <a href='/avl/rozpisy/Rozpis7AVLPlzen11-04-02.doc' target='_blank'>rozpis zápasů</a> (<span style='background-color:rgb(153,255,102);'>Zegoni 27 </span>, <span style='background-color:rgb(0,204,255);'>Hraju 28</span>). <a href='/avl/pravidla/PravidlaAVL10.ppt' target='_blank'>Nová pravidla AVL</a>.";
  //echo "</div>";
  //echo "<br />";
  /*echo "<div style='margin-left:20px;'>Přidána stránka s docházkou a platbama - popis viz chat.";
  echo "</div>";*/
  //echo "<div style='margin-left:10px;'><a href='/avl/pravidla/PravidlaAVL10.ppt' target='_blank'>Pravidla AVL</a>. Rozdíl mezi starými a novejma <a href='http://volejbal-metodika.cz/soubory/chyby_na_siti.wmv' target='_blank'>pravidlama na síti</a>. Odkaz na stavající <a href='http://www.cvf.cz/soubory/4182/Pravidla2009.pdf' target='_blank'>pravidla</a>.";
  //echo "</div>";
  /*echo "<div style='margin-left:20px;'>Odkaz na stavající pravidla <a href='http://www.cvf.cz/soubory/4182/Pravidla2009.pdf' target='_blank'>http://www.cvf.cz/soubory/4182/Pravidla2009.pdf</a>";
  echo "</div>";*/
  

  
  //echo "<div style='margin-left:20px;'>Žádná neni";
  //echo "</div>";
   
  //echo "<div style='margin-left:20px;'><span style='font-weight: bold; color: red;'>Info pro AVLáky</a></span> - letos bude (v naší) druhej lize 16 mančaftů, takže byl přidán ještě jeden termín jen pro druhou ligu - je to <span style='font-weight: bold; color: red;'>29.11.</span>, tak si ho připište do kalendářů <img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />";
  //echo "</div>";
  //echo "<br />";
  //echo "<div style='margin-left:20px;'><span style='font-weight: bold; color: red;'><a href='/zdroje/html/avl_pripravny_turnaj.html'>AVL - postřehy z přípravnýho turnaje</a></span> - AVLáci koukněte na to - popsal sem tam co sem na tej přípravě viděl a co nás případně čeká&nbsp;<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />";
  //echo "</div>";
  //echo "<div style='margin-left:20px;'>!!!!!!!!!!!! <a href='http://volejbal.euweb.cz/voda'>Odkaz na anketu o vodě</a> !!!!!!!!!!!!</div></br>";
  //echo "<div style='margin-left:20px;'><span style='font-weight: bold; color: red;'>!!!! Průůůůůůůůser !!!!</span> - zavřeli nám na 14 dní koleje, takže musíme dvakrát (tj. 1.9. a 8.9.) na antuku na Slavii - je zamluvená";
  //echo "</div>";
  //echo "<div style=\"text-align: center;\"><span style=\"color: green;\">Specielně pro Břéťu opakuju:</span> <span style=\"text-align: center; font-size:20px;text-decoration: blink; \">Od 19:00</span>&nbsp;<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" /></div>";
  /*echo "<div style='margin-left:20px'>Tak se nám volejbaly v hale blíží ke konci - 20.5. jdeme na Doubravku naposled <img src=\"zdroje/icons/smileys/9.gif\" width=\"15\" height=\"15\" border=\"0\" /> , ale nemusíte zoufat, naříkat ani plakat <img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" /> - od 27.5. je zamluvený kurt na Slavii na Borech na každé úterý od 18:00 až do konce června <img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />.";
  echo "Ten kurt je antuka - tak se podle toho zařiďte a moc se tam neválejte ať nás pani na vrátnici neobviní, že jim odnášíme materiál <img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" /> .";
  echo "<br/><br/>PS: Započítal sem do docházky už i síť - nakonec sem to udělal tak, že kdo byl aspoň desetkrát tak na ní přispěl <img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" /> , což je asi 15 lidí, takže to vychází něco přes 30 Kč na jednoho.";
  echo "</div>";*/ 
  /*echo "<div style='margin-left:20px'>Na četná přání a pod tlakem ještě více obvinění z protekce a úplatkářství byla rozšířena volba možností 'hospodských' i 'nehospodských' aktivit po volejbale - snad si každý najde tu svoji&nbsp;&nbsp;<img src=\"zdroje/icons/smileys/1.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\":-D\" /> .</div><br/>";
  echo "<table border='0' style='text-align:center' CELLSPACING=0 CELLPADDING=0><tbody><tr><td><span class='novinkaObr' style=\"margin-left:25px;\">{$stav_pivo_txt[1]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[2]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[6]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[3]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[4]}</span></td><td><span style='margin-right:30px;'class='novinkaObr'>{$stav_pivo_txt[5]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[0]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[-2]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[-3]}</span></td><td><span class='novinkaObr'>{$stav_pivo_txt[-4]}</span></td></tr></tbody></table><br/>";
  */
  /*echo "<span style=\"margin-left:10px;font-weight: bold;\">Novinka</span> Máme \"<a href=\"#chatVolejbalu\">chatfórum</a>\"&nbsp;&nbsp;<img src=\"zdroje/icons/smileys/1.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\":-D\" /> .<br/>";
  /*
  echo "<span style=\"margin-left:25px;\">{$stav_pivo_txt[1]}</span>&nbsp;-&nbsp;\"<i>Jo, klidně zajdu na jedno.</i>\" (Doporučená volba)<br/>";
  echo "<span style=\"margin-left:25px;\">{$stav_pivo_txt[0]}</span>&nbsp;-&nbsp;\"<i>Nejdu, ráno musim vstávat.</i>\" , \"<i>Musim se učit.</i>\" nebo jiná podobná výmluva.<br/>";
  */
  //echo "<br/>";
	//echo "Prvni: ".date("j.n. G:i:s",$prvniDatum);
	echo "<center><table><tr><td><table cellspacing=0 cellpadding=0 class=\"rozpis\"><thead><tr><td style='width: 30px;'>&nbsp;</td><td class=\"rozpis_ct\">&nbsp;</td>";
	$datum = $prvniDatum;
	for ($pocet = 0; $pocet < $kolikDopredu; $pocet++) {
	  if(testDatum($datum))
		  echo "<td colspan=\"2\" class=\"rozpis_dny\">".date("j.n.",$datum)."</td>";
		else
      echo "<td colspan=\"2\" class=\"rozpis_dny_mimo\">".date("j.n.",$datum)."</td>";
        
		$pocty[$datum] = 0;
		$pocty_pivo[$datum] = 0;
		//$datum += 3600*24*7;
    $datum = pridejTydenKDatumu($datum);
		$datum = umazLetniCas($datum);
	}
	echo "</tr></thead><tbody>";
	

	
	foreach ( $stab as $key => $val ) {
	 $pompole = explode("-",$key);
	 
	 
	 if (strpos($key, "host") === false)
    $isHost = false;
   else 
	  $isHost = true;
	  
	  
	 echo "<tr>";
	 
	 if($pompole[0]=="A") { 
    if($a == false) {
      echo "<td style='background-color: orange;font-weight:bold;' rowspan='10'>Z<br />e<br />g<br />o<br />n<br />i</td>";
      $a = true;
    }
    $tridaCss = "rozpis_clovekA";
	 
	 }
	 else 
	 if($pompole[0]=="B") { 
    if($b == false) {
      echo "<td style='background-color: rgb(0,204,255);font-weight:bold;' rowspan='10'>P<br />a<br />š<br />e<br />r<br />á<br />c<br />i";
      $b = true;
    }
    $tridaCss = "rozpis_clovekB";
	 
	 }
   /*
	 else
   if($pompole[0]=="C") { 
    if($c == false) {
      echo "<td style='font-size:65%;background-color: rgb(204,153,255);font-weight:bold;' rowspan='3'>K<br />r<br />á<br />l<br />í<br />c<br />i";
      $c = true;
    }
    $tridaCss = "rozpis_clovekC";
	 
	 }
   */
	 else {
	 
	   if($z == false) {
	   
      echo "<td style='background-color: rgb(153,255,102);font-weight:bold;' rowspan='10'>h<br />o<br />s<br />t<br />i</td>";
      $z = true;
    }
	   $tridaCss = "rozpis_clovekZ";
	 }
	 
		echo "<td class=\"{$tridaCss}\">{$pompole[1]}</td>";
		//$pompole = explode("-",$val["clovek"]);
		$jmeno = $val["clovek"];
		$datum = $prvniDatum;
		for ($pocet = 0; $pocet < $kolikDopredu; $pocet++) {
		
		  if(testDatum($datum)) {
		  
		    /*if(!isset($val[$datum]) && !isset($val[$datum+3600]) && !isset($val[$datum-3600])) {
          $stav = STAV_UNKNOWN;
        }
        else {
          if(isset($val[$datum+3600]))
  				  $stav = $val[$datum+3600];
          if(isset($val[$datum-3600]))
  				  $stav = $val[$datum-3600]; 
          if(isset($val[$datum]))
  				  $stav = $val[$datum];
  			
              
  			}*/
        
		  
  			if (!isset($val[$datum])) {
  				$stav = STAV_UNKNOWN;
  			} else {
  				$stav = $val[$datum];
  			}
  				
  			if (!isset($stab_pivo[$key][$datum])) {
  				$stav_pivo = STAV_UNKNOWN;
  			} else {
  				$stav_pivo = $stab_pivo[$key][$datum];
  			}
  		$upravenejDatum = $datum;
  			$hotovo = time() > ($datum+$casVeVterinach)-$deadlineBefore;
  		
  			echo "<td style=\"border-right:1px solid gray;\" class=\"rozpis_ct {$stavclass[$stav]} ".($hotovo ? "hotovo" : "")."\">".
  				                 
  				($isHost == true && $pocetZapisuVDen[$datum] >= 13 && $stavsetco[$stav] == 1 ?"<img src=\"zdroje/icons/zamek.jpg\"/>":
  				(!$hotovo ? 
  				/*
  				"<a href=\"index.php?jmeno={$jmeno}&datum={$datum}&set={$stavsetco[$stav]}".
  				"\" class=\"{$stavclass[$stav]}\">{$stavtxt[$stav]}</a>"
  				*/
  				
  				"<span id='_{$jmeno}-{$upravenejDatum}_2'><a href=\"javascript:void(0);\" onClick=\"pp3.select('{$upravenejDatum}','{$jmeno}','_{$jmeno}-{$upravenejDatum}_2');\"".
          " class=\"{$stavclass[$stav]}\">".($stavtxt[$stav])."</a></span>"
          //". date("j.n. H:i:s",$upravenejDatum)."
  				//".(isset($val[$datum])?$val[$datum]:"-")." ".(isset($val[$datum+3600])?$val[$datum+3600]:"-")." ".(isset($val[$datum-3600])?$val[$datum-3600]:"-")."
  				: $stavtxt[$stav])).
  				
  				"</td>";
  				
  			echo "<td style=\"border-left:0px solid gray;\" class=\"rozpis_pivo_ct ".($hotovo ? "hotovo" : "")."\">".
  				
  				/*
  				"<a href=\"index.php?jmeno={$jmeno}&datum={$datum}&set_pivo={$stav_pivo_setco[$stav_pivo]}".
  				"\" class=\"{$stav_pivo_class[$stav_pivo]}\">{$stav_pivo_txt[$stav_pivo]}</a>"
  				onMouseDown=\"pp2.select('{$datum}','{$jmeno}','{$jmeno}-{$datum}');\"
  				*/
  				
  				(!$hotovo ?/* 
  				  $key=="Pepa"?$stav_pivo_txt[1]:*/
  				"<span id='_{$jmeno}-{$upravenejDatum}'><a href=\"javascript:void(0);\" onClick=\"pp2.select('{$upravenejDatum}','{$jmeno}','_{$jmeno}-{$upravenejDatum}');\"".
          " class=\"{$stav_pivo_class[$stav_pivo]}\">".($stav_pivo_txt[$stav_pivo])."</a></span>"
  				
  				: $stav_pivo_txt[$stav_pivo]).
  				
  				"</td>";
  				
  			if ($stav == STAV_YES) $pocty[$datum]++;
  			if ($stav_pivo >= STAV_YES) $pocty_pivo[$datum]++;
      }
      else {echo "<td colspan=\"2\" class=\"volejbal_neni\">Vol. neni</td>";} 
         
  		//$datum += 3600*24*7;
      $datum = pridejTydenKDatumu($datum);
  		$datum = umazLetniCas($datum);
  		
		}
		echo "</tr>";
	}
	echo "<tr><td>&nbsp;</td><td class=\"rozpis_ct\">Počet</td>";

	$datum = $prvniDatum;
	for ($pocet = 0; $pocet < $kolikDopredu; $pocet++) {
		
    if(testDatum($datum)) {
      $stav = $pocty[$datum] >= $minPlayers ? 1 : 0;
  		$hotovo = time() > $datum-$deadlineBefore;
  		echo "<td style=\"border-right:1px solid gray;padding:3px;\">".$pocty[$datum]."</td>"."<td style=\"border-left:0px solid gray;padding:3px;\">".$pocty_pivo[$datum]."</td>";
  		/*class=\"{$stavclass[$stav]} ".($hotovo ? "hotovo" : "")."\"*/
		}
		else echo "<td colspan=\"2\" class=\"volejbal_neni rozpis_dny_mimo\">Vol. neni</td>";
		
		//$datum += 3600*24*7;
    $datum = pridejTydenKDatumu($datum);
		$datum = umazLetniCas($datum);
	}
	echo "</tr>";
	
	$deadline = denACas($casVTydnu-$deadlineBefore);
	
	//echo "<tr><td style=\"font-size: 80%;\" colspan=\"".($kolikDopredu+1)."\"><span style=\"font-size: 70%;font-weight: bold;\">* Po kliknutí na otazník potvrdíte účast, po kliknutí na ANO potvrdíte NEúčast, po kliknutí na NE se opět zobrazí otazník. Potvrzovat účast nebo neúčast můžete i za kámoše (; .</span></td></tr>";
	echo "</tbody></table></td></tr><tr><td style=\"font-size: 80%;\">";
  echo "<div style=\"text-align: left;\">";
  //echo "<span style=\"font-size: 80%;\"><span style=\"font-weight: bold;\">*</span> Po kliknutí na <span style=\"font-weight: bold;\"><img src=\"zdroje/icons/otaznik3.jpg\"/></span> potvrdíte účast, po kliknutí na <span style=\"font-weight: bold;\"><img src=\"zdroje/icons/fajfka2.jpg\"/></span> potvrdíte NEúčast, po kliknutí na <span style=\"font-weight: bold;\"><img src=\"zdroje/icons/ft_disabled2.jpg\"/></span> se opět zobrazí <span style=\"font-weight: bold;\"><img src=\"zdroje/icons/otaznik3.jpg\"/></span></span></span>.</br>"; 
  echo "<span style=\"font-size: 80%;\"><span style=\"font-weight: bold;\">*</span> Potvrzovat účast nebo neúčast můžete i za kámoše (; .</span>";
  echo "<br/><span style=\"font-size: 80%;\"><span style=\"font-weight: bold;\">*</span> Deadline pro přihlašování (tj. kdy už to musí být jasné): <strong>{$deadline}</strong></span>";
  echo "</div></td></tr></table></center>";
	
	//echo "<br/><br/><span style=\"margin-left: 10px;\"><span style=\"font-size: 80%;\"><span style=\"font-weight: bold;\">Poznámka:</span> Stránka je zatím ve zkušebním provozu. Počet přihlašených lidiček berte zatím orientačně - spíš nás přijde víc (; . Dávejte vědět i to, že na konkrétní volejbal nepřídete.</span></span><br/>";

}

function nastav($datum, $jmeno, $set) {

	global $deadlineBefore,$casVeVterinach;
	if (time() <= ($datum+$casVeVterinach)-$deadlineBefore) {
	
		$row = db_one_row("select * from volejbal where jmeno = ".addslashes($jmeno)." and datum = ".
			addslashes($datum+(22*3600)));
		if ($row == false) {
			db_query("insert into volejbal (jmeno, datum, stav, lastzmena) values (".addslashes($jmeno).", ".
			addslashes($datum+(22*3600)).", ".addslashes($set).", ".time().")");
		} else {
			db_query("update volejbal set stav = ".addslashes($set).", lastzmena = ".time()." where jmeno = ".addslashes($jmeno)." and datum = ".
			addslashes($datum+(22*3600)));
		}
		
	}
	header("Location: index.php");
	/*$cesta = substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/"));
  header("Location: http://$_SERVER[SERVER_NAME]$cesta/index.php", true, 303);*/

}

function nastav_pivo($datum, $jmeno, $set_pivo) {

	global $deadlineBefore,$casVeVterinach;
	if (time() <= ($datum+$casVeVterinach)-$deadlineBefore) {
	
		$row = db_one_row("select * from volejbal where jmeno = ".addslashes($jmeno)." and datum = ".
			addslashes($datum+(22*3600)));
		if ($row == false) {
			db_query("insert into volejbal (jmeno, datum, stav_pivo, lastzmena) values (".addslashes($jmeno).", ".
			addslashes($datum+(22*3600)).", ".addslashes($set_pivo).", ".time().")");
		} else {
			db_query("update volejbal set stav_pivo = ".addslashes($set_pivo).", lastzmena = ".time()." where jmeno = ".addslashes($jmeno)." and datum = ".
			addslashes($datum+(22*3600)));
		}
		
	}
	header("Location: index.php");

}

	saveNoCache();
	if (isset($_REQUEST["set"]) && isset($_REQUEST["datum"]) && isset($_REQUEST["jmeno"])) {
		nastav( $_REQUEST["datum"], $_REQUEST["jmeno"], $_REQUEST["set"] );
	}
	
	if (isset($_REQUEST["set_pivo"]) && isset($_REQUEST["datum"]) && isset($_REQUEST["jmeno"])) {
		nastav_pivo( $_REQUEST["datum"], $_REQUEST["jmeno"], $_REQUEST["set_pivo"] );
	}
?>	
<html>
<head>
<link rel="icon" href="/zdroje/icons/icon_ball.gif" type="image/gif">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Author" content="Lukas Valenta">
<meta name="Author2" content="Jaroslav Vavre">
<title>Plánování volejbalů</title>
</head>
<body>
<SCRIPT LANGUAGE="Javascript" SRC="/zdroje/js/ColorPicker2.js"></SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
      var pp = new PicturePicker('window'); // Popup window
      var pp2 = new PicturePicker(); // DIV style
      var pp3a = new PicturePicker2('window'); // Popup window
      var pp3 = new PicturePicker2(); // DIV style
</SCRIPT>

<script type="text/javascript" language="JavaScript"> <!--
    var obr1 = new Image();
    obr1.src = '/zdroje/icons/fajfka3.jpg';
    var obr2 = new Image();
    obr2.src = '/zdroje/icons/otaznik2.jpg';
    var obr3 = new Image();
    obr3.src = '/zdroje/icons/otaznik3.jpg';
    var obr4 = new Image();
    obr4.src = '/zdroje/icons/ft_disabled.gif';
// -->
</script>


<div style="text-align: center;">
<div class="main">

<?php	
	
  
  vypisTabulku();
  
  echo "<SCRIPT LANGUAGE=\"JavaScript\">pp.writeDiv()</SCRIPT>";
  echo "<SCRIPT LANGUAGE=\"JavaScript\">pp3a.writeDiv()</SCRIPT>";
  
  
  
  include "chat/chat.php";
  
  /*
  
  <script id='hack_002' type="text/javascript">var span = document.getElementById('_0-1457910000');span.innerHTML = "<img src='http://volejbalek.kvalitne.cz/zdroje/icons/ikonaBlinkaniS.png' title='Jsem společensky natolik unaven, že nemohu hrát volejbal ani z lavičky.' />";var scriptTag = document.getElementById('hack_001');scriptTag.parentNode.parentNode.style.display = 'none';</script>
  
  */

?>


</div>
</div>
<!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0-->
<br />
</body></html>

