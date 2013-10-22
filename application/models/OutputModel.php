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
     * 刪除儲存中的 key
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
     * 儲存VALUE與KEY
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
     * 給DEBUG用，以DEBUG KEY一直去累加要輸出的資料
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
     * 重大錯誤，且危害程式執行，故停止執行程式
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
     * 把暫存資料以JSON格式輸出
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
     * 把暫存資料以JSON格式，透過SSE輸出
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