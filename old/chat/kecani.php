<?php
  $old_messages = null;
  $print_titles = null;
  $email = " ";
  $linkurl = " ";
  $linktitle = " ";
  
  
  
    ######################################################################
    # DekodujSmajly
    # -------------
    # program v promenne $co premeni textove smajly na graficke
    # (c) blog.JakubDvorak.eu
    # Tohle je svobodny software, sirte! ;-)
    ######################################################################
    
    function DekodujSmajly($co) {
    
      /*
      V pripade nevyuziti css stylu, nechte promennou $cssstyle prazdnou ($cssstyle="")
      Pokud mate css styl ulozeny v externim souboru, samozrejme muzete do promenne vlozit
      tag class, napriklad $cssstyle="class=\"smajlici\""
      */
        //smajlici normal
        $co = str_replace(':-D', "<img src=\"zdroje/icons/smileys/1.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':-))',"<img src=\"zdroje/icons/smileys/2.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':oD', "<img src=\"zdroje/icons/smileys/15.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);  
        $co = str_replace(':-)',"<img src=\"zdroje/icons/smileys/3.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(';-)',"<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':-P',"<img src=\"zdroje/icons/smileys/5.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':oP',"<img src=\"zdroje/icons/smileys/16.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace('%-)',"<img src=\"zdroje/icons/smileys/17.gif\" width=\"16\" height=\"16\" border=\"0\" />", $co);
        $co = str_replace(':-|',"<img src=\"zdroje/icons/smileys/6.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':-/',"<img src=\"zdroje/icons/smileys/7.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':(',"<img src='zdroje/icons/smileys/8.gif' width='15' height='15' border='0' />", $co);
        $co = str_replace('X[]',"<img src=\"zdroje/icons/smileys/12.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':´-(',"<img src=\"zdroje/icons/smileys/9.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':´o(',"<img src=\"zdroje/icons/smileys/19.gif\" width=\"21\" height=\"16\" border=\"0\" />", $co);
        $co = str_replace(':-O',"<img src=\"zdroje/icons/smileys/10.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace('B-]',"<img src=\"zdroje/icons/smileys/11.gif\" width=\"21\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':_)',"<img src=\"zdroje/icons/smileys/13.gif\" width=\"50\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':-!',"<img src=\"zdroje/icons/smileys/18.gif\" width=\"22\" height=\"19\" border=\"0\" />", $co);
        //smajlici kratky
        $co = str_replace(':D', "<img src=\"zdroje/icons/smileys/1.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':))',"<img src=\"zdroje/icons/smileys/2.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);         
        $co = str_replace(':)',"<img src=\"zdroje/icons/smileys/3.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(';)',"<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':P',"<img src=\"zdroje/icons/smileys/5.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace(':|',"<img src=\"zdroje/icons/smileys/6.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        //$co = str_replace(':/',"<img src=\"zdroje/icons/smileys/7.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\":-/\" />", $co);
        $co = str_replace(':(',"<img src='zdroje/icons/smileys/8.gif' width='15' height='15' border='0' />", $co);
        $co = str_replace('X[]',"<img src='zdroje/icons/smileys/12.gif' width='15' height='15' border='0' />", $co);
        
        //obraceny smajlici
        
        $co = str_replace('(:',"<img src=\"zdroje/icons/smileys/3.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace('(;',"<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace('((:',"<img src=\"zdroje/icons/smileys/2.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        
        $co = str_replace('(-:',"<img src=\"zdroje/icons/smileys/3.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace('(-;',"<img src=\"zdroje/icons/smileys/4.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        $co = str_replace('((-:',"<img src=\"zdroje/icons/smileys/2.gif\" width=\"15\" height=\"15\" border=\"0\" />", $co);
        
        $co = str_replace('::',";;", $co);
      
      
      return $co;
    }


    ////////////////////////////////////////
    //////////
    // A php only message board.//
    // All source written by Ian Hickman. Au
    //     g 2000. //
    // Use with thread.php and message.txt 
    //
    // Feel free to use this.//
    ////////////////////////////////////////
    //////////
    // open message file
    $message_array = file( "/3w/kvalitne.cz/v/volejbalek/chat/messages.txt" );
    $number_of_threads=0;
    $print_titles = "";
    // store the last entry to avoid duplica
    $lidi = array("Agáta","Bergy","Evča","Hory","Huďák","Jakub","Jarda","Ivana","Kasička","Katka Voč.","Lucka","Luďa","Majda","Michal","Mirka","Míša","Martin","Michal","Ondra","Patrik","Gali","Štěpán","Tereza","Tomáš J.","Tomáš R.","Tomáš V.","Vojta","Winky","Zuzka");
    //array("Taker","Čuník","Tonda","Peťa B.","Peťa H.","Bambík","Vendy","Hanka","Mahony","Kosťa","Tuňák","Romča","Danča","Jana H.","Janča","Míša","Lukáš","Fík","Slávek","Jája","Standa","Katka","Břéťa","Míša(Vondry)","Vondry","Pták");
    //array("Taker","Čuník","Tonda","Peťa B.","Peťa H.","Bambík","Vendy","Hanka","Mahony","Kosťa","Tuňák","Romča","Vous","Jana H.","Janča","Míša","Lukáš","Slávek","Jája","Standa","Břéťa","Míša(Vondry)","Vondry","Pták");
    ksort($lidi);
    
    $subject = "volejbal";
    if(!isset($color)) $color = 'black';

    //     tes
    if( isset($message_array[0]) ){
      list( $fnumber, $fsubject, $fname, $femail, $fdate, $ftext, $flinkurl, $flinktitle, ) = split("::", $message_array[0] );
    }
    // if something has been entered and it 
    //     is not the same as the last one
    if( isset($_REQUEST["name"]) && $_REQUEST["name"]!="" /*&& $subject!=""*/ && isset($_REQUEST["message"]) && $_REQUEST["message"]!="" /*&& ( $name!=$fname || $subject!=$fsubject )*/ ){
    // load all old messages
    for( $counter=0; $counter<100; $counter++ ){
    // split message field up
    if( isset($message_array[$counter]) ){
    list( $pnumber, $psubject, $pname, $pemail, $pdate, $ptext, $plinkurl, $plinktitle ) = split("::", $message_array[$counter] );
    // check for vality should be as all mes
    //     sages are valid when entered
    if( $pnumber && $psubject && $ptext && $pname ){
    // count the number of messages
    if( $pnumber > $number_of_threads ){
    $number_of_threads=intval($pnumber);
    }
    // add all the old messages into old_mes
    //     sages string
    $old_messages .= $message_array[$counter];
    }
    }
    }
    // create new message...
    // add date in format hh:mm:ss dd/mm/yy
    $date=date("H:i d.m.");
    // replace newlines with <br>'s
    $message=ereg_replace( "\n", "<br>", DekodujSmajly($message) );
    // format the new message
    if($_REQUEST['color'] != '#EEFFFF')
      $new_message = ($number_of_threads+1)."::".$subject."::<span style=\"color:".$_REQUEST['color'].";\">".$_REQUEST['name']."</span>::".$email."::".$date."::".$message;
    else
      $new_message = ($number_of_threads+1)."::".$subject."::<span style=\"color:pink;\">".$_REQUEST['name']."</span>::".$email."::".$date."::".$message;  
    //strip html chars
    //$new_message = htmlspecialchars( $new_message );
    if( $linkurl && !(ereg( "^http:\/\/", $linkurl )) ){
    $linkurl = "http://".$linkurl;
    }
    $new_message .= "::$linkurl::$linktitle\n";
    // open message file
    $open_file = fopen( "/3w/kvalitne.cz/v/volejbalek/chat/messages.txt", "w");
    // add message and old messages to file
    // new message will be at the top of the
    //     board
    fputs($open_file, stripslashes( $new_message ));
    fputs($open_file, $old_messages);
    fclose($open_file);
    }
    // indent is used to align are the messa
    //     ge threads
    //$indent=0;
    // open message file
    $message_array = file( "/3w/kvalitne.cz/v/volejbalek/chat/messages.txt" );
    // loop through message file lines
    for( $counter=0; $counter<100; $counter++ ){
    // split the message field
    if( isset($message_array[$counter]) ){
    list( $pnumber, $psubject, $pname, $pemail, $pdate, $ptext ) = split("::", $message_array[$counter] );
    // check that it is valid, should be due
    //     to validy check on message post
    if( $pnumber && $psubject && $ptext && $pname ){
    // indent if message is a reply
    /*if( intval($pnumber)!=$pnumber && $indent==0){
    $print_titles .= "<ul>\n";
    $indent=1;
    }
    if( intval($pnumber)==$pnumber && $indent==1){
    $print_titles .= "</ul>\n";
    $indent=0;
    }*/
    // add the titles to the print_titles st
    //     ring
    //$print_titles .= "<li><a href=\"thread.php#$pnumber?thread=$pnumber\">$psubject</a> ";
    // if an email address is supplied link 
    //     to it
    //if( $pemail ){
    //$print_titles .= "- <a href=\"mailto:$pemail\">$pname</a> $pdate\n";
    //} else {
    $print_titles .= "<li> <span class='name'>$pname</span> <span class='date'>($pdate)</span>: <span class='message'>$ptext</span></li>";
    //} 
    }
    }
    }
    // print the title array
    echo( "<ul style=\"list-style-type:none;margin:0px;padding:0px;\">\n" );
    echo( $print_titles );
    echo( "</ul>\n" );
    echo ("<!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0--><!--WZ-REKLAMA-1.0-->");
    // check that there are no lists open
    //if( $indent==1 ){
    //echo( "</ul>\n" );
    //}
    //session_start();
?>
