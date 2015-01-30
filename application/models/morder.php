<?php
/**
 *
 **/
class MOrder extends CI_Model
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

    public function intAddReturnOrderId($main_data, $address_info)
    {
        $post_fee = $this->intCalcPostFee();
        $current_user_id = 1;//get by session
        $insert_sql_address = "";
        $insert_sql_address .= "
            insert into address_books
            (user_id, contact, province_id, city_id, address_info, remark, mobile)
            values ({$current_user_id},?,?,?,?,?,?);
        ";
        $binds_address = array(
            $address_info['contact'],
            $address_info['province_id'],
            $address_info['city_id'],
            $address_info['address_info'],
            $address_info['remark'],
            $address_info['mobile'],
        );
        $insert_sql_order = "";
        $insert_sql_order .= "

            insert into order (user_id, product_id, count, level, parent_level, address_book_id, is_post, post_fee )
            values (
            select {$current_user_id}, ?, ?, u.level, u.pid, currval('address_books_id_seq'), ?, ?
                from
                (select u.level, u.pid from users where user_id = {$current_user_id}) as u
            )
            );
        ";
        $binds_order = array(
            $main_data['product_id'], $main_data['count'], $main_data['is_post'], $post_fee,
        );

        $insert_sql_amount = "";
        $insert_sql_amount .= "
            insert into amounts (amount, order_id, level)
            value
            (
                (select pr.price from product p, price pr where pr.product_id = p.id and level = 1),
                currval(orders_id_seq),
                1
            ),
            (
                (select pr.price from product p, price pr where pr.product_id = p.id and level = 2),
                currval(orders_id_seq),
                2
            ),
            (
                (select pr.price from product p, price pr where pr.product_id = p.id and level = 3),
                currval(orders_id_seq),
                3
            );
        ";

        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_address, $binds_address);
        $this->objDB->query($insert_sql_order, $binds_order);
        $this->objDB->query($insert_sql_amount);
        $inserted_order_id_result = $this->objDB->query(
            "select currval(orders_id_seq) id;"
        );

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true){
            if($inserted_order_id_result->num_rows() > 0) {
                $inserted_order_id = $inserted_order_id_result->row()->id;
            }

            $inserted_order_id_result->free_result();
            return $inserted_order_id;
        }else{
            return 0;
        }
    }

    public function intCalcPostFee(){
        return 0;
    }

    /*public function intGetProductsCount($where)
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
    }*/

}