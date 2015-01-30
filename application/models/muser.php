<?php
/**
 *
 **/
class MUser extends CI_Model
{
    private $objDB;

    function __construct()
    {
        parent::__construct();
        $this->objDB = $this->load->database("default", true);
    }

    public function boolVerify( $login_id, $password)
    {
        $query_sql = "";
        $query_sql .= "
            select
                count(1) as count
            from
                users
                where
                username = ?
                and password = ?
        ";
        $binds = array($login_id, $password);
        $data = array();
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function boolUpdatePassword($password, $login_id){
        $update_data = array("password" => $password);
        $update_sql =  $this->objDB->update_string('users', $update_data, array("username"=>$login_id));
        $result = $this->objDB->query($update_sql);
        if($result === true)
            return true;
        else
            return false;
    }
}