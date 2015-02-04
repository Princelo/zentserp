<?php
/**
 *
 **/
class MForecast extends CI_Model
{
    private $objDB;

    function __construct()
    {
        parent::__construct();
        $this->objDB = $this->load->database("default", true);
    }

    public function objGetForecastList()
    {
        $query_sql = "";
        $query_sql .= "
            select
                *
            from
                forecasts
        ";
        $data = array();
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }
        $query->free_result();

        return $data;
    }

    public function objGetForecastInfo()
    {
        $query_sql = "";
        $query_sql .= "
            select
                content
            from
                forecasts
            where
                id = 1
        ";
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0){
            $data = $query->result()[0];
        }
        $query->free_result();

        return $data;
    }

    public function update($forecast)
    {
        $update_sql = $this->objDB->update_string('forecasts', array('content'=>$forecast), array('id'=>1));
        $result = $this->objDB->query($update_sql);
        if($result === true)
            return true;
        else
            return false;
    }

}