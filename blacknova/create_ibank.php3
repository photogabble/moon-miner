<?

if(!createuniverseincluded)
{
  include("config.php3");
  updatecookie();
  
  $title="Create Universe";
  include("header.php3");
  
  connectdb();
  bigtitle();
}

if($swordfish != $adminpass)
{
  echo "<form action=create_universe.php3 method=post>";
  echo "Password: <input type=password name=swordfish size=20 maxlength=20><BR><BR>";
  echo "<input type=submit value=Submit><input type=reset value=Reset>";
  echo "</form>";
}
elseif($swordfish == $adminpass && $engage == "")
{
  echo "done with setting up the universe.<BR>Lets set up the IGB...<BR>";
  
  /////////////////////////////////////////////////////////////////////////////
  // iBank accounts creation
  // Author: dfroberg@users.sourceforge.net email if you want it somewhere else.
  echo "Creating iBank Accounts table...<BR>";
  // Contains some fields for possible future implementation 
  mysql_query("CREATE TABLE ibank_accounts (id bigint(20) DEFAULT '0' NOT NULL, ballance bigint(20)  DEFAULT '0', loan bigint(20)  DEFAULT '0', ibank_shareholder int(11) DEFAULT '0' NOT NULL, ibank_employee int(1) DEFAULT '0' NOT NULL, ibank_owner int(1) DEFAULT '0' NOT NULL, PRIMARY KEY (id) );");
  // Insert default
  echo "Creating iBank default account...<BR>";
  mysql_query("INSERT INTO ibank_accounts (id,ballance,loan,ibank_shareholder,ibank_employee,ibank_owner) VALUES ($ibank_owner,1000000000000000,0,100,1,1);");
}
else
{
  echo "Huh?";
}
if(!createuniverseincluded)
{
  include("footer.php3");
}

?>
