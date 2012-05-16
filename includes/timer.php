<?php
//
// File: includes/timer.php

class c_Timer
{
    var $t_start = 0;
    var $t_stop = 0;
    var $t_elapsed = 0;

    function start()
    {
        $this->t_start = microtime();
    }

    function stop()
    {
        $this->t_stop  = microtime();
    }

    function elapsed()
    {
        $start_u = substr($this->t_start,0,10); $start_s = substr($this->t_start,11,10);
        $stop_u  = substr($this->t_stop,0,10);  $stop_s  = substr($this->t_stop,11,10);
        $start_total = floatval($start_u) + $start_s;
        $stop_total  = floatval($stop_u) + $stop_s;
        $this->t_elapsed = $stop_total - $start_total;
        return $this->t_elapsed;
    }
}
?>
