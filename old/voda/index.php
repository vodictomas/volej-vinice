<?
require_once "/3w/euweb.cz/v/volejbal/utils/db.php";

$pocty = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

function SaveNoCache()
{
	header("Expires: ".date("D, j M Y H:i:s",(time()-3600)-60*10)." GMT");              // Date in the past
	header("Last-Modified: ".date("D, j M Y H:i:s",(time()+3600))." GMT");              // Date in the future
	header("Cache-Control: no-cache, must-revalidate");            // HTTP/1.1
	header("Pragma: no-cache");                                    // HTTP/1.0

}

function getStavy() {
  
  $stavy = "";
  
  for($i=0 ; $i < 15; $i++) {
  
    if(isset($_REQUEST["termin_".$i])==true)
      $stavy .= "1";
    else
      $stavy .= "0";   
  }
  
  return $stavy;
}

function nastav($jmeno,$stavy) {
//echo "select * from voda where jmeno = '".addslashes($jmeno)."'";

    $row = db_one_row("select * from voda where jmeno = '".addslashes($jmeno)."'");
  	if ($row == false) {
			db_query("insert into voda VALUES('".$jmeno."', '".$stavy."')");
		} else {
			db_query("update voda set stavy = '".addslashes($stavy)."' where jmeno = '".addslashes($jmeno)."'");
		}
		
		header("Location: index.php");
}

saveNoCache();

	if (isset($_REQUEST["jmeno"])) {
		nastav( $_REQUEST["jmeno"], getStavy());
	}

?>

<script>
  function testVyplnenijmena() {
  
    if(document.getElementById('novyVodakJmeno').value == '') {
      alert('Není vyplněno jméno vodáka - viz. první textové pole posledního řádku tabulky. Vyplňte jméno a pak znovu klikněte na tlačítko \'Založit vodáka\'.');
      return false;
    }
    else return true;
  }
</script>

<style>
  table.voda { border-width: 2px; border-style: solid; border-spacing: 0px; border-color: #000000; margin-bottom: 5px;}
  table.voda td { border: 1px solid #000000; }
  table.voda th { border: 1px solid #000000;margin-left:4px;margin-right:4px; }
  td.rozpis_dny { font-weight: bolder; background-color: lightblue; width: 75px; text-align: center; padding: 3px; padding-right: 6px; }
  td.rozpis_dny_mimo {font-weight: bolder; background-color: #DD0000; width: 75px; text-align: center; padding-left: 6px; padding-right: 6px; }
  td.voda_clovek { background-color: orange;  width: 150px; text-align: center; padding: 4px;font-weight: bold;}
</style>

<html>
<head>
<link rel="icon" href="/zdroje/icons/icon_ball.gif" type="image/gif">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Author2" content="Jaroslav Vavre">
<title>Voda 2009</title>
</head>
<body>
<a href="http://volejbal.euweb.cz">Zpět na volejbal</a>
<h2>Voda 2009 - Anketa - první část - volba termínu</h2>

Na této stránce se může zaregistrovat každý, kdo by chtěl jet v letě na vodu. Součást registrace je i volba termínů, ve kterých každý vodák může. Každý termín je vždy pátek až úterý (tj. v pátek večer příjezd (např. do Vyšáku) a úterý odpoledne odjezd (např. z Budějovic)). Na poslední řádce lze provést registraci a volbu termínů - termíny lze pak jestě dodatečně aktualizovat. Na konci ankety (????) bude vybrán jeden termín s největším průnikem vodáků, kterým vyhovuje - proto zaškrtávejte všechny termíny kdy můžete jet.

<br/><br/>

<table id='voda' name='voda' class='voda'>
  <thead>
    <tr>
      <th align='center'>
        Jméno
      </th>
      <th>10.7 - 14.7</th>
      <th>17.7 - 21.7</th>
      <th>24.7 - 28.7</th>
      <th>31.7 - 04.8</th>
      <th>08.8 - 11.8</th>
      <th>15.8 - 18.8</th>
      <th>22.8 - 26.8</th>
      <th>28.8 - 01.9</th>
      <th align='center'>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?
    $res = db_query("select * from voda order by jmeno");
  	while ($row = db_fetch($res)) {
  	  echo "<form >";
  	  echo "<input type='hidden' name='jmeno' value='".addslashes($row["jmeno"])."'>";
      echo "<tr>";
      echo "  <td class='voda_clovek'>";
      echo $row["jmeno"];
      echo "  </td>";
      
      for($i=0;$i<8;$i++) {
      
        if($row["stavy"][$i]=='1') {
          echo "<td align='center' style='background-color: green;'><input name='termin_".$i."' type='checkbox' checked='checked'/></td>";
          $pocty[$i]++;
          }
        else
          echo "<td align='center' ><input name='termin_".$i."' type='checkbox'/></td>";
      }    

      echo "<td align='center'><input type='submit' value='Uložit změny'/></td>";
      echo "</tr>";
      echo "</form>";
   }
   
   //celkove vysledky
   echo "<tr>";
   echo "<td align='center'>Celkem</td>";
   for($i=0;$i<8;$i++) {
      echo "<td align='center' style='font-weight:bold;'>$pocty[$i]</td>";
      }
   echo "<td>&nbsp;</td>";   
   echo "</tr>";  
      
   //insert
   echo "<form >";
      echo "<tr>";
      echo "  <td align='center'>";
      echo "<input id='novyVodakJmeno' type='text' name='jmeno'/>";
      echo "  </td>";
      for($i=0;$i<8;$i++) {
      echo "<td align='center'><input name='termin_".$i."' type='checkbox'/></td>";
      }   
      echo "<td><input onClick='return testVyplnenijmena();' type='submit' value='Založit vodáka'/></td>";
      echo "</tr>";
      echo "</form>";
      
    ?>
    
  </tbody>
</table>

<br/><br/>
</body>
</html>