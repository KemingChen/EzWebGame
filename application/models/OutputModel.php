<?php

class OutputModel extends CI_Model
{
    private $output = array();

    /**
     * OutputModel::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * OutputModel::delete()
     * 
     * �R���x�s���� key
     * 
     * @param mixed $key
     * @return void
     */
    public function delete($key)
    {
        if (isset($this->output[$key]))
        {
            unset($this->output[$key]);
        }
    }

    /**
     * OutputModel::save()
     * 
     * �x�sVALUE�PKEY
     * 
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function save($key, $value)
    {
        if ($value === true || $value === false)
            $this->output[$key] = $value ? "true" : "false";
        else
            $this->output[$key] = $value;
    }

    /**
     * OutputModel::debug()
     * 
     * ��DEBUG�ΡA�HDEBUG KEY�@���h�֥[�n��X�����
     * 
     * @param mixed $value
     * @return void
     */
    public function debug($value)
    {
        $key = "DebugInfo";
        if (isset($this->output[$key]))
            $this->output[$key] .= " ; " . $value;
        else
            $this->output[$key] = $value;
    }

    /**
     * OutputModel::wrong()
     * 
     * ���j���~�A�B�M�`�{������A�G�������{��
     * 
     * @param mixed $value
     * @return void
     */
    public function wrong($value)
    {
        $this->save("Wrong", $value);
        $this->show();
        exit;
    }

    /**
     * OutputModel::show()
     * 
     * ��Ȧs��ƥHJSON�榡��X
     * 
     * @return void
     */
    public function show()
    {
        echo json_encode($this->output);
    }

    /**
     * OutputModel::flush()
     * 
     * ��Ȧs��ƥHJSON�榡�A�z�LSSE��X
     * 
     * @return void
     */
    public function flush()
    {
        echo "data: ";
        $this->out->show();
        echo "\n\n";
        $this->output = array();
        flush();
    }

    /**
     * OutputModel::convertToEvent()
     * 
     * Event Format is "array("Type" => $key, "Param" => $value)"
     * 
     * @param mixed $key
     * @param mixed $value
     * @return
     */
    public function convertToEvent($key, $value)
    {
        return array("Type" => $key, "Param" => $value);
    }
}

?>