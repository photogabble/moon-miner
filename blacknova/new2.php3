<?
$title="Create New Player Phase Two";
include("header.php3");

include("config.php3");

bigtitle();
connectdb();

if($account_creation_closed)
{
  die($account_creation_closed_message);
}
$character=ereg_replace("[^[:digit:][:space:][:alpha:]]"," ",$character);
$shipname=ereg_replace("[^[:digit:][:space:][:alpha:]]"," ",$shipname);
$result = mysql_query ("select email, character_name, ship_name from ships where email='$username' OR character_name='$character' OR ship_name='$shipname'");
$flag=0;
if ($username=='' || $character=='' || $shipname=='' ) { echo "E-mail, ship name, and character name may not be blank.<BR>"; $flag=1;}

if ($result>0)
{
  while ($row = mysql_fetch_row ($result))
  {
    
    if ($row[0]==$username) { echo "E-mail address $username, is already in use.  If you have forgotten your password, click <a href=mail.php3?mail=$username>here</a> to have it e-mailed to you.<BR>"; $flag=1;}
    if ($row[1]==$character) { echo "Character name $character, is already in use.<BR>"; $flag=1;}
    if ($row[2]==$shipname) { echo "Ship name $shipname, is already in use.<BR>"; $flag=1;}
    
  }
}


if ($flag==0)
{
  
  /* insert code to add player to database */
  $makepass="";
  $syllables="er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
  $syllable_array=explode(",", $syllables);
  srand((double)microtime()*1000000);
  for ($count=1;$count<=4;$count++) {
    if (rand()%10 == 1) {
      $makepass .= sprintf("%0.0f",(rand()%50)+1);
    } else {
      $makepass .= sprintf("%s",$syllable_array[rand()%62]);
    }
  }
  $stamp=date("Y-m-d H:i:s");
  $result2 = mysql_query("INSERT INTO ships VALUES('','$shipname','N','$character','$makepass','$username',0,0,0,0,0,0,0,0,0,0,$start_armour,0,$start_credits,0,0,0,0,$start_energy,0,$start_fighters,$start_turns,'','N',0,1,0,0,'N','N',0,0, '$stamp',0,0,0,1,0,'N','$ip')");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br>";
  } else {
    mail("$username", "Traders Password", "Greetings,\n\nSomeone from the IP address $ip requested that your password for Traders be sent to you.\n\nYour password is: $makepass\n\nThank you\n\nThe Traders web team.\n\nhttp://$SERVER_NAME","From: $admin_mail\nReply-To: webmaster@$SERVER_NAME\nX-Mailer: PHP/" . phpversion());
    
    echo "Password has been sent to $username.<BR><BR>";
    echo "Click <A HREF=login.php3>here</A> to go to the login screen.";
    
  }
} else {
  
  echo "Please go back or click <a href=new.php3>here</a> and try again.";
}

include("footer.php3");
?>
