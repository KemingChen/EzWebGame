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

    public function error($value)
    {
        $key = "Error";
        if (isset($this->output[$key]))
            $this->output[$key] .= " ; " . $value;
        else
            $this->output[$key] = $value;
    }

    public function show()
    {
        echo json_encode($this->output);
    }
}

?>