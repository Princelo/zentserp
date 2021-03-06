<?php
/**
 *
 **/
class MProduct extends CI_Model
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
                p.trial_price price,
                pr1.price price_special,
                pr2.price price_last_2,
                pr3.price price_last_3,
                pr0.price price_normal,
                p.category category
            from
                products p
                left join price pr1
                on p.id = pr1.product_id
                and pr1.level = 1
                left join price pr2
                on p.id = pr2.product_id
                and pr2.level = 2
                left join price pr3
                on p.id = pr3.product_id
                and pr3.level = 3
                left join price pr0
                on p.id = pr0.product_id
                and pr0.level = 0
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
                pr1.price price_special,
                pr2.price price_last_2,
                pr3.price price_last_3,
                pr0.price price_normal,
                p.trial_price price,
                p.category category,
                p.weight weight
            from
                products p
                left join price pr1
                on p.id = pr1.product_id
                and pr1.level = 1
                left join price pr2
                on p.id = pr2.product_id
                and pr2.level = 2
                left join price pr3
                on p.id = pr3.product_id
                and pr3.level = 3
                left join price pr0
                on p.id = pr0.product_id
                and pr0.level = 0
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


    public function add($main_data, $price_list)
    {
        $price_list_str = "";
        for($i = 0; $i < count($price_list); $i ++)
        {
            $price_list_str .= "(?,currval('products_id_seq'),?),";
        }
        $price_list_str = substr($price_list_str, 0, -1).";";
        $insert_sql_product = "";
        $insert_sql_product .= "
            insert into products
            (title, properties, feature, usage_method, ingredient, img, is_valid, weight, category)
            values (?,?,?,?,?,?,?,?,?);
        ";
        $insert_sql_price = "";
        $insert_sql_price .= "
            insert into price (level, product_id, price)
            values {$price_list_str};
        ";
        $binds_product = array(
            $main_data['title'], $main_data['properties'], $main_data['feature'], $main_data['usage_method'],
            $main_data['ingredient'], $main_data['img'], $main_data['is_valid'], $main_data['weight'], $main_data['category']
        );
        $binds_price = array();

        foreach($price_list as $k => $v)
        {
            array_push($binds_price, $k);
            array_push($binds_price, $v);
        }

        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_product, $binds_product);
        $this->objDB->query($insert_sql_price, $binds_price);

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true){
            return true;
        }else{
            return false;
        }
    }


    public function trial_add($main_data)
    {
        $insert_sql_product = "";
        $insert_sql_product .= "
            insert into products
            (title, properties, feature, usage_method, ingredient, img, is_valid, weight, category, is_trial, trial_price, trial_type)
            values (?,?,?,?,?,?,?,?,?,true,?,?);
        ";
        $binds_product = array(
            $main_data['title'], $main_data['properties'], $main_data['feature'], $main_data['usage_method'],
            $main_data['ingredient'], $main_data['img'], $main_data['is_valid'], $main_data['weight'], $main_data['category'],
            $main_data['price'], $main_data['trial_type'],
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
            select count(1) from products p
            left join price pr1 on pr1.level = 1 and pr1.product_id = p.id
            left join price pr2 on pr2.level = 2 and pr2.product_id = p.id
            left join price pr3 on pr3.level = 3 and pr3.product_id = p.id
            left join price pr0 on pr0.level = 0 and pr0.product_id = p.id
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
            select title from products where id = ?;
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
            update products set is_valid = true where id = ?
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
            update products set is_valid = false where id = ?
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
        $update_sql = $this->objDB->update_string("products", $main_data, array("id" => $id));
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