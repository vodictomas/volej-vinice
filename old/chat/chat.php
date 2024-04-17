  <style>
    .message {
      font-family: 'Verdana CE', 'Arial CE', 'Lucida Grande CE', 'Helvetica CE', Verdana, Arial, lucida, sans-serif;
    }
    
    .date {
      font-family: sans-serif;
      font-size: 9pt;
    }
    
    .name {
      font-family: 'Verdana CE', 'Arial CE', 'Lucida Grande CE', 'Helvetica CE', Verdana, Arial, lucida, sans-serif;
    }
  </style>
  
  <script type="text/javascript">
          /*
           name - name of the cookie
           value - value of the cookie
           [expires] - expiration date of the cookie
             (defaults to end of current session)
           [path] - path for which the cookie is valid
             (defaults to path of calling document)
           [domain] - domain for which the cookie is valid
             (defaults to domain of calling document)
           [secure] - Boolean value indicating if the cookie transmission requires
             a secure transmission
           * an argument defaults when it is assigned null as a placeholder
           * a null placeholder is not required for trailing omitted arguments
        */

        function setCookie(name, value , expires, path /*, domain, secure*/) {
          var curCookie = name + "=" + escape(value) +
              ((expires) ? "; expires=" + expires.toGMTString() : "") +
              ((path) ? "; path=" + path : ""); /* +
              ((domain) ? "; domain=" + domain : "") +
              ((secure) ? "; secure" : "");*/
          document.cookie = curCookie;
        }


        /*
          name - name of the desired cookie
          return string containing value of specified cookie or null
          if cookie does not exist
        */

        function getCookie(name) {
          var dc = document.cookie;
          var prefix = name + "=";
          var begin = dc.indexOf("; " + prefix);
          if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
          } else
            begin += 2;
          var end = document.cookie.indexOf(";", begin);
          if (end == -1)
            end = dc.length;
          return unescape(dc.substring(begin + prefix.length, end));
        }
        
        function setJmenoDoCookie(selectbox) {
        
              var datum= new Date();
              datum.setTime(datum.getTime() + 1000 * 60 * 60 * 24 * 30);
              setCookie('volejbal_chat_jmeno',selectbox.options[selectbox.selectedIndex].value,datum,'/');
              
        }
        
  </script>      
  
  <script type="text/javascript">
  
    function insertText(elname, what) {
       if (document.getElementById('message').createTextRange) {
       document.getElementById('message').focus();
       document.selection.createRange().duplicate().text = what;
       } else if ((typeof document.getElementById('message').selectionStart) != 'undefined') { // for Mozilla
       var tarea = document.getElementById('message');
       var selEnd = tarea.selectionEnd;
       var txtLen = tarea.value.length;
       var txtbefore = tarea.value.substring(0,selEnd);
       var txtafter = tarea.value.substring(selEnd, txtLen);
       var oldScrollTop = tarea.scrollTop;
       tarea.value = txtbefore + what + txtafter;
       tarea.selectionStart = txtbefore.length + what.length;
       tarea.selectionEnd = txtbefore.length + what.length;
       tarea.scrollTop = oldScrollTop;
       tarea.focus();
       } else {
       document.getElementById('message').value += what;
       document.getElementById('message').focus();
       }
     } 
  </script>
  
  <script type="text/javascript">
      function Smile(what)
      {
        //document.forms.comment.message.focus();
        //document.forms.comment.message.value=document.forms.comment.message.value+what;
        insertText('message', what);
      }
      
  </script>
  
    <SCRIPT LANGUAGE="JavaScript">
      var cp = new ColorPicker('window'); // Popup window
      var cp2 = new ColorPicker(); // DIV style
    </SCRIPT>
<br/>   
<a name="chatVolejbalu"/>
<center>
<table border="0" style="margin:20px 105px 70px 105px; padding:0px;">
<tr>
<td> 
 
<div style="border: 1px solid gray;background:#EEEEDD;_padding-top: 6px;">

