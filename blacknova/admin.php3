<?

include("config.php3");
updatecookie();

$title="Administration";
include("header.php3");

connectdb();
bigtitle();

$module = $menu;

if($swordfish != $adminpass)
{
  echo "<FORM ACTION=admin.php3 METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  if(empty($module))
  {
    echo "Welcome to the BlackNova Traders administration module<BR><BR>";
    echo "Select a function from the list below:<BR>";
    echo "<FORM ACTION=admin.php3 METHOD=POST>";
    echo "<SELECT SIZE=1 NAME=menu>";
    echo "<OPTION VALUE=useredit SELECTED>User editor</OPTION>";
    echo "<OPTION VALUE=univedit>Universe editor</OPTION>";
    echo "<OPTION VALUE=linkedit>Link editor</OPTION>";
    echo "<OPTION VALUE=zoneedit>Zone editor</OPTION>";
    echo "</SELECT>";
    echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
    echo "<INPUT TYPE=SUBMIT VALUE=Submit>";
    echo "</FORM>";
  }
  else
  {
    if($module == "useredit")
    {
      echo "<B>User editor</B>";
    }
    elseif($module == "univedit")
    {
      echo "<B>Universe editor</B>";
    }
    elseif($module == "linkedit")
    {
      echo "<B>Link editor</B>";
    }
    elseif($module == "zoneedit")
    {
      echo "<B>Zone editor</B>";
    }
    else
    {
      echo "Unknown function";
    }

    echo "<BR><BR>";
    echo "<FORM ACTION=admin.php3 METHOD=POST>";
    echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
    echo "<INPUT TYPE=SUBMIT VALUE=\"Return to main menu\">";
    echo "</FORM>";
  }
}
  
include("footer.php3");

?> 
