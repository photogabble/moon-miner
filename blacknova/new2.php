<?
include("config.php");
include("languages/$lang");

$title=$l_new_title2;

include("header.php");


bigtitle();

connectdb();

if($account_creation_closed)
{
  die($l_new_closed_message);
}
$character=ereg_replace("[^[:digit:][:space:][:alpha:][\']]"," ",$character);
$shipname=ereg_replace("[^[:digit:][:space:][:alpha:][\']]"," ",$shipname);

if(!get_magic_quotes_gpc())
{
  $username = addslashes($username);
  $character = addslashes($character);
  $shipname = addslashes($shipname);
}

$result = $db->Execute ("select email, character_name, ship_name from $dbtables[ships] where email='$username' OR character_name='$character' OR ship_name='$shipname'");
$flag=0;
if ($username=='' || $character=='' || $shipname=='' ) { echo "$l_new_blank<BR>"; $flag=1;}

$username = $HTTP_POST_VARS['username'];
if ($result>0)
{
  while (!$result->EOF)
  {
    $row = $result->fields;
    if ($row[0]==$username) { echo "$l_new_inuse  $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<BR>"; $flag=1;}
    if ($row[1]==$character) { echo "$l_new_inusechar<BR>"; $flag=1;}
    if ($row[2]==$shipname) { echo "$l_new_inuseship<BR>"; $flag=1;}
    $result->MoveNext();
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
  $query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM $dbtables[ships]");
  $res = $query->fields;

  $mturns = $res[mturns];

  if($mturns > $max_turns)
    $mturns = $max_turns;

  $result2 = $db->Execute("INSERT INTO $dbtables[ships] VALUES('','$shipname','N','$character','$makepass','$username',0,0,0,0,0,0,0,0,0,0,$start_armour,0,$start_credits,0,0,0,0,$start_energy,0,$start_fighters,$mturns,'','N',0,0,0,0,'N','N',0,0, '$stamp',0,0,0,0,'N','$ip',0,0,0,0,'Y','N','N','Y',' ','$default_lang', 'Y')");
  if(!$result2) {
    echo $db->ErrorMsg() . "<br>";
  } else {
    $result2 = $db->Execute("SELECT ship_id FROM $dbtables[ships] WHERE email='$username'");
    $shipid = $result2->fields;

 $l_new_message = str_replace("[pass]", $makepass, $l_new_message);
    mail("$username", "$l_new_topic", "$l_new_message\n\nhttp://$gamedomain","From: $admin_mail\nReply-To: $admin_mail\nX-Mailer: PHP/" . phpversion());

    $db->Execute("INSERT INTO $dbtables[zones] VALUES('','$character\'s Territory', $shipid[ship_id], 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
    $db->Execute("INSERT INTO $dbtables[ibank_accounts] VALUES($shipid[ship_id],0,0)");

    echo "$l_new_pwsent<BR><BR>";
    echo "<A HREF=login.php>$l_clickme</A> $l_new_login";

  }
} else {

  echo $l_new_err;
}

include("footer.php");
?>
