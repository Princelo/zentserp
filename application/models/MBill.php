<?php
/**
 *
 **/
class MBill extends CI_Model
{
    private $objDB;

    function __construct()
    {
        parent::__construct();
        $this->objDB = $this->load->database("default", true);
    }

    public function objGetBillsOfDayWithFilter($date_from = '', $date_to = '', $current_user_id = '', $limit = '', $type = '')
    {
        $query_sql = "";
        $query_sql .= "
            select
                date(o.finish_time) as date,
                sum(b.volume)    volume,
                count(b.id)         count
            from
                bills b, users u, products p, orders o
            where
                b.user_id = u.id
                and
                b.order_id = o.id
                and
                o.product_id = p.id
                and o.is_pay = true
                and o.is_correct = true
                and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and u.id = {$current_user_id}
                and b.type = {$type}
            group by date(o.finish_time)
            order by date(o.finish_time);
            {$limit}
        ";
        //http://www.plumislandmedia.net/mysql/sql-reporting-time-intervals/
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

    public function objGetBillsOfDay($date_from = '', $date_to = '', $current_user_id = '', $limit = '', $type = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $days = $interval->days + 1;
        $query_sql = "
            SELECT d.date, count(b.id), sum(b.volume) volume FROM (
                select to_char(date_trunc('day', (date('{$date_from}') + offs)), 'YYYY-MM-DD')
                AS date
                FROM generate_series(0, {$days}, 1)
                AS offs
                ) d
            LEFT OUTER JOIN bills b
            ON (d.date=to_char(date_trunc('day', b.create_time), 'YYYY-MM-DD'))
            left join orders o
            on o.id = b.order_id
            left join users u
            on u.id = b.user_id
            where u.id = {$current_user_id}
                and b.type = {$type}
            GROUP BY d.date
            order by d.date
            {$limit};
        ";
        //http://stackoverflow.com/questions/15691127/postgresql-query-to-count-group-by-day-and-display-days-with-no-data
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

    public function objGetBillsOfMonthWithFilter($date_from = '', $date_to = '', $current_user_id = '', $limit = '', $type = '')
    {
        $query_sql = "
           select
               extract(year from o.finish_time) || ' ' ||
               to_char(o.finish_time,'Mon') as date,
               sum(b.volume) as volume,
               count(b.id) as count
           from
               bills b, users u, products p, orders o
           where
               b.user_id = u.id
               and
               b.order_id = o.id
               and
               o.product_id = p.id
               and o.is_pay = true
               and o.is_correct = true
               and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
               and u.id = {$current_user_id}
               and b.type = {$type}
               GROUP BY 1,2
               {$limit}
        ";
        //http://stackoverflow.com/questions/17492167/group-query-results-by-month-and-year-in-postgresql
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


    public function objGetBillsOfMonth($date_from = '', $date_to = '', $current_user_id = '', $limit = '', $type = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $months = $interval->y*12 + $interval->m + 1;
        $query_sql = "
            SELECT d.date, count(b.id), sum(b.volume) FROM (
                select to_char(date_trunc('month', (date('{$date_from}') + offs)), 'YYYY-MM-DD')
                AS date
                FROM generate_series(0, {$months}, 1)
                AS offs
                ) d
            LEFT OUTER JOIN bills b
            ON (d.date=to_char(date_trunc('day', b.create_time), 'YYYY-MM-DD'))
            left join orders o
            on o.id = b.order_id
            left join users u
            on u.id = b.user_id
            where u.id = {$current_user_id}
                and b.type = {$type}
            GROUP BY d.date
            order by d.date
            {$limit};
        ";
        //http://stackoverflow.com/questions/17492167/group-query-results-by-month-and-year-in-postgresql
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

    public function objGetBillsOfYearWithFilter($date_from = '', $date_to = '', $current_user_id = '', $type = '')
    {
        $query_sql = "
           select
               --to_char(o.finish_time,'Mon') || ' ' ||
               extract(year from o.finish_time) as date,
               sum(b.volume) as volume,
               count(b.id) as count
           from
               bills b, users u, products p, orders o
           where
               b.user_id = u.id
               and
               b.order_id = o.id
               and
               o.product_id = p.id
               and o.is_pay = true
               and o.is_correct = true
               and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
               and u.id = {$current_user_id}
               and b.type = {$type}
               GROUP BY 1
        ";
        //http://stackoverflow.com/questions/17492167/group-query-results-by-month-and-year-in-postgresql
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

    public function objGetBillsOfYear($date_from = '', $date_to = '', $current_user_id = '', $type = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $years = $interval->y + 1;
        $query_sql = "
            SELECT d.date, count(b.id), sum(b.volume) FROM (
                select to_char(date_trunc('year', (date('{$date_from}') + offs)), 'YYYY-MM-DD')
                AS date
                FROM generate_series(0, {$years}, 1)
                AS offs
                ) d
            LEFT OUTER JOIN bills b
            ON (d.date=to_char(date_trunc('day', b.create_time), 'YYYY-MM-DD'))
            left join orders o
            on o.id = b.order_id
            left join users u
            on u.id = b.user_id
            where u.id = {$current_user_id}
                and b.type = {$type}
            GROUP BY d.date
            order by d.date;
        ";
        //http://stackoverflow.com/questions/17492167/group-query-results-by-month-and-year-in-postgresql
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
            join price pr3 on pr3.level = 3 and pr3.product_id = p.id
            join price pr0 on pr0.level = 0 and pr0.product_id = p.id
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

}