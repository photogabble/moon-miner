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
// File: includes/timer.php

class c_Timer
{
    public $t_start = 0;
    public $t_stop = 0;
    public $t_elapsed = 0;

    public function start ()
    {
        $this->t_start = microtime ();
    }

    public function stop ()
    {
        $this->t_stop  = microtime ();
    }

    public function elapsed ()
    {
        $start_u = substr($this->t_start, 0, 10); $start_s = substr($this->t_start, 11, 10);
        $stop_u  = substr($this->t_stop, 0, 10);  $stop_s  = substr($this->t_stop, 11, 10);
        $start_total = floatval ($start_u) + $start_s;
        $stop_total  = floatval ($stop_u) + $stop_s;
        $this->t_elapsed = $stop_total - $start_total;

        return $this->t_elapsed;
    }
}
?>
