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

    public function objGetOrderList($where = '', $order = '', $limit = '')
    {
        $query_sql = "";
        $query_sql .= "
 select
           sum(iq.amount) amount,sum(quantity) quantity,count(opid) diff_quantity,id,username,parent_user_id,is_root,post_fee,
                          is_pay, is_correct, pay_time, pay_amt, is_cancelled, is_post, province_id, city_id,
                          address_info,linkman,mobile,remark,finish_time,stock_time,is_pay_online,pay_method,
                          pay_amt_without_post_fee,post_info,purchase_level,uid,username name_ch
           from (select
                   --p.title          title,
                   --p.id             pid,
                   op.id	opid,
                   sum(op.quantity) quantity,
                   count(op.id)  diff_quantity,
                   --string_agg(op.product_id::character(255), ',')     products,
                   o.id             id,
                   u.name           username,
                   u.id             uid,
                   u.pid            parent_user_id,
                   u.is_root        is_root,
                   o.post_fee       post_fee,
                   sum(a.amount*a.quantity)         amount,
                   --sum(ta.amount)   trial_amount,
                   o.level          purchase_level,
                   --o.parent_level   purchase_parent_level,
                   o.is_pay         is_pay,
                   o.is_correct     is_correct,
                   o.pay_time       pay_time,
                   o.pay_amt        pay_amt,
                   o.is_cancelled   is_cancelled,
                   o.is_post        is_post,
                   b.province_id    province_id,
                   b.city_id        city_id,
                   b.address_info   address_info,
                   b.contact        linkman,
                   b.mobile         mobile,
                   b.remark         remark,
                   o.finish_time    finish_time,
                   o.create_time    stock_time,
                   o.is_pay_online  is_pay_online,
                   o.pay_method     pay_method,
                   o.pay_amt_without_post_fee   pay_amt_without_post_fee,
                   o.is_first       is_first,
                   o.post_info      post_info
            from
                orders o
                join order_product op
                on op.order_id = o.id
                and o.is_deleted = false
                join users u
                on o.user_id = u.id
                join address_books b
                on b.id = o.address_book_id
                join product_amount a
                on a.order_id = o.id
                and ((a.is_trial = true and a.level = 0 )
                    or (a.is_trial = false and a.level = o.level))
                    and a.product_id = op.product_id

            where
                1 = 1
                {$where}


            group by o.id, u.name, u.id, a.amount, b.province_id, b.city_id, b.address_info, b.contact, b.mobile, b.remark, op.id
            {$order}
            {$limit}
            ) as iq
            where 1 = 1
            group by id,username,parent_user_id,is_root,post_fee,
                          is_pay, is_correct, pay_time, pay_amt, is_cancelled, is_post, province_id, city_id,
                          address_info,linkman,mobile,remark,finish_time,stock_time,is_pay_online,pay_method,
                          pay_amt_without_post_fee,post_info,purchase_level,uid,iq.username
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

    public function addToCart($user_id, $product_id, $quantity, $is_trial)
    {
        $insert_sql = "
            insert into cart_product(user_id, product_id, quantity, is_trial)
            values(?, ?, ?, ?)
            ;
        ";
        $binds = array(
            $user_id, $product_id, $quantity, $is_trial
        );
        $result = $this->objDB->query($insert_sql, $binds);
        if($result === true)
            return true;
        else
            return false;
    }

    public function getCartInfo($user_id, $level)
    {
        $query_sql = "
            select
                c.product_id pid,
                COALESCE(p.title, tp.title) title,
                c.quantity quantity,
                COALESCE(pr.price, tp.trial_price) as unit_price
            from
                cart_product c
                left join products p
                on p.is_trial = false
                and p.id = c.product_id
                left join price pr
                on pr.level = {$level}
                and pr.product_id = c.product_id
                left join products tp
                on tp.is_trial = true
                and tp.id = c.product_id
            where
                c.user_id = {$user_id}
        ";
        $data = array();
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }
        return $data;
    }

    public function intAddReturnOrderId($main_data, $address_info)
    {
        $post_fee = $main_data['post_fee'];
        $current_user_id = $this->session->userdata('current_user_id');
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

            insert into orders (user_id, level, parent_level, address_book_id, is_post, post_fee, is_first, pay_method)
            select {$current_user_id} user_id,
                    u.level as level,
                    u.plevel parent_level,
                    currval('address_books_id_seq') address_book_id,
                    ? is_post,
                    ? post_fee,
                    (case when u.level = 0 then true else false end),
                    ?
                from
                (select c.level as level, p.level plevel
                    from users c, users p where c.id = {$current_user_id}
                        and c.pid = p.id) as u
            ;
        ";
        $binds_order = array(
            $main_data['is_post'], $post_fee, $main_data['pay_method']
        );

        $insert_sql_order_product = "
            insert into order_product(order_id, product_id, quantity, is_trial) values";
        foreach($main_data['products'] as $k => $v)
        {
            if(!is_numeric($k))
                exit;
            if(!is_numeric($v))
                exit;
            $insert_sql_order_product .= "(currval('orders_id_seq'), ".$k . ", " . $v . ",(select is_trial from products where id = {$k}) ),";
        }
        $insert_sql_order_product = substr($insert_sql_order_product, 0, -1);
        $insert_sql_order_product .= ";";

        $product_id_implode_by_comma = "";

        foreach($main_data['products'] as $k => $v)
        {
            $product_id_implode_by_comma .= $k . ",";
        }
        $product_id_implode_by_comma = substr($product_id_implode_by_comma, 0, -1);

        $temp_amounts_str = "";
        $temp_amounts_str_2 = "";
        $temp_amounts_str_3 = "";
        $temp_amounts_str_4_1 = "";
        $temp_amounts_str_4_2 = "";
        $temp_amounts_str_4_3 = "";
        $temp_amounts_str_4_0 = "";


        foreach($main_data['products'] as $k => $v)
        {
            $temp_amounts_str .= "pr{$k}.price::decimal * {$v} +";
            $temp_amounts_str_2 .= "price pr{$k},";
            $temp_amounts_str_3 .= "pr{$k}.product_id = {$k} and ";
            $temp_amounts_str_4_1 .= "pr{$k}.level = 1 and ";
            $temp_amounts_str_4_2 .= "pr{$k}.level = 2 and ";
            $temp_amounts_str_4_3 .= "pr{$k}.level = 3 and ";
            $temp_amounts_str_4_0 .= "pr{$k}.level = 1 and ";
        }
        $temp_amounts_str = substr($temp_amounts_str, 0, -1);
        $temp_amounts_str_2 = substr($temp_amounts_str_2, 0, -1);
        $temp_amounts_str_3 = substr($temp_amounts_str_3, 0, -4);
        $temp_amounts_str_4_1 = substr($temp_amounts_str_4_1, 0, -4);
        $temp_amounts_str_4_2 = substr($temp_amounts_str_4_2, 0, -4);
        $temp_amounts_str_4_3 = substr($temp_amounts_str_4_3, 0, -4);
        $temp_amounts_str_4_0 = substr($temp_amounts_str_4_0, 0, -4);

        $insert_sql_amount = "";
        $insert_sql_amount .= "
            insert into amounts (amount, order_id, level)
            values
            (
                (select {$temp_amounts_str} as amount from products p, {$temp_amounts_str_2} where {$temp_amounts_str_3} and {$temp_amounts_str_4_1} and p.id in ($product_id_implode_by_comma) group by amount),
                currval('orders_id_seq'),
                1
            ),
            (
                (select {$temp_amounts_str} as amount from products p, {$temp_amounts_str_2} where {$temp_amounts_str_3} and {$temp_amounts_str_4_2} and p.id in ($product_id_implode_by_comma) group by amount),
                currval('orders_id_seq'),
                2
            ),
            (
                (select {$temp_amounts_str} as amount from products p, {$temp_amounts_str_2} where {$temp_amounts_str_3} and {$temp_amounts_str_4_3} and p.id in ($product_id_implode_by_comma) group by amount),
                currval('orders_id_seq'),
                3
            ),
            (
                (select {$temp_amounts_str} as amount from products p, {$temp_amounts_str_2} where {$temp_amounts_str_3} and {$temp_amounts_str_4_0} and p.id in ($product_id_implode_by_comma) group by amount),
                currval('orders_id_seq'),
                0
            )
            ;
        ";
        $insert_sql_product_amount = "";
        $binds_product_amount = array();
        foreach($main_data['products'] as $product_id => $quantity)
        {
            $insert_sql_product_amount .= "
            insert into product_amount (amount, order_id, product_id, level, is_trial, quantity)
            values
            (
                (select (case when p.is_trial = true then p.trial_price else pr.price end) from products p left join price pr on pr.product_id = p.id and pr.level = 1 where p.id = ?),
                currval('orders_id_seq'),
                ?,
                1,
                (select is_trial from products where id = {$product_id}),
                {$quantity}
            ),
            (
                (select (case when p.is_trial = true then p.trial_price else pr.price end) from products p left join price pr on pr.product_id = p.id and pr.level = 2 where p.id = ?),
                currval('orders_id_seq'),
                ?,
                2,
                (select is_trial from products where id = {$product_id}),
                {$quantity}
            ),
            (
                (select (case when p.is_trial = true then p.trial_price else pr.price end) from products p left join price pr on pr.product_id = p.id and pr.level = 3 where p.id = ?),
                currval('orders_id_seq'),
                ?,
                3,
                (select is_trial from products where id = {$product_id}),
                {$quantity}
            ),
            (
                (select (case when p.is_trial = true then p.trial_price else pr.price end) from products p left join price pr on pr.product_id = p.id and pr.level = 0 where p.id = ?),
                currval('orders_id_seq'),
                ?,
                0,
                (select is_trial from products where id = {$product_id}),
                {$quantity}
            )
            ;
        ";
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
            array_push($binds_product_amount, $product_id);
        }


        $clean_cart_sql = "delete from cart_product where user_id = {$current_user_id} and product_id in ({$product_id_implode_by_comma});";

        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_address, $binds_address);
        $this->objDB->query($insert_sql_order, $binds_order);
        $this->objDB->query($insert_sql_order_product);
        $this->objDB->query($insert_sql_amount);
        $this->objDB->query($insert_sql_product_amount, $binds_product_amount);
        $this->objDB->query($clean_cart_sql);
        $inserted_order_id_result = $this->objDB->query(
            "select currval('orders_id_seq') id;"
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

    public function intGetOrdersCount($where)
    {
        $query_sql = "";
        $query_sql .= "
            select count(1) from orders o--, products p
            where --o.product_id = p.id
            1 = 1
              {$where}
        ;";
        $query = $this->objDB->query($query_sql);


        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        }

        $query->free_result();

        return $count;
    }

    public function objGetOrderInfo($order_id)
    {
        $order_id = $this->objDB->escape($order_id);
        $query_sql = "";
        $query_sql .= "
           select
           sum(iq.amount) amount,sum(quantity) quantity,count(opid) diff_quantity,id,username,id,parent_user_id,is_root,post_fee,
                          is_pay, is_correct, pay_time, pay_amt, is_cancelled, is_post, province_id, city_id,
                          address_info,linkman,mobile,remark,finish_time,stock_time,is_pay_online,pay_method,
                          pay_amt_without_post_fee,post_info,purchase_level,uid, username name_ch, is_first
            from (select
                   op.id            opid,
                   op.quantity      quantity,
                   o.id             id,
                   u.name           username,
                   u.id             uid,
                   o.level          purchase_level,
                   u.pid            parent_user_id,
                   u.is_root        is_root,
                   o.post_fee       post_fee,
                   sum(a.amount*a.quantity)         amount,
                   o.is_pay         is_pay,
                   o.is_correct     is_correct,
                   o.pay_time       pay_time,
                   o.pay_amt        pay_amt,
                   o.is_cancelled   is_cancelled,
                   o.is_post        is_post,
                   b.province_id    province_id,
                   b.city_id        city_id,
                   b.address_info   address_info,
                   b.contact        linkman,
                   b.mobile         mobile,
                   b.remark         remark,
                   o.finish_time    finish_time,
                   o.create_time    stock_time,
                   o.is_pay_online  is_pay_online,
                   o.pay_method     pay_method,
                   o.pay_amt_without_post_fee   pay_amt_without_post_fee,
                   o.is_first       is_first,
                   o.post_info      post_info
            from
                orders o
                join order_product op
                on op.order_id = o.id
                and o.is_deleted = false
                and
                o.id = {$order_id}
                join users u
                on o.user_id = u.id
                join address_books b
                on b.id = o.address_book_id
                join product_amount a
                on a.order_id = o.id
                and ((a.is_trial = true and a.level = 0 )
                    or (a.is_trial = false and a.level = o.level))
                    and a.product_id = op.product_id

            where
                1 = 1


            group by o.id, u.name, u.id, a.amount, b.province_id, b.city_id, b.address_info, b.contact, b.mobile, b.remark,op.id) as iq
            where 1 = 1
            group by id,username,parent_user_id,is_root,post_fee,is_pay,is_correct,is_pay_online,post_info
            ,is_first, pay_method,stock_time,finish_time,remark,mobile,linkman,pay_time,pay_amt,pay_amt_without_post_fee,
            is_cancelled,is_post,province_id,city_id,address_info,purchase_level,uid
        ";
        $data = array();
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }
        $query->free_result();

        if(isset($data[0]))
            return $data[0];
        else
            return null;
    }

    function getOrderProducts($order_id)
    {
        $query_sql = "
            select
                p.id id,
                p.title title,
                op.quantity quantity,
                pa.amount   amount,
                p.is_trial  is_trial
            from
                products p
                join order_product op
                on op.product_id = p.id
                join orders o
                on o.id = op.order_id
                join product_amount pa
                on ((pa.level = o.level and pa.is_trial = false) or (pa.level = 0 and pa.is_trial = true) )
                left join products tp
                on tp.is_trial = true
                and tp.id = op.product_id
            where
                op.order_id = ?
                and
                (pa.product_id = p.id
                or
                pa.product_id = tp.id
                )
                and pa.order_id = o.id
            group by pa.amount, p.id, op.quantity
        ";
        $binds = array($order_id);
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }
        $query->free_result();

        return $data;
    }

    function finish_with_pay($order_id, $pay_amt, $user_id, $parent_user_id, $is_root, $pay_amt_without_post_fee, $post_fee, $pay_amt_not_trial, $is_first)
    {
        $now = now();
        $order_id = $this->objDB->escape($order_id);
        $update_sql_first_purchase = "
            update orders
                set pay_amt = '{$pay_amt}',
                    is_pay = true,
                    is_correct = true,
                    pay_amt_without_post_fee = '{$pay_amt_without_post_fee}',
                    update_time = '{$now}',
                    finish_time = '{$now}'
            where
                id = {$order_id};

            update
                users
                set basic_level = case
                when
                    first_purchase::decimal + {$pay_amt_not_trial} >= 1980
                    and first_purchase::decimal + {$pay_amt_not_trial} < 3980
                    and basic_level = 0
                    and not exists
                        ( select id from orders
                            where
                            (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                    --and assign_level = 3
                    then 3
                when
                    first_purchase::decimal + {$pay_amt_not_trial} >= 3980
                    and first_purchase::decimal + {$pay_amt_not_trial} < 19800
                    and (basic_level = 0 or basic_level = 3)
                    and not exists
                        ( select id from orders
                            where
                            (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                    --and assign_level = 2
                    then 2
                when
                    first_purchase::decimal + {$pay_amt_not_trial} >= 19800
                    and not exists
                        ( select id from orders
                            where
                            (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                    --and assign_level = 1
                    then 1
                    else basic_level
                end
            where id = {$user_id};
            update users set level = basic_level, assign_level = basic_level where id = {$user_id};
            update users set first_purchase = first_purchase::decimal + {$pay_amt_not_trial} where id = {$user_id};
        ";
        $update_sql = "
            update orders
                set pay_amt = '{$pay_amt}',
                    is_pay = true,
                    is_correct = true,
                    pay_amt_without_post_fee = '{$pay_amt_without_post_fee}',
                    update_time = '{$now}',
                    finish_time = '{$now}'
            where
                id = {$order_id};


            update
                users
                    set turnover =
                        turnover::decimal + {$pay_amt_not_trial}
                        -
                        ( select
                            case when ua.amount::decimal > pa.amount::decimal
                                           and ua.level <> 0 and p.is_admin = false
                            then (ua.amount::decimal - pa.amount::decimal)
                            else 0 end
                            + case when ua.level = 0 and u.level <> 0 and u.initiation = false and p.is_admin = false
                                  and not exists
                                      ( select id from orders
                                        where
                                        (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                                         --the last of first purchase
                                   then
                                   case when p.level = 1 and u.level = 1
                                        then 5000
                                        when p.level = 1 and u.level = 2
                                        then 1000
                                        when p.level = 1 and u.level = 3
                                        then 300
                                        when p.level = 2 and u.level = 1
                                        then 3000
                                        when p.level = 2 and u.level = 2
                                        then 500
                                        when p.level = 2 and u.level = 3
                                        then 200
                                        when p.level = 3 and u.level = 1
                                        then 1000
                                        when p.level = 3 and u.level = 2
                                        then 300
                                        when p.level = 3 and u.level = 3
                                        then 100
                                   else
                                   0 end
                               else 0 end
                          from
                              orders o, amounts ua, amounts pa, users u, users p
                              where
                                  ua.order_id = {$order_id}
                                  and ua.level = o.level
                                  and pa.order_id = {$order_id}
                                  and pa.level = o.parent_level
                                  and o.id = {$order_id}
                                  and u.id = {$user_id}
                                  and p.id = u.pid
                              )
                where id = {$user_id};
            --and then trigger
            --CREATE OR REPLACE FUNCTION log_turnover_change_to_bills()
            --  RETURNS trigger AS
            --\$BODY$
            --BEGIN
            --    update bills set volume = NEW.turnover::decimal - OLD.trunover::decimal
            --        where id = NEW.current_bill_id;
            --    RETURN NEW;
            --END;
            --\$BODY$
            --  LANGUAGE plpgsql VOLATILE
            --  COST 100;
            update
                users
                set level = case
                            when turnover::decimal >= 19800 and turnover::decimal < 59600
                                and (basic_level = 0 or basic_level = 3 )
                            then 2
                            when turnover::decimal >= 59600
                                and (basic_level = 0 or basic_level = 3 or basic_level = 2)
                            then 1
                            else level
                            end
                where
                    id = {$user_id};
        ";
        $update_bills = "
            insert into bills (user_id, order_id, volume, type, reason)
            values
            ({$user_id}, {$order_id}, 0, 3, 1);
            update users set current_bill_id = currval('bills_id_seq') where id = {$user_id};
            insert into bills (user_id, order_id, volume, type, reason, pay_amt_without_post_fee)
            values
            ({$user_id}, {$order_id}, {$pay_amt}, 1, 1, {$pay_amt_without_post_fee});
            --insert into zents_bills (user_id, order_id, income_without_post_fee, income_with_post_fee)
            --values
            --({$user_id}, {$order_id}, {$pay_amt_without_post_fee}, {$pay_amt});

        ";
        $update_sql_parent = "
            update
                users
                    set turnover =
                        turnover::decimal + {$pay_amt_not_trial}
                        -
                        ( select
                            case when ua.amount::decimal > pa.amount::decimal
                                           and ua.level <> 0 and p.is_admin = false
                            then (ua.amount::decimal - pa.amount::decimal)
                            else 0 end
                            + case when ua.level = 0 and u.level <> 0 and u.initiation = false and p.is_admin = false
                                  and not exists
                                      ( select id from orders
                                        where
                                        (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                                         --the last of first purchase
                                   then
                                   case when p.level = 1 and u.level = 1
                                        then 5000
                                        when p.level = 1 and u.level = 2
                                        then 1000
                                        when p.level = 1 and u.level = 3
                                        then 300
                                        when p.level = 2 and u.level = 1
                                        then 3000
                                        when p.level = 2 and u.level = 2
                                        then 500
                                        when p.level = 2 and u.level = 3
                                        then 200
                                        when p.level = 3 and u.level = 1
                                        then 1000
                                        when p.level = 3 and u.level = 2
                                        then 300
                                        when p.level = 3 and u.level = 3
                                        then 100
                                   else
                                   0 end
                               else 0 end
                          from
                              orders o, amounts ua, amounts pa, users u, users p
                              where
                                  ua.order_id = {$order_id}
                                  and ua.level = o.level
                                  and pa.order_id = {$order_id}
                                  and pa.level = o.parent_level
                                  and o.id = {$order_id}
                                  and u.id = {$user_id}
                                  and p.id = u.pid
                              )
                where id = (select pid from users where id = {$user_id});
        ";
        $update_sql_parent_profit = "
            insert into bills (user_id, order_id, sub_user_id, volume, type, reason)
            values
            ({$parent_user_id}, {$order_id}, {$user_id}, 0, 2, 2);
            update users set current_bill_id = currval('bills_id_seq') where id = {$parent_user_id};
            update users set profit = profit::decimal + (
                select
                    case when ua.amount::decimal > pa.amount::decimal
                             and ua.level <> 0
                    then (ua.amount::decimal - pa.amount::decimal)
                    else 0 end
                    + case when ua.level = 0 and u.level <> 0 and u.initiation = false
                                  and not exists
                                      ( select id from orders
                                        where
                                        (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                                        --the last of first purchase
                           then
                           case when p.level = 1 and u.level = 1
                                then 5000
                                when p.level = 1 and u.level = 2
                                then 1000
                                when p.level = 1 and u.level = 3
                                then 300
                                when p.level = 2 and u.level = 1
                                then 3000
                                when p.level = 2 and u.level = 2
                                then 500
                                when p.level = 2 and u.level = 3
                                then 200
                                when p.level = 3 and u.level = 1
                                then 1000
                                when p.level = 3 and u.level = 2
                                then 300
                                when p.level = 3 and u.level = 3
                                then 100
                           else
                           0 end
                       else 0 end
                  from
                      orders o, amounts ua, amounts pa, users u, users p
                      where
                          ua.order_id = {$order_id}
                          and ua.level = o.level
                          and pa.order_id = {$order_id}
                          and pa.level = o.parent_level
                          and o.id = {$order_id}
                          and u.id = {$user_id}
                          and p.id = u.pid
            )
            where id = {$parent_user_id};
            --and then
            --CREATE OR REPLACE FUNCTION log_profit_change_to_bills()
            --  RETURNS trigger AS
            --\$BODY$
            --BEGIN
            --	update bills set volume = NEW.profit::decimal - OLD.profit::decimal
            --		where id = NEW.current_bill_id;
            --	RETURN NEW;
            --END;
            --\$BODY$
            --  LANGUAGE plpgsql VOLATILE
            --  COST 100;

            --insert into bills (user_id, order_id, sub_user_id, volume, type, reason)
            --values
            --({$parent_user_id}, {$order_id}, {$user_id},
            --    (
            --    select case when ua.amount::decimal > pa.amount::decimal
            --            then (ua.amount::decimal - pa.amount::decimal)
            --            else 0 end
            --    from orders o, amounts ua, amounts pa
            --    where
            --        ua.order_id = {$order_id}
            --        and ua.level = o.level
            --        and pa.order_id = {$order_id}
            --        and pa.level = o.parent_level
            --        and o.id = {$order_id}
            --    ),
            --    2, 2
            --);
        ";
        $update_sql_parent_level = "
            update
                users
            set level = case
                when
                    turnover::decimal >= 19800
                    and
                    turnover::decimal < 59600
                    and (basic_level = 0 or basic_level = 3)
                    then 2
                when
                    turnover::decimal >= 59600
                    and (basic_level = 0 or basic_level = 3 or basic_level = 2)
                    then 1
                else level
                end
            where id = {$parent_user_id};
        ";
        $update_sql_initiation = "
            update
                users
                set initiation = true
            where initiation = false
                  and level <> 0
                  and id = {$user_id}
                  and not exists
                      ( select id from orders
                        where
                        (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                        -- the last of first purchase
        ";
        $finish_log = "
            insert into
                finish_log(order_id, pay_amt, user_id, parent_user_id, is_root, pay_amt_without_post_fee, is_first)
                values
                ({$order_id}, ?, ?, ?, ?, ?, ?)
            ;";
        $binds_finish_log = array(
            $pay_amt, $user_id, $parent_user_id, $is_root, $pay_amt_without_post_fee, $is_first
        );
        $upgrade_log = '
        CREATE OR REPLACE FUNCTION log_upgrade_level()
          RETURNS trigger AS
            $BODY$
                BEGIN
                    insert into level_update_log
                    (user_id, new_level, original_level, new_profit,
                    original_profit, new_first_purchase, original_first_purchase,
                    original_basic_level, new_basic_level, original_turnover, new_turnover)
                    values
                    (NEW.id, NEW.level, OLD.level, NEW.profit,
                     OLD.profit, NEW.first_purchase, OLD.first_purchase,
                     OLD.basic_level, NEW.basic_level, OLD.turnover, NEW.turnover);
                    RETURN NEW;
                END;
            $BODY$ LANGUAGE plpgsql;
        CREATE TRIGGER upgrade_level
            AFTER UPDATE
            ON users
            FOR EACH ROW
            EXECUTE PROCEDURE log_upgrade_level();
        ';
        $this->objDB->trans_start();

        $this->objDB->query("set constraints all deferred");
        if($is_first == 't')
            $this->objDB->query($update_sql_first_purchase);
        else
            $this->objDB->query($update_sql);
        $this->objDB->query($update_bills);
        if($is_root == 'f')
        {
            $this->objDB->query($update_sql_parent);
            $this->objDB->query($update_sql_parent_profit);
            $this->objDB->query($update_sql_parent_level);
        }
        $this->objDB->query($update_sql_initiation);
        $this->objDB->query($finish_log, $binds_finish_log);

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true){
            return true;
        }else{
            return false;
        }

    }

    public function checkIsOwn($user_id, $order_id)
    {
        $query_sql = "select count(1) from orders where id = ? and user_id = ?;";
        $binds = array($order_id, $user_id);
        $data = array();
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

    public function delete($order_id)
    {
        $this->objDB->from("orders");
        $this->objDB->where("id", $order_id);
        $this->objDB->delete();
        return ($this->objDB->affected_rows() > 0 );
    }

    public function is_paid( $order_id)
    {
        $current_user_id = $this->session->userdata('current_user_id');
        if(!$this->checkIsOwn($current_user_id, $order_id))
            exit('The order is not yours!');
        $query_sql = "";
        $query_sql .= "
            select
                count(1) as count
            from
                orders
                where
                is_pay = true
                and id = ?
        ";
        $binds = array($order_id);
        $data = array();
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

    public function getOrderPrice($id)
    {
        if($this->is_paid($id))
            exit('This order has paid!');
        $query_sql = "
            select sum(pay_amt_without_post_fee) as pay_amt_without_post_fee,
                   post_fee,
                   sum(pay_amt_without_post_fee) + post_fee as total
                   from(
                        select
                            sum(pa.amount::decimal * pa.quantity) pay_amt_without_post_fee,
                            o.post_fee::decimal as post_fee
                        from
                            orders o, product_amount pa
                        where
                            pa.order_id = ?
                        and pa.order_id = o.id
                        and (
                            (pa.level = o.level and pa.is_trial = false)
                            or
                            (pa.level = 0 and pa.is_trial = true)
                            )
                        and o.is_pay = false
                        group by o.post_fee, pa.amount
                        ) as iq
                where 1 = 1
                group by post_fee
            ;";
        $binds = array($id);
        $query = $this->objDB->query($query_sql, $binds);
        $data = $query->result()[0];
        $query->free_result();

        return $data;
    }

    public function updateOrderTradeNo($trade_no, $order_id)
    {
        $data['trade_no'] = $trade_no;
        $where = array(
            'is_pay'    =>  'false',
            'pay_method'    =>  'alipay',
            'id'    =>  $order_id,
        );
        $update_sql = $this->objDB->update_string('orders', $data, $where);
        $query = $this->objDB->query($update_sql);

        if($query === true)
            return true;
        else
            return false;
    }

    public function updateNonMemberCartTradeNo($trade_no)
    {
        $current_user_id = $this->session->userdata('current_user_id');
        $data['trade_no'] = $trade_no;
        $where = array(
            'user_id'   =>  $current_user_id,
            'is_pay'    =>  'false',
            'pay_method'    =>  'alipay',
        );
        $update_sql = $this->objDB->update_string('orders', $data, $where);
        $query = $this->objDB->query($update_sql);

        if($query === true)
            return true;
        else
            return false;

    }

    public function updatePaymentStatus($trade_no)
    {
        $data = array();
        $data['is_pay'] = 'true';
        $where = array(
            'id'  =>  $trade_no,
        );
        $update_sql = $this->objDB->update_string('orders', $data, $where);
        $query = $this->objDB->query($update_sql);

        if($query === true)
            return true;
        else
            return false;
    }
}