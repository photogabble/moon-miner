<?

	include("config.php3");

	updatecookie();



	$title="Ship Report";

	include("header.php3");




        connectdb();

        if (checklogin()) {die();}






                $result = mysql_query ("SELECT * FROM ships WHERE email='$username'");

                $playerinfo=mysql_fetch_array($result);


        bigtitle();


                        echo "Report on $playerinfo[ship_name], Captained by:  $playerinfo[character_name]<BR><BR>";

                        echo "<b>Ship Component levels:</b><BR><BR>";

                        echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">

                                        <tr>

                                                <td >

                                                Hull:

                                                </td>

                                                <td >

                                                $playerinfo[hull]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Engines:

                                                </td>

                                                <td >

                                                $playerinfo[engines]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Power:

                                                </td>

                                                <td >

                                                $playerinfo[power]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Computer:

                                                </td>

                                                <td >

                                                $playerinfo[computer]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Sensors:

                                                </td>

                                                <td >

                                                $playerinfo[sensors]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Beams:

                                                </td>

                                                <td >

                                                $playerinfo[beams]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Torpedo Launchers:

                                                </td>

                                                <td >

                                                $playerinfo[torp_launchers]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Armour:

                                                </td>

                                                <td >

                                                $playerinfo[armour]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Shields:

                                                </td>

                                                <td >

                                                $playerinfo[shields]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td >

                                                Cloak:

                                                </td>

                                                <td >

                                                 $playerinfo[cloak]

                                                </td>

                                        </tr>

                                </table><BR>";

                echo "<b>Armament:</b><BR><BR>";

                echo "<table>

                                        <tr>

                                                <td>

                                                Armour Points:

                                                </td>

                                                <td>

                                                $playerinfo[armour_pts]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Fighters:

                                                </td>

                                                <td>

                                                $playerinfo[ship_fighters]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Torpedoes:

                                                </td>

                                                <td>

                                                $playerinfo[torps]

                                                </td>

                                        </tr>

                                </table><BR>";

                echo "<b>Carrying:</b><BR><BR>";

                echo "<table>

                                        <tr>

                                                <td>

                                                Credits:

                                                </td>

                                                <td>

                                                $playerinfo[credits]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Colonists:

                                                </td>

                                                <td>

                                                $playerinfo[ship_colonists]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Energy:

                                                </td>

                                                <td>

                                                $playerinfo[ship_energy]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Ore:

                                                </td>

                                                <td>

                                                $playerinfo[ship_ore]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Organics:

                                                </td>

                                                <td>

                                                $playerinfo[ship_organics]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Goods:

                                                </td>

                                                <td>

                                                $playerinfo[ship_goods]

                                                </td>

                                        </tr>

                                </table><BR>";

                echo "<b>Devices:</b><BR><BR>";

                echo "<table>

                                        <tr>

                                                <td>

                                                Warp Editors:

                                                </td>

                                                <td>

                                                $playerinfo[dev_warpedit]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Genesis Torpedoes:

                                                </td>

                                                <td>

                                                $playerinfo[dev_genesis]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Mine Deflectors:

                                                </td>

                                                <td>

                                                $playerinfo[dev_minedeflector]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Emergency Warp Devices:

                                                </td>

                                                <td>

                                                $playerinfo[dev_emerwarp]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Escape Pods:

                                                </td>

                                                <td>

                                                $playerinfo[dev_escapepod]

                                                </td>

                                        </tr>

                                        <tr>

                                                <td>

                                                Fuel Scoop:

                                                </td>

                                                <td>

                                                $playerinfo[dev_fuelscoop]

                                                </td>

                                        </tr>

                                </table><BR><BR>";

                echo "Click <a href=main.php3>here</a> to return to Main Menu.";



        include("footer.php3");

?>
