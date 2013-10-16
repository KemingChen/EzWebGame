<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PlayerStatus
{
    public function test()
    {
        echo "test!!!!";
    }
    
    // �гy ���a�C�������A��(PS)
    // turn( P => Playing, E => Ending, N => Now )
    public function create($roomPlayers)
    {
        $ps = array();
        for ($i = 0; $i < count($roomPlayers); $i++)
        {
            $ps[$i] = array($i == 0 ? "N" : "P", $roomPlayer["userId"]);
        }
        return $ps;
    }

    // ���U�@�쪱�a �öǦ^ �U�@�쪱�a�bPS������m
    public function next($ps)
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
    public function toString($ps)
    {
        $sPS = "";
        for ($i = 0; $i < count($ps); $i++)
        {
            if ($i != 0)
                $sPS .= "-";
            $sPS .= $ps[$i][0] . "&" . $ps[$i][0];
        }
        return $sPS;
    }

    // ��r���নPS
    public function parse($sPS)
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