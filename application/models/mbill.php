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
            order by date(o.finish_time)
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
            and b.type = {$type}
            and b.user_id = {$current_user_id}
            --left join orders o
            --    on o.id = b.order_id
            --left join users u
            --    on u.id = b.user_id
            --    and u.id = {$current_user_id}
            --where
            --    b.type = {$type}
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

    public function objGetZentsBillsOfDay($date_from = '', $date_to = '', $limit = '')
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
                and b.type = 1
            left join orders o
            on o.id = b.order_id
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
               GROUP BY 1
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
            SELECT to_char(date_trunc('month', d.date), 'YYYY-MM') date, count(b.id), sum(b.volume) volume FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,month_count::int)) date
                    from (
                       select extract(year from diff) * 12 + extract(month from diff) as month_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            left join bills b
            ON (to_char(date_trunc('month', d.date), 'YYYY-MM')=to_char(date_trunc('month', b.create_time), 'YYYY-MM'))
                and b.type = {$type}
                and b.user_id = {$current_user_id}
            --left join orders o
            --on o.id = b.order_id
            --left join users u
            --on u.id = o.user_id
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

    public function objGetZentsBillsOfMonth($date_from = '', $date_to = '', $limit = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $months = $interval->y*12 + $interval->m + 1;

        $query_sql = "
            SELECT to_char(date_trunc('month', d.date), 'YYYY-MM') date, count(b.id), sum(b.volume) volume FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,month_count::int)) date
                    from (
                       select extract(year from diff) * 12 + extract(month from diff) as month_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            left join bills b
            ON (to_char(date_trunc('month', d.date), 'YYYY-MM')=to_char(date_trunc('month', b.create_time), 'YYYY-MM'))
                and b.type = 1
            left join orders o
            on o.id = b.order_id
            GROUP BY d.date
            order by d.date
            {$limit};
        ";
        //http://stackoverflow.com/questions/7450515/postgresql-generate-series-of-months
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
            SELECT to_char(date_trunc('year', d.date), 'YYYY') date, count(b.id), sum(b.volume) volume FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,year_count::int)) date
                    from (
                       select extract(year from diff) as year_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            LEFT OUTER JOIN bills b
            ON (to_char(date_trunc('year', d.date), 'YYYY')=to_char(date_trunc('year', b.create_time), 'YYYY'))
            and b.type={$type}
            and b.user_id = {$current_user_id}
            --left join orders o
            --on o.id = b.order_id
            --left join users u
            --on u.id = b.user_id
            --where u.id = {$current_user_id}
                --and b.type = {$type}
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

    public function objGetZentsBillsOfYear($date_from = '', $date_to = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $years = $interval->y + 1;
        $query_sql = "
            SELECT to_char(date_trunc('year', d.date), 'YYYY') date, count(b.id), sum(b.volume) volume FROM (
                select DATE '{$date_from}' + (interval '1' year * generate_series(0,year_count::int)) date
                    from (
                       select extract(year from diff) as year_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            left join bills b
            ON (to_char(date_trunc('year', d.date), 'YYYY')=to_char(date_trunc('year', b.create_time), 'YYYY'))
                and b.type = 1
            left join orders o
            on o.id = b.order_id
            GROUP BY d.date
            order by d.date
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


    public function objGetProductBills($date_from, $date_to)
    {
        $query_sql = "
        select
              '{$date_from}' as date_from,
              '{$date_to}' as date_to,
              list.product_id product_id,
              p.title,
              case when p.is_trial then 'true' else 'false' end is_trial,
              list.quantity total_quantity,
              coalesce(list1.quantity, 0) quantity_1,
              coalesce(list2.quantity, 0) quantity_2,
              coalesce(list3.quantity, 0) quantity_3,
              coalesce(list0.quantity, 0) quantity_0,
              coalesce(listt.quantity, 0) quantity_t,
              case when p.is_trial then p.trial_price*list.quantity else list.amount end amount,
              coalesce(list0.amount*list0.quantity, '$0') amount_0,
              coalesce(list1.amount*list1.quantity, '$0') amount_1,
              coalesce(list2.amount*list2.quantity, '$0') amount_2,
              coalesce(list3.amount*list3.quantity, '$0') amount_3,
              coalesce(p.trial_price*listt.quantity, '$0') amount_t
            from
              (
                select
                  sum(op.quantity) quantity,
                  p.id product_id,
                  sum(pa.amount*op.quantity) amount
                from order_product op, orders o, product_amount pa, products p
                where
                  op.order_id = o.id
                  and pa.order_id = o.id
                  and pa.product_id = op.product_id
                    and pa.level = o.level
                  and p.id = op.product_id
                  and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                group by op.product_id, p.id--, pa.amount
              ) as list
              full JOIN (
                          select
                            sum(op.quantity) quantity,
                            p.id product_id,
                            pa.amount
                          from order_product op, orders o, product_amount pa, products p
                          where
                            op.order_id = o.id
                            and pa.order_id = o.id
                            and pa.product_id = op.product_id
                            and p.id = op.product_id
                            and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                            and o.level = 0
                            and pa.level = o.level
                            and p.is_trial != true
                          group by op.product_id, p.id, pa.amount
                        ) as list0
                on list0.product_id = list.product_id
              full join (
                          select
                            sum(op.quantity) quantity,
                            p.id product_id,
                            pa.amount
                          from order_product op, orders o, product_amount pa, products p
                          where
                            op.order_id = o.id
                            and pa.order_id = o.id
                            and pa.product_id = op.product_id
                            and p.id = op.product_id
                            and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                            and o.level = 1
                            and pa.level = o.level
                            and p.is_trial != true
                          group by op.product_id, p.id, pa.amount
                        ) as list1
                on list1.product_id = list.product_id
              full join (
                          select
                            sum(op.quantity) quantity,
                            p.id product_id,
                            pa.amount
                          from order_product op, orders o, product_amount pa, products p
                          where
                            op.order_id = o.id
                            and pa.order_id = o.id
                            and pa.product_id = op.product_id
                            and p.id = op.product_id
                            and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                            and o.level = 2
                            and pa.level = o.level
                            and p.is_trial != true
                          group by op.product_id, p.id, pa.amount
                        ) as list2
                on list2.product_id = list.product_id
              full join (
                          select
                            sum(op.quantity) quantity,
                            p.id product_id,
                            pa.amount
                          from order_product op, orders o, product_amount pa, products p
                          where
                            op.order_id = o.id
                            and pa.order_id = o.id
                            and pa.product_id = op.product_id
                            and p.id = op.product_id
                            and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                            and o.level = 3
                            and pa.level = o.level
                            and p.is_trial != true
                          group by op.product_id, p.id, pa.amount
                        ) as list3
                on list3.product_id = list.product_id
              full join (
                          select
                            sum(op.quantity) quantity,
                            p.id product_id,
                            pa.amount
                          from order_product op, orders o, product_amount pa, products p
                          where
                            op.order_id = o.id
                            and pa.order_id = o.id
                            and pa.product_id = op.product_id
                            and p.id = op.product_id
                            and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                            --and o.level = 1
                            and pa.level = o.level
                            and p.is_trial = true
                          group by op.product_id, p.id, pa.amount
                        ) as listt
                on listt.product_id = list.product_id
                join products p on p.id = list.product_id
            order by list.product_id
        ";
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }
        $query->free_result();
        return $data;
    }

}