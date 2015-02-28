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
            select p.title          title,
                   p.id             pid,
                   o.id             id,
                   u.name           username,
                   u.id             uid,
                   o.count          quantity,
                   --o.post_fee       post_fee,
                   a.amount         unit_price,
                   --o.level          purchase_level,
                   --o.parent_level   purchase_parent_level,
                   o.is_pay         is_pay,
                   o.is_correct     is_correct,
                   --o.pay_time       pay_time,
                   --o.pay_amt        pay_amt,
                   o.is_cancelled   is_cancelled,
                   o.is_post        is_post,
                   --b.province_id    province_id,
                   --b.city_id        city_id,
                   --b.address_info   address_info,
                   b.contact        linkman,
                   b.mobile         mobile,
                   b.remark         remark,
                   o.finish_time    finish_time,
                   o.create_time    stock_time
            from
                orders o, products p, users u, address_books b, amounts a
            where
                o.user_id = u.id
                and
                o.product_id = p.id
                and
                o.address_book_id = b.id
                and
                a.order_id = o.id
                and
                a.level = o.level
                and
                o.is_deleted = false
                and o.is_valid = true
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

    public function intAddNonMemberReturnOrderId($main_data, $address_info)
    {
        $post_fee = $this->intCalcPostFee();
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

            insert into orders (user_id, product_id, count, level, parent_level, address_book_id, is_post, post_fee, is_first, cart_id, is_valid)
            select {$current_user_id} user_id,
                    ? product_id,
                    ? count,
                    u.level as level,
                    u.plevel parent_level,
                    currval('address_books_id_seq') address_book_id,
                    ? is_post,
                    ? post_fee,
                    --case when not exists (select id from orders where user_id = {$current_user_id}) then true else false end,
                    true,
                    (select id from cart where user_id = {$current_user_id}),
                    false
                from
                (select c.level as level, p.level plevel
                    from users c, users p where c.id = {$current_user_id}
                        and c.pid = p.id) as u
            ;
        ";
        $binds_order = array(
            $main_data['product_id'], $main_data['count'], $main_data['is_post'], $post_fee,
        );

        $insert_sql_amount = "";
        $insert_sql_amount .= "
            insert into amounts (amount, order_id, level)
            values
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 1 and p.id = ?),
                currval('orders_id_seq'),
                1
            ),
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 2 and p.id = ?),
                currval('orders_id_seq'),
                2
            ),
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 3 and p.id = ?),
                currval('orders_id_seq'),
                3
            ),
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 0 and p.id = ?),
                currval('orders_id_seq'),
                0
            )
            ;
        ";
        $binds_amount = array($main_data['product_id'], $main_data['product_id'], $main_data['product_id'], $main_data['product_id']);


        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_address, $binds_address);
        $this->objDB->query($insert_sql_order, $binds_order);
        $this->objDB->query($insert_sql_amount, $binds_amount);
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
    public function intAddReturnOrderId($main_data, $address_info)
    {
        $post_fee = $this->intCalcPostFee();
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

            insert into orders (user_id, product_id, count, level, parent_level, address_book_id, is_post, post_fee, is_first, cart_id)
            select {$current_user_id} user_id,
                    ? product_id,
                    ? count,
                    u.level as level,
                    u.plevel parent_level,
                    currval('address_books_id_seq') address_book_id,
                    ? is_post,
                    ? post_fee,
                    --case when not exists (select id from orders where user_id = {$current_user_id}) then true else false end,
                    false,
                    (select id from cart where user_id = {$current_user_id})
                from
                (select c.level as level, p.level plevel
                    from users c, users p where c.id = {$current_user_id}
                        and c.pid = p.id) as u
            ;
        ";
        $binds_order = array(
            $main_data['product_id'], $main_data['count'], $main_data['is_post'], $post_fee,
        );

        $insert_sql_amount = "";
        $insert_sql_amount .= "
            insert into amounts (amount, order_id, level)
            values
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 1 and p.id = ?),
                currval('orders_id_seq'),
                1
            ),
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 2 and p.id = ?),
                currval('orders_id_seq'),
                2
            ),
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 3 and p.id = ?),
                currval('orders_id_seq'),
                3
            ),
            (
                (select pr.price from products p, price pr where pr.product_id = p.id and level = 0 and p.id = ?),
                currval('orders_id_seq'),
                0
            )
            ;
        ";
        $binds_amount = array($main_data['product_id'], $main_data['product_id'], $main_data['product_id'], $main_data['product_id']);


        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_address, $binds_address);
        $this->objDB->query($insert_sql_order, $binds_order);
        $this->objDB->query($insert_sql_amount, $binds_amount);
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

    public function enableCart($user_id)
    {
        $update_sql = "
            update orders set is_valid = true where cart_id = (select id from cart where user_id = {$user_id});
        ";
        $result = $this->objDB->query($update_sql);
        if($result === true)
            return true;
        else
            return false;
    }

    public function intCalcPostFee(){
        return 0;
    }

    public function intGetOrdersCount($where)
    {
        $query_sql = "";
        $query_sql .= "
            select count(1) from orders o, products p
            where o.product_id = p.id  {$where}
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
            select p.title          title,
                   p.id             pid,
                   o.id             id,
                   u.name           username,
                   u.id             uid,
                   u.pid            parent_user_id,
                   u.is_root        is_root,
                   o.count          quantity,
                   o.post_fee       post_fee,
                   a.amount         unit_price,
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
                   o.pay_amt_without_post_fee   pay_amt_without_post_fee,
                   o.is_first       is_first
            from
                orders o, products p, users u, address_books b, amounts a
            where
                o.user_id = u.id
                and
                o.product_id = p.id
                and
                o.address_book_id = b.id
                and
                a.order_id = o.id
                and
                a.level = o.level
                and
                o.is_deleted = false
                and
                o.id = {$order_id}
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

    function finish_with_pay($order_id, $pay_amt, $user_id, $parent_user_id, $is_root, $pay_amt_without_post_fee, $is_first)
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
                    first_purchase::decimal + {$pay_amt_without_post_fee} >= 1980
                    and first_purchase::decimal + {$pay_amt_without_post_fee} < 3980
                    and basic_level = 0
                    and not exists
                        ( select id from orders
                            where
                            (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                    --and assign_level = 3
                    then 3
                when
                    first_purchase::decimal + {$pay_amt_without_post_fee} >= 3980
                    and first_purchase::decimal + {$pay_amt_without_post_fee} < 19800
                    and (basic_level = 0 or basic_level = 3)
                    and not exists
                        ( select id from orders
                            where
                            (is_pay = false or is_correct = false) and is_valid = true and is_first = true and user_id = {$user_id})
                    --and assign_level = 2
                    then 2
                when
                    first_purchase::decimal + {$pay_amt_without_post_fee} >= 19800
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
            update users set first_purchase = first_purchase::decimal + {$pay_amt_without_post_fee} where id = {$user_id};
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
                        turnover::decimal + {$pay_amt_without_post_fee}
                        -
                        ( select
                            case when ua.amount::decimal > pa.amount::decimal
                                           and ua.level <> 0 and p.is_admin = false
                            then (ua.amount::decimal - pa.amount::decimal)*o.count
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
                            when turnover::decimal >= 19800 and turnover::decimal < 39800
                                and (basic_level = 0 or basic_level = 3 )
                            then 2
                            when turnover::decimal >= 39800 and turnover::decimal < 198000
                                and (basic_level = 0 or basic_level = 3 or basic_level = 2)
                            then 1
                            when turnover::decimal >= 198000
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
                        turnover::decimal + {$pay_amt_without_post_fee}
                        -
                        ( select
                            case when ua.amount::decimal > pa.amount::decimal
                                           and ua.level <> 0 and p.is_admin = false
                            then (ua.amount::decimal - pa.amount::decimal)*o.count
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
                    then (ua.amount::decimal - pa.amount::decimal)*o.count
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
            --            then (ua.amount::decimal - pa.amount::decimal)*o.count
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
                    turnover::decimal < 39800
                    and basic_level = 0
                    then 3
                when
                    turnover::decimal >= 39800
                    and
                    turnover::decimal < 198000
                    and (basic_level = 0 or basic_level = 3)
                    then 2
                when
                    turnover::decimal >= 198000
                    then 1
                    else level
                end
            where id = {$parent_user_id}
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
}