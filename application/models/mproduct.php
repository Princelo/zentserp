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
                p.title title,
                p.properties properties,
                p.feature feature,
                p.usage_method usage_mothod,
                p.img img,
                pr1.price price_special,
                pr2.price price_last_2,
                pr3.price price_last_3
            from
                products p
                join price pr1
                on p.id = pr1.product_id
                and pr1.level = 1
                join price pr2
                on p.id = pr2.product_id
                and pr2.level = 2
                join price pr3
                on p.id = pr3.product_id
                and pr3.level = 3
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
            (title, properties, feature, usage_method, ingredient, img, is_valid)
            values (?,?,?,?,?,?,?);
        ";
        $insert_sql_price = "";
        $insert_sql_price .= "
            insert into price (level, product_id, price)
            values {$price_list_str};
        ";
        $binds_product = array(
            $main_data['title'], $main_data['properties'], $main_data['feature'], $main_data['usage_method'],
            $main_data['ingredient'], $main_data['img'], $main_data['is_valid']
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

    public function intGetProductsCount($where)
    {
        $query_sql = "";
        $query_sql .= "
            select count(1) from products p
            join price pr1 on pr1.level = 1 and pr1.product_id = p.id
            join price pr2 on pr2.level = 2 and pr2.product_id = p.id
            join price pr3 on pr1.level = 3 and pr3.product_id = p.id
            where 1 = 1 {$where}
        ;";
        $query = $this->objDB->query($query_sql);


        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        }

        $query->free_result();

        return $count;
    }

}