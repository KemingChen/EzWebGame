<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PlayerStatus
{
    // �гy ���a�C�������A��(PS)
    // turn( P => Playing, E => Ending, N => Now )
    private function createPS($roomPlayers)
    {
        $ps = array();
        for ($i = 0; $i < count($roomPlayers); $i++)
        {
            $ps[$i] = array($i == 0 ? "N" : "P", $roomPlayers[$i]["userId"]);
        }
        return $ps;
    }
    
    // ���U�@�쪱�a �öǦ^ �U�@�쪱�a�bPS������m
    private function nextPS($ps)
    {
        for ($i = 0; $i < count($ps); $i++)
        {
            if ($ps[$i] == "N")
            {
                $ps[$i] = "P";
                $next = ($i + 1) % count($ps);
                $ps[$next] = "N";
                return $next;
            }
        }
    }

    // ��PS�ন�r��
    private function PStoString($ps)
    {
        $sPS = "";
        for ($i = 0; $i < count($ps); $i++)
        {
            if ($i != 0)
                $sPS .= "-";
            $sPS .= $ps[$i][0] . "&" . $ps[$i][1];
        }
        return $sPS;
    }

    // ��r���নPS
    private function parsePS($sPS)
    {
        $ps = explode("-", $sPS);
        foreach ($ps as & $value)
        {
            $value = explode("&", $value);
        }
        return $ps;
    }
}

/* End of file Someclass.php */
