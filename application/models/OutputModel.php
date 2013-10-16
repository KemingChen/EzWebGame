<?php

class OutputModel extends CI_Model
{
    private $output = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function save($key, $value)
    {
        if ($value === true || $value === false)
            $this->output[$key] = $value ? "true" : "false";
        else
            $this->output[$key] = $value;
    }

    public function debug($value)
    {
        $key = "DebugInfo";
        if (isset($this->output[$key]))
            $this->output[$key] .= " ; " . $value;
        else
            $this->output[$key] = $value;
    }

    // ���j���~ �{���������
    public function wrong($value)
    {
        $this->save("Wrong", $value);
        $this->show();
        exit;
    }

    public function show()
    {
        echo json_encode($this->output);
    }
}

?>