<?
include("config.php");
updatecookie();

$title="Credit Sweeper";
include("languages/$lang");

include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


bigtitle();

//this code will gather credits from all of your planets below a certain amount, and transfer them to your ship! (I hope)  It will charge a fee for the transfer of .5%

?>
<br><h2><center>Welcome to the Credit Sweeper!</center></h2><br><br>
Enter in an amount in the box provided below, and we will search though all of you planets and find thoose that have less than your amount specified.  Then we will transfer all of thoose credits from your planets that match that criteria to your ship.  There is a nominal transfer fee of .5%, which is automatically deducted from the total.  <br><br>
<form action=credittrans2.php method=post>
Enter Max transfer amount here:  <input type=text name=maxamount><br><br>
<button type="submit">Go to Confirmation Page</button>
</form>
<?
//---------------------------------------------------------------------------------
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
  ?>
