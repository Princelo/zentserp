<?php
/**
 *
 **/
class MPost extends CI_Model
{
    private $objDB;

    function __construct()
    {
        parent::__construct();
        $this->objDB = $this->load->database("default", true);
    }

    public function objGetRules()
    {
        $this->objDB->select('*');
        $this->objDB->from('post_rules');
        $this->objDB->order_by('province_id');
        $this->objDB->order_by('city_id');
        $query = $this->objDB->get();
        return $query->result();
    }

    public function getRuleInfo($id)
    {
        $this->objDB->select("*");
        $this->objDB->from('post_rules');
        $this->objDB->where(array('id' => $id));
        $query = $this->objDB->get();
        return $query->result()[0];
    }

    public function checkIsDuplicate($data)
    {
        $query_sql = "";
        $query_sql .= "
            select
                count(1) count
            from
                post_rules
            where
                province_id = ?
                and
                city_id = ?
        ";
        $binds = array($data['province_id'], $data['city_id']);
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0){
            if($query->result()[0]->count > 0 )
                return true;
            else
                return false;
        }else{
            return false;
        }
    }

    public function checkWhetherConflict($data, $id)
    {
        $query_sql = "";
        $query_sql .= "
            select
                count(1) count
            from
                post_rules
            where
                province_id = ?
                and
                city_id = ?
                and id <> ?
        ";
        $binds = array($data['province_id'], $data['city_id'], $id);
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0){
            if($query->result()[0]->count > 0 )
                return true;
            else
                return false;
        }else{
            return false;
        }
    }

    public function add($data)
    {
        $insert_sql = $this->objDB->insert_string('post_rules', $data);
        $query = $this->objDB->query($insert_sql);
        if($query === true)
            return true;
        else
            return false;
    }

    public function update($data, $id)
    {
        $update_sql = $this->objDB->update_string('post_rules', $data, array('id' => $id));
        $query = $this->objDB->query($update_sql);
        if($query === true)
            return true;
        else
            return false;
    }

}