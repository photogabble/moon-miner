<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: admin.php

include "config.php";
include "languages/$lang";
updatecookie();

$title = $l_admin_title;
include "header.php";

connectdb();
bigtitle();

function CHECKED($yesno)
{
  return(($yesno == "Y") ? "CHECKED" : "");
}

function YESNO($onoff)
{
  return(($onoff == "ON") ? "Y" : "N");
}

$module = $menu;

if ($swordfish != $adminpass)
{
  echo "<form action=admin.php method=post>";
  echo "Password: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
  echo "<input type=submit value=Submit><input type=reset value=Reset>";
  echo "</form>";
}
else
{
  if (empty($module))
  {
    echo "Welcome to the Blacknova Traders administration module<br><br>";
    echo "select a function from the list below:<br>";
    echo "<form action=admin.php method=post>";
    echo "<select name=menu>";
    echo "<option value=useredit selectED>User editor</option>";
    echo "<option value=univedit>Universe editor</option>";
    echo "<option value=sectedit>Sector editor</option>";
    echo "<option value=planedit>Planet editor</option>";
    echo "<option value=linkedit>Link editor</option>";
    echo "<option value=zoneedit>Zone editor</option>";
    echo "<option value=ipedit>IP bans editor</option>";
    echo "<option value=logview>Log Viewer</option>";
    echo "</select>";
    echo "<input type=hidden name=swordfish value=$swordfish>";
    echo "&nbsp;<input type=submit value=Submit>";
    echo "</form>";
  }
  else
  {
    $button_main = true;

    if ($module == "useredit")
    {
      echo "<B>User editor</B>";
      echo "<br>";
      echo "<form action=admin.php method=post>";
      if (empty($user))
      {
        echo "<select size=20 name=user>";
        $res = $db->Execute("select ship_id,character_name FROM $dbtables[ships] ORDER BY character_name");
        while (!$res->EOF)
        {
          $row=$res->fields;
          echo "<option value=$row[ship_id]>$row[character_name]</option>";
          $res->MoveNext();
        }
        echo "</select>";
        echo "&nbsp;<input type=submit value=Edit>";
      }
      else
      {
        if (empty($operation))
        {
          $res = $db->Execute("select * FROM $dbtables[ships] WHERE ship_id=$user");
          $row = $res->fields;
          echo "<table border=0 cellspacing=0 cellpadding=5>";
          echo "<tr><td>Player name</td><td><input type=text name=character_name value=\"$row[character_name]\"></td></tr>";
          echo "<tr><td>Password</td><td><input type=text name=password2 value=\"$row[password]\"></td></tr>";
          echo "<tr><td>E-mail</td><td><input type=text name=email value=\"$row[email]\"></td></tr>";
          echo "<tr><td>ID</td><td>$user</td></tr>";
          echo "<tr><td>Ship</td><td><input type=text name=ship_name value=\"$row[ship_name]\"></td></tr>";
          echo "<tr><td>Destroyed?</td><td><input type=CHECKBOX name=ship_destroyed value=ON " . CHECKED($row[ship_destroyed]) . "></td></tr>";
          echo "<tr><td>Levels</td>";
          echo "<td><table border=0 cellspacing=0 cellpadding=5>";
          echo "<tr><td>Hull</td><td><input type=text size=5 name=hull value=\"$row[hull]\"></td>";
          echo "<td>Engines</td><td><input type=text size=5 name=engines value=\"$row[engines]\"></td>";
          echo "<td>Power</td><td><input type=text size=5 name=power value=\"$row[power]\"></td>";
          echo "<td>Computer</td><td><input type=text size=5 name=computer value=\"$row[computer]\"></td></tr>";
          echo "<tr><td>Sensors</td><td><input type=text size=5 name=sensors value=\"$row[sensors]\"></td>";
          echo "<td>Armor</td><td><input type=text size=5 name=armor value=\"$row[armor]\"></td>";
          echo "<td>Shields</td><td><input type=text size=5 name=shields value=\"$row[shields]\"></td>";
          echo "<td>Beams</td><td><input type=text size=5 name=beams value=\"$row[beams]\"></td></tr>";
          echo "<tr><td>Torpedoes</td><td><input type=text size=5 name=torp_launchers value=\"$row[torp_launchers]\"></td>";
          echo "<td>Cloak</td><td><input type=text size=5 name=cloak value=\"$row[cloak]\"></td></tr>";
          echo "</table></td></tr>";
          echo "<tr><td>Holds</td>";
          echo "<td><table border=0 cellspacing=0 cellpadding=5>";
          echo "<tr><td>Ore</td><td><input type=text size=8 name=ship_ore value=\"$row[ship_ore]\"></td>";
          echo "<td>Organics</td><td><input type=text size=8 name=ship_organics value=\"$row[ship_organics]\"></td>";
          echo "<td>Goods</td><td><input type=text size=8 name=ship_goods value=\"$row[ship_goods]\"></td></tr>";
          echo "<tr><td>Energy</td><td><input type=text size=8 name=ship_energy value=\"$row[ship_energy]\"></td>";
          echo "<td>Colonists</td><td><input type=text size=8 name=ship_colonists value=\"$row[ship_colonists]\"></td></tr>";
          echo "</table></td></tr>";
          echo "<tr><td>Combat</td>";
          echo "<td><table border=0 cellspacing=0 cellpadding=5>";
          echo "<tr><td>Fighters</td><td><input type=text size=8 name=ship_fighters value=\"$row[ship_fighters]\"></td>";
          echo "<td>Torpedoes</td><td><input type=text size=8 name=torps value=\"$row[torps]\"></td></tr>";
          echo "<tr><td>Armor Pts</td><td><input type=text size=8 name=armor_pts value=\"$row[armor_pts]\"></td></tr>";
          echo "</table></td></tr>";
          echo "<tr><td>Devices</td>";
          echo "<td><table border=0 cellspacing=0 cellpadding=5>";
          echo "<tr><td>Beacons</td><td><input type=text size=5 name=dev_beacon value=\"$row[dev_beacon]\"></td>";
          echo "<td>Warp Editors</td><td><input type=text size=5 name=dev_warpedit value=\"$row[dev_warpedit]\"></td>";
          echo "<td>Genesis Torpedoes</td><td><input type=text size=5 name=dev_genesis value=\"$row[dev_genesis]\"></td></tr>";
          echo "<tr><td>Mine Deflectors</td><td><input type=text size=5 name=dev_minedeflector value=\"$row[dev_minedeflector]\"></td>";
          echo "<td>Emergency Warp</td><td><input type=text size=5 name=dev_emerwarp value=\"$row[dev_emerwarp]\"></td></tr>";
          echo "<tr><td>Escape Pod</td><td><input type=CHECKBOX name=dev_escapepod value=ON " . CHECKED($row[dev_escapepod]) . "></td>";
          echo "<td>FuelScoop</td><td><input type=CHECKBOX name=dev_fuelscoop value=ON " . CHECKED($row[dev_fuelscoop]) . "></td></tr>";
          echo "</table></td></tr>";
          echo "<tr><td>Credits</td><td><input type=text name=credits value=\"$row[credits]\"></td></tr>";
          echo "<tr><td>Turns</td><td><input type=text name=turns value=\"$row[turns]\"></td></tr>";
          echo "<tr><td>Current sector</td><td><input type=text name=sector value=\"$row[sector]\"></td></tr>";
          echo "</table>";
          echo "<br>";
          echo "<input type=hidden name=user value=$user>";
          echo "<input type=hidden name=operation value=save>";
          echo "<input type=submit value=Save>";
        }
        elseif ($operation == "save")
        {
          // update database
          $_ship_destroyed = empty($ship_destroyed) ? "N" : "Y";
          $_dev_escapepod = empty($dev_escapepod) ? "N" : "Y";
          $_dev_fuelscoop = empty($dev_fuelscoop) ? "N" : "Y";
          $db->Execute("UPDATE $dbtables[ships] SET character_name='$character_name',password='$password2',email='$email',ship_name='$ship_name',ship_destroyed='$_ship_destroyed',hull='$hull',engines='$engines',power='$power',computer='$computer',sensors='$sensors',armor='$armor',shields='$shields',beams='$beams',torp_launchers='$torp_launchers',cloak='$cloak',credits='$credits',turns='$turns',dev_warpedit='$dev_warpedit',dev_genesis='$dev_genesis',dev_beacon='$dev_beacon',dev_emerwarp='$dev_emerwarp',dev_escapepod='$_dev_escapepod',dev_fuelscoop='$_dev_fuelscoop',dev_minedeflector='$dev_minedeflector',sector='$sector',ship_ore='$ship_ore',ship_organics='$ship_organics',ship_goods='$ship_goods',ship_energy='$ship_energy',ship_colonists='$ship_colonists',ship_fighters='$ship_fighters',torps='$torps',armor_pts='$armor_pts' WHERE ship_id=$user");
          echo "Changes saved<br><br>";
          echo "<input type=submit value=\"Return to User editor\">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<input type=hidden name=menu value=useredit>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "</form>";
    }
    elseif ($module == "univedit")
    {
      echo "<B>Universe editor</B>";

        $title=$l_change_uni_title;
        echo "<br>Expand or Contract the Universe <br>";


        if (empty($action))
        {
        echo "<form action=admin.php method=post>";
        echo "Universe Size: <input type=text name=radius value=\"$universe_size\">";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        echo "<input type=hidden name=menu value=univedit>";
        echo "<input type=hidden name=action value=doexpand> ";
        echo "<input type=submit value=\"Play God\">";
        echo "</form>";
        }
        elseif ($action == "doexpand")
        {
        echo "<br><font size='+2'>Be sure to update your config.php file with the new universe_size value</font><br>";
        $result = $db->Execute("select sector_id FROM $dbtables[universe] ORDER BY sector_id ASC");
        while (!$result->EOF)
        {
                $row=$result->fields;
                $distance=mt_rand(1,$radius);
                $db->Execute("UPDATE $dbtables[universe] SET distance=$distance WHERE sector_id=$row[sector_id]");
                echo "Updated sector $row[sector_id] set to $distance<br>";
                $result->MoveNext();
        }

    }
        }
    elseif ($module == "sectedit")
    {
      echo "<H2>Sector editor</H2>";
      echo "<form action=admin.php method=post>";
      if (empty($sector))
      {
        echo "<H5>Note: Cannot Edit Sector 0</H5>";
        echo "<select size=20 name=sector>";
        $res = $db->Execute("select sector_id FROM $dbtables[universe] ORDER BY sector_id");
        while (!$res->EOF)
        {
          $row=$res->fields;
          echo "<option value=$row[sector_id]> $row[sector_id] </option>";
          $res->MoveNext();
        }
        echo "</select>";
        echo "&nbsp;<input type=submit value=Edit>";
      }
      else
      {
        if (empty($operation))
        {
          $res = $db->Execute("select * FROM $dbtables[universe] WHERE sector_id=$sector");
          $row = $res->fields;

          echo "<table border=0 cellspacing=2 cellpadding=2>";
          echo "<tr><td><tt>          Sector ID  </tt></td><td><font color=#6f0>$sector</font></td>";
          echo "<td align=Right><tt>  Sector Name</tt></td><td><input type=text size=15 name=sector_name value=\"$row[sector_name]\"></td>";
          echo "<td align=Right><tt>  Zone ID    </tt></td><td>";
                                      echo "<select size=1 name=zone_id>";
                                      $ressubb = $db->Execute("select zone_id,zone_name FROM $dbtables[zones] ORDER BY zone_name");
                                      while (!$ressubb->EOF)
                                      {
                                        $rowsubb=$ressubb->fields;
                                        if ($rowsubb[zone_id] == $row[zone_id])
                                        {
                                        echo "<option selectED=$rowsubb[zone_id] value=$rowsubb[zone_id]>$rowsubb[zone_name]</option>";
                                        } else {
                                        echo "<option value=$rowsubb[zone_id]>$rowsubb[zone_name]</option>";
                                        }
                                        $ressubb->MoveNext();
                                      }
                                      echo "</select></td></tr>";
          echo "<tr><td><tt>          Beacon     </tt></td><td colspan=5><input type=text size=70 name=beacon value=\"$row[beacon]\"></td></tr>";
          echo "<tr><td><tt>          Distance   </tt></td><td><input type=text size=9 name=distance value=\"$row[distance]\"></td>";
          echo "<td align=Right><tt>  Angle1     </tt></td><td><input type=text size=9 name=angle1 value=\"$row[angle1]\"></td>";
          echo "<td align=Right><tt>  Angle2     </tt></td><td><input type=text size=9 name=angle2 value=\"$row[angle2]\"></td></tr>";
          echo "<tr><td colspan=6>    <HR>       </td></tr>";
          echo "</table>";

          echo "<table border=0 cellspacing=2 cellpadding=2>";
          echo "<tr><td><tt>          Port Type  </tt></td><td>";
                                      echo "<select size=1 name=port_type>";
                                      $oportnon = $oportorg = $oportore = $oportgoo = $oportene = "value";
                                      if ($row[port_type] == "none") $oportnon = "selectED=none value";
                                      if ($row[port_type] == "organics") $oportorg = "selectED=organics value";
                                      if ($row[port_type] == "ore") $oportore = "selectED=ore value";
                                      if ($row[port_type] == "goods") $oportgoo = "selectED=goods value";
                                      if ($row[port_type] == "energy") $oportene = "selectED=energy value";
                                      echo "<option $oportnon=none>none</option>";
                                      echo "<option $oportorg=organics>organics</option>";
                                      echo "<option $oportore=ore>ore</option>";
                                      echo "<option $oportgoo=goods>goods</option>";
                                      echo "<option $oportene=energy>energy</option>";
                                      echo "</select></td>";
          echo "<td align=Right><tt>  Organics   </tt></td><td><input type=text size=9 name=port_organics value=\"$row[port_organics]\"></td>";
          echo "<td align=Right><tt>  Ore        </tt></td><td><input type=text size=9 name=port_ore value=\"$row[port_ore]\"></td>";
          echo "<td align=Right><tt>  Goods      </tt></td><td><input type=text size=9 name=port_goods value=\"$row[port_goods]\"></td>";
          echo "<td align=Right><tt>  Energy     </tt></td><td><input type=text size=9 name=port_energy value=\"$row[port_energy]\"></td></tr>";
          echo "<tr><td colspan=10>   <HR>       </td></tr>";
          echo "</table>";

          echo "<br>";
          echo "<input type=hidden name=sector value=$sector>";
          echo "<input type=hidden name=operation value=save>";
          echo "<input type=submit size=1 value=Save>";
        }
        elseif ($operation == "save")
        {
          // update database
          $secupdate = $db->Execute("UPDATE $dbtables[universe] SET sector_name='$sector_name',zone_id='$zone_id',beacon='$beacon',port_type='$port_type',port_organics='$port_organics',port_ore='$port_ore',port_goods='$port_goods',port_energy='$port_energy',distance='$distance',angle1='$angle1',angle2='$angle2' WHERE sector_id=$sector");
          if (!$secupdate) {
            echo "Changes to Sector record have FAILED Due to the following Error:<br><br>";
            echo $db->ErrorMsg() . "<br>";
          } else {
            echo "Changes to Sector record have been saved.<br><br>";
          }
          echo "<input type=submit value=\"Return to Sector editor\">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<input type=hidden name=menu value=sectedit>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "</form>";
    }
    elseif ($module == "planedit")
    {
      echo "<H2>Planet editor</H2>";
      echo "<form action=admin.php method=post>";
      if (empty($planet))
      {
        echo "<select size=15 name=planet>";
        $res = $db->Execute("select planet_id, name, sector_id FROM $dbtables[planets] ORDER BY sector_id");
        while (!$res->EOF)
        {
          $row=$res->fields;
          if ($row[name] == "")

            $row[name] = "Unnamed";

          echo "<option value=$row[planet_id]> $row[name] in sector $row[sector_id] </option>";
          $res->MoveNext();
        }
        echo "</select>";
        echo "&nbsp;<input type=submit value=Edit>";
      }
      else
      {
        if (empty($operation))
        {
          $res = $db->Execute("select * FROM $dbtables[planets] WHERE planet_id=$planet");
          $row = $res->fields;

          echo "<table border=0 cellspacing=2 cellpadding=2>";
          echo "<tr><td><tt>          Planet ID  </tt></td><td><font color=#6f0>$planet</font></td>";
          echo "<td align=Right><tt>  Sector ID  </tt><input type=text size=5 name=sector_id value=\"$row[sector_id]\"></td>";
          echo "<td align=Right><tt>  Defeated   </tt><input type=CHECKBOX name=defeated value=ON " . CHECKED($row['defeated']) . "></td></tr>";
          echo "<tr><td><tt>          Planet Name</tt></td><td><input type=text size=15 name=name value=\"$row['name']\"></td>";
          echo "<td align=Right><tt>  Base       </tt><input type=CHECKBOX name=base value=ON " . CHECKED($row['base']) . "></td>";
          echo "<td align=Right><tt>  Sells      </tt><input type=CHECKBOX name=sells value=ON " . CHECKED($row['sells']) . "></td></tr>";
          echo "<tr><td colspan=4>    <HR>       </td></tr>";
          echo "</table>";

          echo "<table border=0 cellspacing=2 cellpadding=2>";
          echo "<tr><td><tt>          Planet Owner</tt></td><td>";
                                      echo "<select size=1 name=owner>";
                                      $ressuba = $db->Execute("select ship_id,character_name FROM $dbtables[ships] ORDER BY character_name");
                                      echo "<option value=0>No One</option>";
                                      while (!$ressuba->EOF)
                                      {
                                      $rowsuba=$ressuba->fields;
                                      if ($rowsuba[ship_id] == $row[owner])
                                        {
                                        echo "<option selectED=$rowsuba[ship_id] value=$rowsuba[ship_id]>$rowsuba[character_name]</option>";
                                        } else {
                                        echo "<option value=$rowsuba[ship_id]>$rowsuba[character_name]</option>";
                                        }
                                        $ressuba->MoveNext();
                                      }
                                      echo "</select></td>";
          echo "<td align=Right><tt>  Organics   </tt></td><td><input type=text size=9 name=organics value=\"$row[organics]\"></td>";
          echo "<td align=Right><tt>  Ore        </tt></td><td><input type=text size=9 name=ore value=\"$row[ore]\"></td>";
          echo "<td align=Right><tt>  Goods      </tt></td><td><input type=text size=9 name=goods value=\"$row[goods]\"></td>";
          echo "<td align=Right><tt>  Energy     </tt></td><td><input type=text size=9 name=energy value=\"$row[energy]\"></td></tr>";
          echo "<tr><td><tt>          Planet Corp</tt></td><td><input type=text size=5 name=corp value=\"$row[corp]\"></td>";
          echo "<td align=Right><tt>  Colonists  </tt></td><td><input type=text size=9 name=colonists value=\"$row[colonists]\"></td>";
          echo "<td align=Right><tt>  Credits    </tt></td><td><input type=text size=9 name=credits value=\"$row[credits]\"></td>";
          echo "<td align=Right><tt>  Fighters   </tt></td><td><input type=text size=9 name=fighters value=\"$row[fighters]\"></td>";
          echo "<td align=Right><tt>  Torpedoes  </tt></td><td><input type=text size=9 name=torps value=\"$row[torps]\"></td></tr>";
          echo "<tr><td colspan=2><tt>Planet Production</tt></td>";
          echo "<td align=Right><tt>  Organics   </tt></td><td><input type=text size=9 name=prod_organics value=\"$row[prod_organics]\"></td>";
          echo "<td align=Right><tt>  Ore        </tt></td><td><input type=text size=9 name=prod_ore value=\"$row[prod_ore]\"></td>";
          echo "<td align=Right><tt>  Goods      </tt></td><td><input type=text size=9 name=prod_goods value=\"$row[prod_goods]\"></td>";
          echo "<td align=Right><tt>  Energy     </tt></td><td><input type=text size=9 name=prod_energy value=\"$row[prod_energy]\"></td></tr>";
          echo "<tr><td colspan=6><tt>Planet Production</tt></td>";
          echo "<td align=Right><tt>  Fighters   </tt></td><td><input type=text size=9 name=prod_fighters value=\"$row[prod_fighters]\"></td>";
          echo "<td align=Right><tt>  Torpedoes  </tt></td><td><input type=text size=9 name=prod_torp value=\"$row[prod_torp]\"></td></tr>";
          echo "<tr><td colspan=10>   <HR>       </td></tr>";
          echo "</table>";

          echo "<br>";
          echo "<input type=hidden name=planet value=$planet>";
          echo "<input type=hidden name=operation value=save>";
          echo "<input type=submit size=1 value=Save>";
        }
        elseif ($operation == "save")
        {
          // update database
          $_defeated = empty($defeated) ? "N" : "Y";
          $_base = empty($base) ? "N" : "Y";
          $_sells = empty($sells) ? "N" : "Y";
          $planupdate = $db->Execute("UPDATE $dbtables[planets] SET sector_id='$sector_id',defeated='$_defeated',name='$name',base='$_base',sells='$_sells',owner='$owner',organics='$organics',ore='$ore',goods='$goods',energy='$energy',corp='$corp',colonists='$colonists',credits='$credits',fighters='$fighters',torps='$torps',prod_organics='$prod_organics',prod_ore='$prod_ore',prod_goods='$prod_goods',prod_energy='$prod_energy',prod_fighters='$prod_fighters',prod_torp='$prod_torp' WHERE planet_id=$planet");
          if (!$planupdate) {
            echo "Changes to Planet record have FAILED Due to the following Error:<br><br>";
            echo $db->ErrorMsg() . "<br>";
          } else {
            echo "Changes to Planet record have been saved.<br><br>";
          }
          echo "<input type=submit value=\"Return to Planet editor\">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<input type=hidden name=menu value=planedit>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "</form>";
    }
    elseif ($module == "linkedit")
    {
      echo "<B>Link editor</B>";
    }
    elseif ($module == "zoneedit")
    {
      echo "<B>Zone editor</B>";
      echo "<br>";
      echo "<form action=admin.php method=post>";
      if (empty($zone))
      {
        echo "<select size=20 name=zone>";
        $res = $db->Execute("select zone_id,zone_name FROM $dbtables[zones] ORDER BY zone_name");
        while (!$res->EOF)
        {
          $row=$res->fields;
          echo "<option value=$row[zone_id]>$row[zone_name]</option>";
          $res->MoveNext();
        }
        echo "</select>";
        echo "<input type=hidden name=operation value=editzone>";
        echo "&nbsp;<input type=submit value=Edit>";

      }
      else
      {
        if ($operation == "editzone")
        {
          $res = $db->Execute("select * FROM $dbtables[zones] WHERE zone_id=$zone");
          $row = $res->fields;
          echo "<table border=0 cellspacing=0 cellpadding=5>";
          echo "<tr><td>Zone ID</td><td>$row[zone_id]</td></tr>";
          echo "<tr><td>Zone Name</td><td><input type=text name=zone_name value=\"$row[zone_name]\"></td></tr>";
          echo "<tr><td>Allow Beacon</td><td><input type=CHECKBOX name=zone_beacon value=ON " . CHECKED($row[allow_beacon]) . "></td>";
          echo "<tr><td>Allow Attack</td><td><input type=CHECKBOX name=zone_attack value=ON " . CHECKED($row[allow_attack]) . "></td>";
          echo "<tr><td>Allow WarpEdit</td><td><input type=CHECKBOX name=zone_warpedit value=ON " . CHECKED($row[allow_warpedit]) . "></td>";
          echo "<tr><td>Allow Planet</td><td><input type=CHECKBOX name=zone_planet value=ON " . CHECKED($row[allow_planet]) . "></td>";
          echo "</table>";
          echo "<tr><td>Max Hull</td><td><input type=text name=zone_hull value=\"$row[max_hull]\"></td></tr>";
          echo "<br>";
          echo "<input type=hidden name=zone value=$zone>";
          echo "<input type=hidden name=operation value=savezone>";
          echo "<input type=submit value=Save>";
        }
        elseif ($operation == "savezone")
        {
          // update database
          $_zone_beacon = empty($zone_beacon) ? "N" : "Y";
          $_zone_attack = empty($zone_attack) ? "N" : "Y";
          $_zone_warpedit = empty($zone_warpedit) ? "N" : "Y";
          $_zone_planet = empty($zone_planet) ? "N" : "Y";
          $db->Execute("UPDATE $dbtables[zones] SET zone_name='$zone_name',allow_beacon='$_zone_beacon' ,allow_attack='$_zone_attack' ,allow_warpedit='$_zone_warpedit' ,allow_planet='$_zone_planet', max_hull='$zone_hull' WHERE zone_id=$zone");
          echo "Changes saved<br><br>";
          echo "<input type=submit value=\"Return to Zone Editor \">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<input type=hidden name=menu value=zoneedit>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "</form>";
    }
    elseif ($module == "ipedit")
    {
      echo "<B>IP Bans editor</B><p>";
      if (empty($command))
      {
        echo "<form action=admin.php method=post>";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        echo "<input type=hidden name=command value=showips>";
        echo "<input type=hidden name=menu value=ipedit>";
        echo "<input type=submit value=\"Show player's ips\">";
        echo "</form>";

        $res = $db->Execute("select ban_mask FROM $dbtables[ip_bans]");
        while (!$res->EOF)
        {
          $bans[]=$res->fields[ban_mask];
          $res->MoveNext();
        }

        if (empty($bans))
          echo "<b>No IP bans are currently active.</b>";
        else
        {
          echo "<table border=1 cellspacing=1 cellpadding=2 width=100% align=center>" .
               "<tr bgcolor=$color_line2><td align=center colspan=7><b><font color=white>" .
               "Active IP Bans" .
               "</font></b>" .
               "</td></tr>" .
               "<tr align=center bgcolor=$color_line2>" .
               "<td><font size=2 color=white><b>Ban Mask</b></font></td>" .
               "<td><font size=2 color=white><b>Affected Players</b></font></td>" .
               "<td><font size=2 color=white><b>E-mail</b></font></td>" .
               "<td><font size=2 color=white><b>Operations</b></font></td>" .
               "</tr>";

          $curcolor=$color_line1;

          foreach ($bans as $ban)
          {
            echo "<tr bgcolor=$curcolor>";
            if ($curcolor == $color_line1)
              $curcolor = $color_line2;
            else
              $curcolor = $color_line1;

            $printban = str_replace("%", "*", $ban);
            echo "<td align=center><font size=2 color=white>$printban</td>" .
                 "<td align=center><font size=2 color=white>";

            $res = $db->Execute("select character_name, ship_id, email FROM $dbtables[ships] WHERE ip_address LIKE '$ban'");
            unset($players);
            while (!$res->EOF)
            {
              $players[] = $res->fields;
              $res->MoveNext();
            }

            if (empty($players))
            {
              echo "None";
            }
            else
            {
              foreach ($players as $player)
              {
                echo "<b>$player[character_name]</b><br>";
              }
            }

            echo "<td align=center><font size=2 color=white>";

            if (empty($players))
            {
              echo "N/A";
            }
            else
            {
              foreach ($players as $player)
              {
                echo "$player[email]<br>";
              }
            }

            echo "<td align=center nowrap valign=center><font size=2 color=white>" .
                 "<form action=admin.php method=post>" .
                 "<input type=hidden name=swordfish value=$swordfish>" .
                 "<input type=hidden name=command value=unbanip>" .
                 "<input type=hidden name=menu value=ipedit>" .
                 "<input type=hidden name=ban value=$ban>" .
                 "<input type=submit value=Remove>" .
                 "</form>";

          }

          echo "</table><p>";
        }
      }
      elseif ($command== 'showips')
      {
        $res = $db->Execute("select DISTINCT ip_address FROM $dbtables[ships]");
        while (!$res->EOF)
        {
          $ips[]=$res->fields[ip_address];
          $res->MoveNext();
        }
        echo "<table border=1 cellspacing=1 cellpadding=2 width=100% align=center>" .
             "<tr bgcolor=$color_line2><td align=center colspan=7><b><font color=white>" .
             "Players sorted by IP address" .
             "</font></b>" .
             "</td></tr>" .
             "<tr align=center bgcolor=$color_line2>" .
             "<td><font size=2 color=white><b>IP address</b></font></td>" .
             "<td><font size=2 color=white><b>Players</b></font></td>" .
             "<td><font size=2 color=white><b>E-mail</b></font></td>" .
             "<td><font size=2 color=white><b>Operations</b></font></td>" .
             "</tr>";

        $curcolor=$color_line1;

        foreach ($ips as $ip)
        {
          echo "<tr bgcolor=$curcolor>";
          if ($curcolor == $color_line1)
            $curcolor = $color_line2;
          else
            $curcolor = $color_line1;

          echo "<td align=center><font size=2 color=white>$ip</td>" .
               "<td align=center><font size=2 color=white>";

          $res = $db->Execute("select character_name, ship_id, email FROM $dbtables[ships] WHERE ip_address='$ip'");
          unset($players);
          while (!$res->EOF)
          {
            $players[] = $res->fields;
            $res->MoveNext();
          }

          foreach ($players as $player)
          {
            echo "<b>$player[character_name]</b><br>";
          }

          echo "<td align=center><font size=2 color=white>";

          foreach ($players as $player)
          {
            echo "$player[email]<br>";
          }

          echo "<td align=center nowrap valign=center><font size=2 color=white>" .
               "<form action=admin.php method=post>" .
               "<input type=hidden name=swordfish value=$swordfish>" .
               "<input type=hidden name=command value=banip>" .
               "<input type=hidden name=menu value=ipedit>" .
               "<input type=hidden name=ip value=$ip>" .
               "<input type=submit value=Ban>" .
               "</form>" .
               "<form action=admin.php method=post>" .
               "<input type=hidden name=swordfish value=$swordfish>" .
               "<input type=hidden name=command value=unbanip>" .
               "<input type=hidden name=menu value=ipedit>" .
               "<input type=hidden name=ip value=$ip>" .
               "<input type=submit value=Unban>" .
               "</form>";

        }

        echo "</table><p>" .
             "<form action=admin.php method=post>" .
             "<input type=hidden name=swordfish value=$swordfish>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=submit value=\"Return to IP bans menu\">" .
             "</form>";
      }
      elseif ($command == 'banip')
      {
        $ip = $_POST[ip];
        echo "<b>Banning ip : $ip<p>";
        echo "<font size=2 color=white>Please select ban type :<p>";

        $ipparts = explode(".", $ip);

        echo "<table border=0>" .
             "<tr><td align=right>" .
             "<form action=admin.php method=post>" .
             "<input type=hidden name=swordfish value=$swordfish>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=hidden name=command value=banip2>" .
             "<input type=hidden name=ip value=$ip>" .
             "<input type=radio name=class value=I checked>" .
             "<td><font size=2 color=white>IP only : $ip</td>" .
             "<tr><td>" .
             "<input type=radio name=class value=A>" .
             "<td><font size=2 color=white>Class A : $ipparts[0].$ipparts[1].$ipparts[2].*</td>" .
             "<tr><td>" .
             "<input type=radio name=class value=B>" .
             "<td><font size=2 color=white>Class B : $ipparts[0].$ipparts[1].*</td>" .
             "<tr><td><td><br><input type=submit value=Ban>" .
             "</table>" .
             "</form>";

        echo "<form action=admin.php method=post>" .
             "<input type=hidden name=swordfish value=$swordfish>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=submit value=\"Return to IP bans menu\">" .
             "</form>";
      }
      elseif ($command == 'banip2')
      {
        $ip = $_POST[ip];
        $ipparts = explode(".", $ip);

        if ($class == 'A')
          $banmask = "$ipparts[0].$ipparts[1].$ipparts[2].%";
        elseif ($class == 'B')
          $banmask = "$ipparts[0].$ipparts[1].%";
        else
          $banmask = $ip;

        $printban = str_replace("%", "*", $banmask);
        echo "<font size=2 color=white><b>Successfully banned $printban</b>.<p>";

        $db->Execute("INSERT INTO $dbtables[ip_bans] valueS(NULL, '$banmask')");
        $res = $db->Execute("select DISTINCT character_name FROM $dbtables[ships], $dbtables[ip_bans] WHERE ip_address LIKE ban_mask");
        echo "Affected players :<p>";
        while (!$res->EOF)
        {
          echo " - " . $res->fields[character_name] . "<br>";
          $res->MoveNext();
        }

        echo "<form action=admin.php method=post>" .
             "<input type=hidden name=swordfish value=$swordfish>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=submit value=\"Return to IP bans menu\">" .
             "</form>";
      }
      elseif ($command == 'unbanip')
      {
        $ip = $_POST[ip];

        if (!empty($ban))
          $res = $db->Execute("select * FROM $dbtables[ip_bans] WHERE ban_mask='$ban'");
        else
          $res = $db->Execute("select * FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask");

        $nbbans = $res->RecordCount();
        while (!$res->EOF)
        {
          $res->fields[print_mask] = str_replace("%", "*", $res->fields[ban_mask]);
          $bans[]=$res->fields;
          $res->MoveNext();
        }

        if (!empty($ban))
          $db->Execute("DELETE FROM $dbtables[ip_bans] WHERE ban_mask='$ban'");
        else
          $db->Execute("DELETE FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask");

        $query_string = "ip_address LIKE '" . $bans[0][ban_mask] ."'";
        for ($i = 1; $i < $nbbans ; $i++)
          $query_string = $query_string . " OR ip_address LIKE '" . $bans[$i][ban_mask] . "'";

        $res = $db->Execute("select DISTINCT character_name FROM $dbtables[ships] WHERE $query_string");
        $nbplayers = $res->RecordCount();
        while (!$res->EOF)
        {
          $players[]=$res->fields[character_name];
          $res->MoveNext();
        }

        echo "<font size=2 color=white><b>Successfully removed $nbbans bans</b> :<p>";

        foreach ($bans as $ban)
        {
          echo " - $ban[print_mask]<br>";
        }

        echo "<p><b>Affected players :</b><p>";
        if (empty($players))
          echo " - None<br>";
        else
        {
          foreach ($players as $player)
          {
            echo " - $player<br>";
          }
        }

        echo "<form action=admin.php method=post>" .
             "<input type=hidden name=swordfish value=$swordfish>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=submit value=\"Return to IP bans menu\">" .
             "</form>";
      }

    }
    elseif ($module == "logview")
    {
      echo "<form action=log.php method=post>" .
           "<input type=hidden name=swordfish value=$swordfish>" .
           "<input type=hidden name=player value=0>" .
           "<input type=submit value=\"View admin log\">" .
           "</form>" .
           "<form action=log.php method=post>" .
           "<input type=hidden name=swordfish value=$swordfish>" .
           "<select name=player>";

      $res = $db->execute("select ship_id, character_name FROM $dbtables[ships] ORDER BY character_name ASC");
      while (!$res->EOF)
      {
        $players[] = $res->fields;
        $res->MoveNext();
      }

      foreach ($players as $player)
        echo "<option value=$player[ship_id]>$player[character_name]</option>";

      echo "</select>&nbsp;&nbsp;" .
           "<input type=submit value=\"View player log\">" .
           "</form><HR size=1 width=80%>";
    }
    else
    {
      echo "Unknown function";
    }

    if ($button_main)
    {
      echo "<p>";
      echo "<form action=admin.php method=post>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=\"Return to main menu\">";
      echo "</form>";
    }
  }
}

include "footer.php";
?>
