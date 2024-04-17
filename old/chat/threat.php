 <?php
    ////////////////////////////////////////
    //////////
    // A php only message board.//
    // All source written by Ian Hickman. Au
    //     g 2000. //
    // Use with chat.php and message.txt//
    // Feel free to use this.//
    ////////////////////////////////////////
    //////////
    // open message file
    $message_array = file( "messages.txt" );
    $threadnumber=0;
    // store the last entry to avoid duplica
    //     tes
    list( $fnumber, $fsubject, $fname, $femail, $fdate, $ftext, $flinkurl, $flinktitle ) = split("::", $message_array[0] );
    // if something has been entered and it 
    //     is not the same as the last one
    if( $name!="" && $subject!="" && $message!="" && ( $name!=$fname || $subject!=$fsubject ) ){
    // loop through all old messages
    for( $counter=0; $counter<100; $counter++ ){
    // check message line exists
    if( $message_array[$counter] ){
    // split message field up
    list( $pnumber, $psubject, $pname, $pemail, $pdate, $ptext, $flinkurl, $flinktitle ) = split("::", $message_array[$counter] );
    // check for validy of message field. Sh
    //     ould be valid as messages entered are va
    //     lidated
    if( $pnumber && $psubject && $ptext && $pname ){
    // find out where to put the new message
    //     
    if( intval($pnumber) < $thread ){
    $old_messages .= $message_array[$counter];
    }
    if( intval($pnumber) == $thread ){
    $messages_after .= $message_array[$counter];
    $threadnumber=$pnumber+0.01;
    }
    if( intval($pnumber) > $thread ){
    $messages_after .= $message_array[$counter];
    }
    }
    }
    }
    // create new message
    $date=date("H:i:s d/m/y");
    $message=ereg_replace( "\n", "<br>", $message );
    $new_message = "$threadnumber::$subject::$name::$email::$date::$message";
    // strip html chars
    $new_message = htmlspecialchars( $new_message );
    if( $linkurl && !(ereg( "^http:\/\/", $linkurl )) ){
    $linkurl = "http://".$linkurl;
    } 
    $new_message .= "::$linkurl::$linktitle\n";
    // open message file
    $open_file = fopen( "messages.txt", "w");
    // strip html stuff and add message to f
    //     ile
    fputs($open_file, $messages_after);
    fputs($open_file, stripslashes( $new_message ));
    fputs($open_file, $old_messages);
    fclose($open_file);
    }
    // loop through all messages copying the
    //     title and messages into separate arrays
    $message_array = file( "messages.txt" );
    for( $counter=0; $counter<100; $counter++ ){
    // split the message field
    list( $pnumber, $psubject, $pname, $pemail, $pdate, $ptext, $plinkurl, $plinktitle ) = split("::", $message_array[$counter] );
    // check that it is valid although it sh
    //     ould be due to earlier validity checks
    // also check to see if the message is t
    //     he thread (or followup) that we want
    if( intval($pnumber)==intval($thread) && $psubject && $ptext && $pname ){
    // add the message to print_messages str
    //     ing
    $print_messages .= "<h3><a name=$pnumber>$psubject</a></h3>\n";
    // if an email address is supplied link 
    //     to it
    if( $pemail ){
    $print_messages .= "<p>Posted by <a href=\"mailto:$pemail\">$pname</a> on $pdate</p>\n<p>$ptext</p>";
    } else {
    $print_messages .= "<p>Posted by $pname on $pdate</p>\n<p>$ptext</p>";
    }
    // if a url has been submitted draw it u
    //     p
    if( $plinkurl && $plinktitle ){
    $print_messages .= "<p><a href=\"$plinkurl\" target=\"_blank\">$plinktitle</a></p>\n";
    } else {
    $print_messages .= "\n";
    }
    }
    }
    // print the messages
    echo( $print_messages );
    ?>
    <hr>
    <form action="thread.php" method="post">
    <?php
    // pass the thread value when submitted
    echo( "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n" ); 
    ?>
    <table border="0">
    <tr>
    <td>Name : </td>
    <td><input size="50" type="text" name="name"></td>
    </tr>
    <tr>
    <td>Email (Optional) : </td>
    <td><input size="50" type="text" name="email"></td>
    </tr>
    <tr>
    <td>Subject : </td>
    <td><input size="50" type="text" name="subject"></td>
    </tr>
    <tr>
    <td>Message : </td>
    <td><textarea cols=55 rows=10 name="message" wrap="virtual"></textarea></td>
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
    <td colspan="2"><input type="submit" value="Post Followup"><input type="reset"></td>
    </tr>
    </table>
    <hr>
    <center>
    [ <a href="chat.php">Main Board</a> ]
    </center>
    <hr>
