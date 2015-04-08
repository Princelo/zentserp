<?php
/**
 *
 **/
class MTrialOrder extends CI_Model
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
                   u.username       username,
                   u.id             uid,
                   u.name           name_ch,
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
                trial_orders o, trial_products p, users u, address_books b, trial_amounts a
            where
                o.user_id = u.id
                and
                o.product_id = p.id
                and
                o.address_book_id = b.id
                and
                a.order_id = o.id
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

            insert into trial_orders
                (user_id, product_id, count, level, parent_level, address_book_id, is_post, post_fee, is_first, pay_method)
            select {$current_user_id} user_id,
                    ? product_id,
                    ? count,
                    u.level as level,
                    u.plevel parent_level,
                    currval('address_books_id_seq') address_book_id,
                    ? is_post,
                    ? post_fee,
                    false,
                    ?
                from
                (select c.level as level, p.level plevel
                    from users c, users p where c.id = {$current_user_id}
                        and c.pid = p.id) as u
            ;
        ";
        $binds_order = array(
            $main_data['product_id'], $main_data['count'], $main_data['is_post'], $post_fee, $main_data['pay_method']
        );

        $insert_sql_amount = "";
        $insert_sql_amount .= "
            insert into trial_amounts (amount, order_id)
            values
            (
                (select p.price from trial_products p where p.id = ?),
                currval('trial_orders_id_seq')
            )
            ;
        ";
        $binds_amount = array($main_data['product_id']);


        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_address, $binds_address);
        $this->objDB->query($insert_sql_order, $binds_order);
        $this->objDB->query($insert_sql_amount, $binds_amount);
        $inserted_order_id_result = $this->objDB->query(
            "select currval('trial_orders_id_seq') id;"
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
            select count(1) from trial_orders o, trial_products p
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
                trial_orders o, trial_products p, users u, address_books b, trial_amounts a
            where
                o.user_id = u.id
                and
                o.product_id = p.id
                and
                o.address_book_id = b.id
                and
                a.order_id = o.id
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

    function finish_with_pay($order_id, $pay_amt, $user_id, $pay_amt_without_post_fee)
    {
        $now = now();
        $order_id = $this->objDB->escape($order_id);
        $update_sql = "
            update trial_orders
                set pay_amt = '{$pay_amt}',
                    is_pay = true,
                    is_correct = true,
                    pay_amt_without_post_fee = '{$pay_amt_without_post_fee}',
                    update_time = '{$now}',
                    finish_time = '{$now}'
            where
                id = {$order_id};
        ";
        /*$update_bills = "
            insert into bills (user_id, order_id, volume, type, reason)
            values
            ({$user_id}, {$order_id}, 0, 3, 1);
            update users set current_bill_id = currval('bills_id_seq') where id = {$user_id};
            insert into bills (user_id, order_id, volume, type, reason, pay_amt_without_post_fee)
            values
            ({$user_id}, {$order_id}, {$pay_amt}, 1, 1, {$pay_amt_without_post_fee});

        ";*/
        /*$finish_log = "
            insert into
                finish_log(order_id, pay_amt, user_id, parent_user_id, is_root, pay_amt_without_post_fee, is_first)
                values
                ({$order_id}, ?, ?, ?, ?, ?, ?)
            ;";
        $binds_finish_log = array(
            $pay_amt, $user_id, $parent_user_id, $is_root, $pay_amt_without_post_fee, $is_first
        );*/
        $this->objDB->trans_start();

        $this->objDB->query("set constraints all deferred");
        $this->objDB->query($update_sql);
        //$this->objDB->query($update_bills);
        //$this->objDB->query($finish_log, $binds_finish_log);

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
        $query_sql = "select count(1) from trial_orders where id = ? and user_id = ?;";
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
        $this->objDB->from("trial_orders");
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
                trial_orders
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

    public function updateOrderTradeNo($trade_no, $order_id)
    {
        $data['trade_no'] = $trade_no;
        $where = array(
            'is_pay'    =>  'false',
            'pay_method'    =>  'alipay',
            'id'    =>  $order_id,
        );
        $update_sql = $this->objDB->update_string('trial_orders', $data, $where);
        $query = $this->objDB->query($update_sql);

        if($query === true)
            return true;
        else
            return false;
    }


    public function getOrderPrice($id)
    {
        if($this->is_paid($id))
            exit('This order has paid!');
        $query_sql = "
            select
                p.price::decimal * o.count pay_amt_without_post_fee,
                o.post_fee::decimal as post_fee,
                p.price::decimal * o.count + o.post_fee::decimal as total
            from
                trial_orders o, trial_products p
            where
                o.id = ?
            and o.is_pay = false
            ;";
        $binds = array($id);
        $query = $this->objDB->query($query_sql, $binds);
        $data = $query->result()[0];
        $query->free_result();

        return $data;
    }

    public function updatePaymentStatus($trade_no)
    {
        $data = array();
        $data['is_pay'] = 'true';
        $where = array(
            'trade_no'  =>  $trade_no,
        );
        $update_sql = $this->objDB->update_string('trial_orders', $data, $where);
        $query = $this->objDB->query($update_sql);

        if($query === true)
            return true;
        else
            return false;
    }

}