<div id="kecaciDiv" style="height:140px;overflow:auto;background:#EEFFFF;border: 1px solid gray;margin: 6px 3px 5px 3px; padding: 5px;text-align:left;font-family: verdana;">



 <?php
  
    include "chat/kecani.php";
    
    if(!isset($_SESSION['color_s']))
      $_SESSION['color_s'] = "#33CCFF";
      
    if(!isset($_SESSION['name_s']))
      $_SESSION['name_s'] = "";
    
    if(isset($_REQUEST["color"])) {      
        $_SESSION['color_s'] = $_REQUEST["color"];  
    }
      
    if(isset($_REQUEST["name"])) {      
        $_SESSION['name_s'] = $_REQUEST["name"];  
    }   
    
 ?>
    </div>
    <div style="border: 1px solid gray;margin: 3px;background: #EEEEE5;text-align:left;font-family: sans-serif;font-size: 9pt;"> 
        <form action="index.php#chatVolejbalu" method="post" name="comment" style="margin: 0px; padding: 0px;">
        <table border="0" style="margin: 0px; padding 0px;">
        <thead></thead>
        <tbody>
        <tr>
        <td>Jméno&nbsp;:&nbsp;</td>
        <td><!--<input size="50" type="text" name="name">-->
          <select name="name" id="chat_name_select" onchange="javascript: setJmenoDoCookie(this);">
             <option value="">NEZVOLENO&nbsp;</option>
             <option value="host">host</option>
             <?
              $lidi = array("Agáta","Bergy","Evča","Hory","Huďák","Jakub","Jarda","Ivana","Kasička","Katka Voč.","Lucka","Luďa","Majda","Michal","Mirka","Míša","Martin","Michal","Ondra","Patrik","Gali","Štěpán","Tereza","Tomáš J.","Tomáš R.","Tomáš V.","Vojta","Winky","Zuzka");
              //array("Taker","Čuník","Tonda","Peťa B.","Peťa H.","Bambík","Vendy","Hanka","Mahony","Kosťa","Tuňák","Romča","Danča","Jana H.","Janča","Míša","Lukáš","Fík","Slávek","Jája","Standa","Katka","Břéťa","Míša(Vondry)","Vondry","Pták");
              //array("Taker","Čuník","Tonda","Peťa B.","Peťa H.","Bambík","Vendy","Hanka","Mahony","Kosťa","Tuňák","Romča","Vous","Jana H.","Janča","Míša","Lukáš","Slávek","Jája","Standa","Břéťa","Míša(Vondry)","Vondry","Pták");
              ksort($lidi);
             
              for($i = 0 ; $i < count($lidi); $i++) {
              
                //$pompole = explode("-",$lidi[$i]);
                
                if( $lidi[$i]==$_SESSION['name_s'])
                  echo "<option value=\"$lidi[$i]\" selected=\"selected\">{$lidi[$i]}</option>\n";
                else
                  echo "<option value=\"$lidi[$i]\">{$lidi[$i]}</option>\n";
                  }  
             ?>
          </select>
        </td>
        <td>  
          &nbsp;<input id="pom_color" type="button" style="background:<?php echo $_SESSION['color_s'];?>;width:22px;" value="&nbsp;" onClick="cp2.select(document.forms[0].color,document.getElementById('pom_color'),'pom_color');return false;">&nbsp;
        </td>
        <td>Zpráva&nbsp;:&nbsp;</td>
        <td>
          <input type="text" name="message" id="message" size="62"/>&nbsp;
        </td>
        <td>
          <input type="submit" value="Poslat">
        </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="left">
          &nbsp;
          </td>
          <td>&nbsp;</td><td>&nbsp;</td>
          <td align="center">
            <img onclick="Smile(' :-D ')" src="/zdroje/icons/smileys/1.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :oD ')" src="/zdroje/icons/smileys/15.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :-)) ')" src="/zdroje/icons/smileys/2.gif" width="15" height="15" align="middle">    
            <img onclick="Smile(' :-) ')" src="/zdroje/icons/smileys/3.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' ;-) ')" src="/zdroje/icons/smileys/4.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :-P ')" src="/zdroje/icons/smileys/5.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :oP ')" src="/zdroje/icons/smileys/16.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' %-) ')" src="/zdroje/icons/smileys/17.gif" width="16" height="16" align="middle">
            <img onclick="Smile(' :-| ')" src="/zdroje/icons/smileys/6.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :-/ ')" src="/zdroje/icons/smileys/7.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :( ')" src="/zdroje/icons/smileys/8.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' X[] ')" src="/zdroje/icons/smileys/12.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :´-( ')" src="/zdroje/icons/smileys/9.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' :´o( ')" src="/zdroje/icons/smileys/19.gif" width="21" height="16" align="middle">
            <img onclick="Smile(' :-O ')" src="/zdroje/icons/smileys/10.gif" width="15" height="15" align="middle">
            <img onclick="Smile(' B-] ')" src="/zdroje/icons/smileys/11.gif" width="21" height="15" align="middle">
            <img onclick="Smile(' :_) ')" src="/zdroje/icons/smileys/13.gif" width="50" height="15" align="middle">
            <img onclick="Smile(' :-! ')" src="/zdroje/icons/smileys/18.gif" width="22" height="19" align="middle">
          </td>
          <td>
            <div id="obalTimeline" style="border: 0px solid rgb(108, 206, 167); margin: 0pt ! important; padding: 0pt ! important; width: 32px; height:11px;" align="left" height="11">
              <img id="timeline" width="30" vspace="0" hspace="0" height="9" src="http://1.im.cz/l/d.gif" style="margin: 0pt ! important; padding: 0pt ! important; background-color: rgb(23, 182, 26); background-image: none; background-repeat: repeat; background-attachment: scroll; background-position: 0%;" bgcolor="#17B61A"/>
            </div>
          </td>
        </tr>
        <!--<tr>
        <td>Email (Optional) : </td>
        <td><input size="50" type="text" name="email"></td>
        </tr>
        <tr>
        <td>Subject : </td>
        <td><input size="50" type="text" name="subject"></td>
        </tr>
        <tr>
        <td>Message : </td>
        <td><input type="text" name="message" wrap="virtual" length="100"/></td>
        </tr>
        <tr>
        <td>Link URL (Optional) : </td>
        <td><input size="50" type="text" name="linkurl"></td>
        </tr>
        <tr>
        <td>Link Title (Optional) : </td>
        <td><input size="50" type="text" name="linktitle"></td>
        </tr>
        <tr>
        <td colspan="2"><input type="submit" value="Poslat zprávu"></td>
        </tr>-->
        </tbody>
        </table>
        <input type="hidden" name="color" id="color" value="<?php echo $_SESSION['color_s'];?>"/> 
          
    </form>
 </div>  
 </div>
 <div style="margin:7px 0px 0px 7px;text-align: left;">
  <span style="font-size: 60%;"><span style="font-weight: bold;">*</span>&nbsp;Chat se AJAXově refrešuje každých 15 vteřin, tak snad to nezpůsobí problémy (; .</span><br/>
  <span style="font-size: 60%;"><span style="font-weight: bold;">*</span>&nbsp;Pro naše hackery-samotnou dvojtečku v textu už chat překousne (javascript si nejdřív někde otestujte (; ).</span><br/> 
  <span style="font-size: 60%;"><span style="font-weight: bold;">*</span>&nbsp;Dodělán překlad některých obrácených a zkrácených smajlíků.</span>
 </div>  
 </td>
 </tr>
 </table>
 <br/>
 </center>
 
<SCRIPT LANGUAGE="JavaScript">cp.writeDiv()</SCRIPT>

<script type="text/javascript">
      /** odeslání XMLHttp požadavku* @param function obsluha funkce zajišťující obsluhu při změně stavu požadavku, dostane parametr s XMLHttp objektem*
       *      @param string method GET|POST|...
       *      @param string url URL požadavku
       *      @param string [content] tělo zprávy
       *      @param array [headers] pole předaných hlaviček ve tvaru { 'hlavička': 'obsah' }
       *      @return bool true v případě úspěchu, false jinak
       *      @copyright Jakub Vrána, http://php.vrana.cz*/
        function send_xmlhttprequest(obsluha, method, url, content, headers) { 
          
          var xmlhttp = (window.XMLHttpRequest ? new XMLHttpRequest : (window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : false));   
          
          if (!xmlhttp) {       
            return false;   
          }  
           
          xmlhttp.open(method, url);
             
          xmlhttp.onreadystatechange = function() {       
            obsluha(xmlhttp);   
          };   
          if (headers) {       
            for (var key in headers) {           
              xmlhttp.setRequestHeader(key, headers[key]);       
            }   
          }   
          xmlhttp.send(content);   
          return true;
        }
        
      </script>
      
      <script type='text/javascript'> 
        //setInterval("send_xmlhttprequest(obsluha,'GET','chat/ajaxRequest.php')",15000) // časovač 15 sec. 
        setInterval("aktualizuj()",500) // časovač 1 sec.
      </script> 
      
      <script type='text/javascript'>
      
        function obsluha(xmlhttp) {
          if (xmlhttp.readyState == 4) {
            document.getElementById('kecaciDiv').innerHTML = xmlhttp.responseText;
          }
          
          obalCitac = document.getElementById('obalTimeline');
          
          citac = document.getElementById('timeline');
          citac.width = 30;
          citac.height = 9;
          
          obalCitac.style.textAlign = 'left';
          
          citac.src = 'http://1.im.cz/l/d.gif';
        }
        
        function aktualizuj() {
        
          citac = document.getElementById('timeline');            
          
          citac.width = citac.width - 1;
          
          if(citac.width == 0) {
            
            obalCitac = document.getElementById('obalTimeline'); 
          
            citac.width = 15;
            citac.height = 15;
            citac.src = '/zdroje/icons/spinner.gif';
            obalCitac.style.textAlign = 'center';
            send_xmlhttprequest(obsluha,'GET','chat/ajaxRequest.php');
          }
        }
        
      </script>
      
      <script type='text/javascript'>
      
        elJmeno = document.getElementById('chat_name_select');
        jmeno = getCookie('volejbal_chat_jmeno');
        
        if(jmeno != null) {
          for (i=0; i<elJmeno.options.length; i++) {
          
        		if (elJmeno.options[i].value == jmeno) {
        			elJmeno.options[i].selected = true;       	
              
        			}
        		}
      	}

        
        elBarvaHidden = document.getElementById('color');
        elBarvaButton = document.getElementById('pom_color');
        
        barva = getCookie('volejbal_chat_barva');
        
        if(barva != null) {
          elBarvaHidden.value = barva;
          elBarvaButton.style.background = barva;
        }
      
      </script>
