<?php
/**
 *
 **/
class MTrialProduct extends CI_Model
{
    private $objDB;

    function __construct()
    {
        parent::__construct();
        $this->objDB = $this->load->database("default", true);
    }

    public function objGetProductList($where = '', $order = '', $limit = '')
    {
        $query_sql = "";
        $query_sql .= "
            select
                p.id id,
                p.title title,
                p.properties properties,
                p.feature feature,
                p.usage_method usage_method,
                p.img img,
                p.price price,
                p.category category
            from
                trial_products p
            where
                1 = 1
                {$where}
            {$order}
            {$limit}
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

    public function objGetProductInfo($id = '')
    {
        $query_sql = "";
        $query_sql .= "
            select
                p.id id,
                p.title title,
                p.properties properties,
                p.feature feature,
                p.ingredient ingredient,
                p.usage_method usage_method,
                p.img img,
                p.is_valid is_valid,
                p.price price,
                p.category category,
                p.weight weight
            from
                trial_products p
            where
                p.id = ?
        ";
        $binds = array($id);
        $data = array();
        $query = $this->objDB->query($query_sql, $binds);
        /*if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }*/
        $data = $query->result()[0];
        $query->free_result();

        return $data;
    }


    public function add($main_data)
    {
        $insert_sql_product = "";
        $insert_sql_product .= "
            insert into trial_products
            (title, properties, feature, usage_method, ingredient, img, is_valid, weight, category, price)
            values (?,?,?,?,?,?,?,?,?,?);
        ";
        $binds_product = array(
            $main_data['title'], $main_data['properties'], $main_data['feature'], $main_data['usage_method'],
            $main_data['ingredient'], $main_data['img'], $main_data['is_valid'], $main_data['weight'], $main_data['category']
            , $main_data['price']
        );

        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_product, $binds_product);

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true){
            return true;
        }else{
            return false;
        }
    }

    public function intGetProductsCount($where)
    {
        $query_sql = "";
        $query_sql .= "
            select count(1) from trial_products p
            where 1 = 1 {$where}
        ;";
        $query = $this->objDB->query($query_sql);


        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        }

        $query->free_result();

        return $count;
    }

    public function strGetProductTitle($product_id)
    {
        $query_sql = '';
        $query_sql .= "
            select title from trial_products where id = ?;
        ";
        $binds = array($product_id);
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0) {
            $title = $query->row()->title;
        }

        $query->free_result();

        return $title;
    }

    public function enable($id)
    {
        $update_sql = "
            update trial_products set is_valid = true where id = ?
        ";
        $binds = array($id);

        $result = $this->objDB->query($update_sql, $binds);
        if($result === true)
            return true;
        else
            return false;
    }

    public function disable($id)
    {
        $update_sql = "
            update trial_products set is_valid = false where id = ?
        ";
        $binds = array($id);

        $result = $this->objDB->query($update_sql, $binds);
        if($result === true)
            return true;
        else
            return false;
    }

    public function update($main_data, $id)
    {
        $update_sql = $this->objDB->update_string("trial_products", $main_data, array("id" => $id));
        $this->objDB->trans_start();

        $this->objDB->query($update_sql);

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true) {
            return true;
        }else {
            return false;
        }
    }
}