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
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $days = $interval->days + 1;
        $query_sql = "";
        $query_sql .= "
            select
                u.id,
                u.username,
                u.name,
                coalesce(
                    sum(s_amount.amount - o_self.return_profit),
                        '$0')
                    as self_turnover,
                coalesce(o_sub.volume - o_sub.return_profit, '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(o_sub.return_profit, '$0') normal_return_profit_sub2self,
                coalesce(o_sub_0.extra_return_profit, '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to,
                coalesce(date(o_self.finish_time)::char(10), date(o_self_0.finish_time)::char(10),
                    date(o_sub.finish_time)::char(10), d.date) as date
            FROM (
                select to_char(date_trunc('day', (date('{$date_from}') + offs)), 'YYYY-MM-DD')
                AS date
                FROM generate_series(0, {$days}, 1)
                AS offs
                ) d
            left join
                orders o_self
                on o_self.level <> 0
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                and date(o_self.finish_time)::char(10) = d.date
                and o_self.user_id = {$current_user_id}
            join amounts s_amount
                on s_amount.level = o_self.level and o_self.id = s_amount.order_id
            left join orders o_self_0
                on o_self_0.user_id = {$current_user_id}
                and o_self_0.level = 0
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
            full join (
                select sum(i.return_profit) return_profit, sum(i.volume) volume, i.pid, finish_time from(
                SELECT Sum(o.return_profit) AS return_profit,
                         Sum(sa.amount)       volume,
                         u.pid,
                         Date(o.finish_time)  finish_time
                  FROM   users u
                         LEFT JOIN orders o
                                ON o.finish_time BETWEEN '{$date_from} 00:00:00'
                                                         AND
                                                         '{$date_to} 23:59:59'
                                   AND o.is_pay = true
                                   AND o.is_correct = true
                                   AND o.user_id = u.id
                         JOIN amounts sa
                           ON sa.level = o.level
                              AND sa.order_id = o.id
                              AND o.level <> 0
                  WHERE  u.pid = {$current_user_id}
                  GROUP  BY u.pid,
                            Date(o.finish_time)
                  ORDER  BY u.pid) as i
                  group by pid,finish_time

                ) as o_sub
                on date(o_sub.finish_time)::char(10) = d.date
            full join (
                select sum(i.extra_return_profit) extra_return_profit, i.pid, finish_time from
                    (select
                           sum(o.extra_return_profit) as extra_return_profit,
                           u.pid,
                           date(o.finish_time) finish_time
                       from
                           users u
                           left join orders o
                           on o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                           and o.is_pay = true and o.is_correct = true
                           and o.user_id = u.id
                           where u.pid = {$current_user_id}
                           and o.level = 0
                       group by u.pid, date(o.finish_time)
                       order by u.pid ) as i
                       group by pid,finish_time
                ) as o_sub_0
                on date(o_sub_0.finish_time)::char(10) = d.date
            join users u
                on u.id = {$current_user_id}
            join users pu
                on u.pid = pu.id
            where 1 = 1
            group by u.id, u.username, u.name, u.pid, pu.id, pu.name, pu.username,o_sub.volume,
                o_sub.return_profit, o_sub_0.extra_return_profit
            , d.date,date(o_sub.finish_time)::char(10),
                date(o_self.finish_time)::char(10),date(o_self_0.finish_time)::char(10)
            order by date
            {$limit};
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

    public function objGetBillsOfDayWithFilterCount($date_from = '', $date_to = '', $current_user_id = '')
    {
        $query_sql = "";
        $query_sql .= "
            select
                count(distinct(date(o_self.finish_time)))
            from
                orders o_self
            where
                o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
            group by date(o_self.finish_time)
        ";
        $query = $this->objDB->query($query_sql);


        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        } else {
            return 0;
        }

        $query->free_result();

        return $count;
    }

    public function objGetBillsOfDay($date_from = '', $date_to = '', $current_user_id = '', $limit = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $days = $interval->days + 1;
        $query_sql = "
            select
                u.id,
                u.username,
                u.name,
                coalesce(
                    sum(s_amount.amount - o_self.return_profit),
                        '$0')
                    as self_turnover,
                coalesce(o_sub.volume - o_sub.return_profit, '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(o_sub.return_profit, '$0') normal_return_profit_sub2self,
                coalesce(o_sub_0.extra_return_profit, '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to,
                coalesce(date(o_self.finish_time)::char(10), date(o_self_0.finish_time)::char(10),
                    date(o_sub.finish_time)::char(10), d.date) as date
            FROM (
                select to_char(date_trunc('day', (date('{$date_from}') + offs)), 'YYYY-MM-DD')
                AS date
                FROM generate_series(0, {$days}, 1)
                AS offs
                ) d
            left join
                orders o_self
                on o_self.level <> 0
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                and date(o_self.finish_time)::char(10) = d.date
                and o_self.user_id = {$current_user_id}
            join amounts s_amount
                on s_amount.level = o_self.level and o_self.id = s_amount.order_id
            left join orders o_self_0
                on o_self_0.user_id = {$current_user_id}
                and o_self_0.level = 0
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
            full join (
                select sum(i.return_profit) return_profit, sum(i.volume) volume, i.pid, finish_time from(
                SELECT Sum(o.return_profit) AS return_profit,
                         Sum(sa.amount)       volume,
                         u.pid,
                         Date(o.finish_time)  finish_time
                  FROM   users u
                         LEFT JOIN orders o
                                ON o.finish_time BETWEEN '{$date_from} 00:00:00'
                                                         AND
                                                         '{$date_to} 23:59:59'
                                   AND o.is_pay = true
                                   AND o.is_correct = true
                                   AND o.user_id = u.id
                         JOIN amounts sa
                           ON sa.level = o.level
                              AND sa.order_id = o.id
                              AND o.level <> 0
                  WHERE  u.pid = {$current_user_id}
                  GROUP  BY u.pid,
                            Date(o.finish_time)
                  ORDER  BY u.pid) as i
                  group by pid,finish_time

                ) as o_sub
                on date(o_sub.finish_time)::char(10) = d.date
            full join (
                select sum(i.extra_return_profit) extra_return_profit, i.pid, finish_time from
                    (select
                           sum(o.extra_return_profit) as extra_return_profit,
                           u.pid,
                           date(o.finish_time) finish_time
                       from
                           users u
                           left join orders o
                           on o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                           and o.is_pay = true and o.is_correct = true
                           and o.user_id = u.id
                           where u.pid = {$current_user_id}
                           and o.level = 0
                       group by u.pid, date(o.finish_time)
                       order by u.pid ) as i
                       group by pid,finish_time
                ) as o_sub_0
                on date(o_sub_0.finish_time)::char(10) = d.date
            join users u
                on u.id = {$current_user_id}
            join users pu
                on u.pid = pu.id
            where 1 = 1
            group by u.id, u.username, u.name, u.pid, pu.id, pu.name, pu.username,o_sub.volume,
                o_sub.return_profit, o_sub_0.extra_return_profit
            , d.date,date(o_sub.finish_time)::char(10),
                date(o_self.finish_time)::char(10),date(o_self_0.finish_time)::char(10)
            order by date
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
        $days = $interval->days;
        $query_sql = "
            SELECT
                d.date,
                sum(o.pay_amt) as total_volume,
                sum(o.pay_amt_without_post_fee) as products_volume,
                sum(o.post_fee) as post_fee,
                coalesce(sum(o.return_profit), '$0') as normal_return_profit_volume,
                sum(o.return_profit) + sum(o.extra_return_profit) as return_profit_volume,
                coalesce(sum(o32.return_profit), '$0') as return_profit_3_2,
                coalesce(sum(o31.return_profit), '$0') as return_profit_3_1,
                coalesce(sum(o21.return_profit), '$0') as return_profit_2_1,
                coalesce(sum(o.extra_return_profit), '$0') as extra_return_profit_volume,
                sum(o.pay_amt_without_post_fee) - sum(o.post_fee) as products_cost,
                count(o.id) order_quantity
                FROM (
                select to_char(date_trunc('day', (date('{$date_from}') + offs)), 'YYYY-MM-DD')
                AS date
                FROM generate_series(0, {$days}, 1)
                AS offs
                ) d
            left join orders o
            on (d.date=to_char(date_trunc('day', o.finish_time), 'YYYY-MM-DD'))
                full join orders o32
                    on o32.id = o.id
                    and o32.level = 3 and o32.parent_level = 2
                full join orders o31
                    on o31.id = o.id
                    and o31.level = 3 and o31.parent_level = 1
                full join orders o21
                    on o21.id = o.id
                    and o21.level = 2 and o21.parent_level = 1
            where o.is_pay = true and o.is_correct = true
            group by d.date
            order by d.date
            {$limit}
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
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $months = $interval->y*12 + $interval->m + 1;
        $query_sql = "
            select
                u.id,
                u.username,
                u.name,
                coalesce(
                    sum(s_amount.amount - o_self.return_profit),
                        '$0')
                    as self_turnover,
                coalesce(o_sub.volume - o_sub.return_profit, '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(o_sub.return_profit, '$0') normal_return_profit_sub2self,
                coalesce(o_sub_0.extra_return_profit, '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to,
                coalesce(date(o_self.finish_time)::char(7), date(o_self_0.finish_time)::char(7),
                    date(o_sub.finish_time)::char(7), d.date::char(7)) as date
            FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,month_count::int)) date
                    from (
                       select extract(year from diff) * 12 + extract(month from diff) as month_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            left join
                orders o_self
                on o_self.level <> 0
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                and date(o_self.finish_time)::char(7) = d.date::char(7)
                and o_self.user_id = {$current_user_id}
            join amounts s_amount
                on s_amount.level = o_self.level and o_self.id = s_amount.order_id
            left join orders o_self_0
                on o_self_0.user_id = {$current_user_id}
                and o_self_0.level = 0
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
            full join (
                select sum(i.return_profit) return_profit, sum(i.volume) volume, i.pid, finish_time from(
                SELECT Sum(o.return_profit) AS return_profit,
                         Sum(sa.amount)       volume,
                         u.pid,
                         Date(o.finish_time)  finish_time
                  FROM   users u
                         LEFT JOIN orders o
                                ON o.finish_time BETWEEN '{$date_from} 00:00:00'
                                                         AND
                                                         '{$date_to} 23:59:59'
                                   AND o.is_pay = true
                                   AND o.is_correct = true
                                   AND o.user_id = u.id
                         JOIN amounts sa
                           ON sa.level = o.level
                              AND sa.order_id = o.id
                              AND o.level <> 0
                  WHERE  u.pid = {$current_user_id}
                  GROUP  BY u.pid,
                            Date(o.finish_time)
                  ORDER  BY u.pid) as i
                  group by pid,finish_time

                ) as o_sub
                on date(o_sub.finish_time)::char(7) = d.date::char(7)
            full join (
                select sum(i.extra_return_profit) extra_return_profit, i.pid, finish_time from
                    (select
                           sum(o.extra_return_profit) as extra_return_profit,
                           u.pid,
                           date(o.finish_time) finish_time
                       from
                           users u
                           left join orders o
                           on o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                           and o.is_pay = true and o.is_correct = true
                           and o.user_id = u.id
                           where u.pid = {$current_user_id}
                           and o.level = 0
                       group by u.pid, date(o.finish_time)
                       order by u.pid ) as i
                       group by pid,finish_time
                ) as o_sub_0
                on date(o_sub_0.finish_time)::char(7) = d.date::char(7)
            join users u
                on u.id = {$current_user_id}
            join users pu
                on u.pid = pu.id
            where 1 = 1
            group by u.id, u.username, u.name, u.pid, pu.id, pu.name, pu.username,
            o_sub.volume,
                o_sub.return_profit, o_sub_0.extra_return_profit
            , d.date,date(o_sub.finish_time)::char(7),
                date(o_self.finish_time)::char(7),date(o_self_0.finish_time)::char(7)
            order by date
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


    public function objGetBillsOfMonthWithFilterCount($date_from = '', $date_to = '', $current_user_id = '')
    {
        $query_sql = "
            select
                count(1)
            from
                orders o_self
            where 1 = 1
                and o_self.user_id = {$current_user_id}
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
            group by date_trunc('month', o_self.finish_time)
        ";
        //http://stackoverflow.com/questions/17492167/group-query-results-by-month-and-year-in-postgresql
        $query = $this->objDB->query($query_sql);


        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        } else {
            return 0;
        }

        $query->free_result();

        return $count;
    }


    public function objGetBillsOfMonth($date_from = '', $date_to = '', $current_user_id = '', $limit = '', $type = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $months = $interval->y*12 + $interval->m + 1;
        $query_sql = "
            select
                u.id,
                u.username,
                u.name,
                coalesce(
                    sum(s_amount.amount - o_self.return_profit),
                        '$0')
                    as self_turnover,
                coalesce(o_sub.volume - o_sub.return_profit, '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(o_sub.return_profit, '$0') normal_return_profit_sub2self,
                coalesce(o_sub_0.extra_return_profit, '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to,
                coalesce(date(o_self.finish_time)::char(7), date(o_self_0.finish_time)::char(7),
                    date(o_sub.finish_time)::char(7), d.date::char(7)) as date
            FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,month_count::int)) date
                    from (
                       select extract(year from diff) * 12 + extract(month from diff) as month_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            left join
                orders o_self
                on o_self.level <> 0
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                and date(o_self.finish_time)::char(7) = d.date::char(7)
                and o_self.user_id = {$current_user_id}
            join amounts s_amount
                on s_amount.level = o_self.level and o_self.id = s_amount.order_id
            left join orders o_self_0
                on o_self_0.user_id = {$current_user_id}
                and o_self_0.level = 0
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
            full join (
                select sum(i.return_profit) return_profit, sum(i.volume) volume, i.pid, finish_time from(
                SELECT Sum(o.return_profit) AS return_profit,
                         Sum(sa.amount)       volume,
                         u.pid,
                         Date(o.finish_time)  finish_time
                  FROM   users u
                         LEFT JOIN orders o
                                ON o.finish_time BETWEEN '{$date_from} 00:00:00'
                                                         AND
                                                         '{$date_to} 23:59:59'
                                   AND o.is_pay = true
                                   AND o.is_correct = true
                                   AND o.user_id = u.id
                         JOIN amounts sa
                           ON sa.level = o.level
                              AND sa.order_id = o.id
                              AND o.level <> 0
                  WHERE  u.pid = {$current_user_id}
                  GROUP  BY u.pid,
                            Date(o.finish_time)
                  ORDER  BY u.pid) as i
                  group by pid,finish_time

                ) as o_sub
                on date(o_sub.finish_time)::char(7) = d.date::char(7)
            full join (
                select sum(i.extra_return_profit) extra_return_profit, i.pid, finish_time from
                    (select
                           sum(o.extra_return_profit) as extra_return_profit,
                           u.pid,
                           date(o.finish_time) finish_time
                       from
                           users u
                           left join orders o
                           on o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                           and o.is_pay = true and o.is_correct = true
                           and o.user_id = u.id
                           where u.pid = {$current_user_id}
                           and o.level = 0
                       group by u.pid, date(o.finish_time)
                       order by u.pid ) as i
                       group by pid,finish_time
                ) as o_sub_0
                on date(o_sub_0.finish_time)::char(7) = d.date::char(7)
            join users u
                on u.id = {$current_user_id}
            join users pu
                on u.pid = pu.id
            where 1 = 1
            group by u.id, u.username, u.name, u.pid, pu.id, pu.name, pu.username,
            o_sub.volume,
                o_sub.return_profit, o_sub_0.extra_return_profit
            , d.date,date(o_sub.finish_time)::char(7),
                date(o_self.finish_time)::char(7),date(o_self_0.finish_time)::char(7)
            order by date
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
            SELECT  to_char(date_trunc('month', d.date), 'YYYY-MM') date,
                    count(o.id),
                    sum(o.pay_amt) as total_volume,
                    sum(o.pay_amt_without_post_fee) as products_volume,
                    sum(o.post_fee) as post_fee,
                    coalesce(sum(o.return_profit), '$0') as normal_return_profit_volume,
                    sum(o.return_profit) + sum(o.extra_return_profit) as return_profit_volume,
                    coalesce(sum(o32.return_profit), '$0') as return_profit_3_2,
                    coalesce(sum(o31.return_profit), '$0') as return_profit_3_1,
                    coalesce(sum(o21.return_profit), '$0') as return_profit_2_1,
                    coalesce(sum(o.extra_return_profit), '$0') as extra_return_profit_volume,
                    count(o.id) order_quantity,
                    sum(o.pay_amt_without_post_fee) - sum(o.post_fee) as products_cost
                    FROM (
                        select DATE '{$date_from}' + (interval '1' month * generate_series(0,month_count::int)) date
                            from (
                               select extract(year from diff) * 12 + extract(month from diff) as month_count
                                   from (
                                     select age(TIMESTAMP '{$date_to} 23:59:59', TIMESTAMP '{$date_from} 00:00:00') as diff
                               ) td
                            ) t
                        ) d
            left join orders o
            ON (to_char(date_trunc('month', d.date), 'YYYY-MM')=to_char(date_trunc('month', o.finish_time), 'YYYY-MM'))
                full join orders o32
                    on o32.id = o.id
                    and o32.level = 3 and o32.parent_level = 2
                full join orders o31
                    on o31.id = o.id
                    and o31.level = 3 and o31.parent_level = 1
                full join orders o21
                    on o21.id = o.id
                    and o21.level = 2 and o21.parent_level = 1
            where o.is_pay = true and o.is_correct = true
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

    public function objGetBillsOfYearWithFilter($date_from = '', $date_to = '', $current_user_id = '')
    {
        $query_sql = "
            select
                u.id,
                u.username,
                u.name,
                coalesce(
                    sum(o_self.pay_amt_without_post_fee - o_self.return_profit),
                        '$0')
                    as self_turnover,
                coalesce(sum(o_sub.pay_amt_without_post_fee - o_sub.return_profit - o_sub.extra_return_profit), '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(sum(o_sub.return_profit), '$0') normal_return_profit_sub2self,
                coalesce(sum(o_sub.extra_return_profit), '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to,
                coalesce(date(o_self.finish_time)::char(4), date(o_self_0.finish_time)::char(4),
                    date(o_sub.finish_time)::char(4), date(d.date)::char(4)) as date
            FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,year_count::int)) date
                    from (
                       select extract(year from diff) as year_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            join
                orders o_self
                on date(o_self.finish_time)::char(4) = d.date::char(4)
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                and o_self.user_id = {$current_user_id}
                and o_self.level <> 0
            left join users u
                on u.id = o_self.user_id
            left join
                orders o_self_0
                ON date(o_self_0.finish_time)::char(4) = d.date::char(4)
                and o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.user_id = {$current_user_id}
                and o_self_0.level = 0
            full join (
                select sum(o.return_profit) as return_profit,
                       sum(o.extra_return_profit) as extra_return_profit,
                       sum(o.pay_amt_without_post_fee) pay_amt_without_post_fee,
                       u.pid,
                       --date_trunc('day', o.finish_time) finish_time
                       o.finish_time
                   from
                       orders o, users u
                       where o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                       and o.is_pay = true and o.is_correct = true
                       and o.user_id = u.id
                       and u.pid = {$current_user_id}
                   group by u.pid, o.finish_time
                ) as o_sub
                on date(o_sub.finish_time)::char(4) = d.date::char(4)
            left join users pu
                on u.pid = pu.id
            where 1 = 1
            GROUP BY u.id, pu.id, d.date::char(4),date(o_sub.finish_time)::char(4),
                date(o_self.finish_time)::char(4),date(o_self_0.finish_time)::char(4),
                d.date
            order by d.date::char(4)
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

    public function objGetBillsOfYear($date_from = '', $date_to = '', $current_user_id = '')
    {
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $years = $interval->y + 1;
        $query_sql = "
            SELECT
                date(d.date)::char(4) as date,
                coalesce(sum(o_self.pay_amt_without_post_fee - o_self.return_profit), '$0') as self_turnover,
                u.id,
                u.username,
                u.name,
                coalesce(sum(o_sub.pay_amt_without_post_fee - o_sub.return_profit - o_sub.extra_return_profit), '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(sum(o_sub.return_profit), '$0') normal_return_profit_sub2self,
                coalesce(sum(o_sub.extra_return_profit), '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to,
                count(distinct(o_self.id)) as count
            FROM (
                select DATE '{$date_from}' + (interval '1' month * generate_series(0,year_count::int)) date
                    from (
                       select extract(year from diff) as year_count
                       from (
                         select age(TIMESTAMP '{$date_to} 00:00:00', TIMESTAMP '{$date_from} 00:00:00') as diff
                       ) td
                    ) t
                ) d
            LEFT OUTER JOIN
                orders o_self
                on o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                and o_self.user_id = {$current_user_id}
                and o_self.level <> 0
                and date(o_self.finish_time)::char(4) = date(d.date)::char(4)
            left join users u
                on u.id = o_self.user_id
            left join
                orders o_self_0
                on o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.user_id = {$current_user_id}
                and o_self_0.level = 0
                and date(o_self_0.finish_time)::char(4) = date(d.date)::char(4)
            full join (
                select sum(o.return_profit) as return_profit,
                       sum(o.extra_return_profit) as extra_return_profit,
                       sum(o.pay_amt_without_post_fee) pay_amt_without_post_fee,
                       u.pid,
                       --date_trunc('day', o.finish_time) finish_time
                       o.finish_time
                   from
                       orders o, users u
                       where o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                       and o.is_pay = true and o.is_correct = true
                       and o.user_id = u.id
                       and u.pid = {$current_user_id}
                   group by u.pid, o.finish_time
                ) as o_sub
                on date(o_sub.finish_time)::char(4) = date(d.date)::char(4)
            left join users pu
                on u.pid = pu.id
            where 1 = 1
            GROUP BY date(d.date)::char(4), u.id, pu.id
            order by date(d.date)::char(4)
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
            SELECT  to_char(date_trunc('year', d.date), 'YYYY') date,
                    count(o.id),
                    sum(o.pay_amt) as total_volume,
                    sum(o.pay_amt_without_post_fee) as products_volume,
                    sum(o.post_fee) as post_fee,
                    coalesce(sum(o.return_profit), '$0') as normal_return_profit_volume,
                    sum(o.return_profit) + sum(o.extra_return_profit) as return_profit_volume,
                    coalesce(sum(o32.return_profit), '$0') as return_profit_3_2,
                    coalesce(sum(o31.return_profit), '$0') as return_profit_3_1,
                    coalesce(sum(o21.return_profit), '$0') as return_profit_2_1,
                    coalesce(sum(o.extra_return_profit), '$0') as extra_return_profit_volume,
                    count(o.id) order_quantity,
                    sum(o.pay_amt_without_post_fee) - sum(o.post_fee) as products_cost
                    FROM (
                        select DATE '{$date_from}' + (interval '1' year * generate_series(0,year_count::int)) date
                            from (
                               select extract(year from diff) as year_count
                                   from (
                                     select age(TIMESTAMP '{$date_to} 23:59:59', TIMESTAMP '{$date_from} 00:00:00') as diff
                           ) td
                        ) t
                    ) d
            left join orders o
            ON (to_char(date_trunc('year', d.date), 'YYYY')=to_char(date_trunc('year', o.finish_time), 'YYYY'))
                full join orders o32
                    on o32.id = o.id
                    and o32.level = 3 and o32.parent_level = 2
                full join orders o31
                    on o31.id = o.id
                    and o31.level = 3 and o31.parent_level = 1
                full join orders o21
                    on o21.id = o.id
                    and o21.level = 2 and o21.parent_level = 1
            where o.is_pay = true and o.is_correct = true
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

    function objGetUserBills($date_from, $date_to, $limit = '')
    {
        $query_sql = "
            select
                u.id,
                u.username,
                u.name,
                coalesce(
                    sum(s_amount.amount - o_self.return_profit),
                        '$0')
                    as self_turnover,
                coalesce(o_sub.volume - o_sub.return_profit, '$0') as sub_turnover,
                coalesce(sum(o_self.return_profit), '$0') normal_return_profit_self2parent,
                coalesce(sum(o_self_0.extra_return_profit), '$0') extra_return_profit_self2parent,
                coalesce(o_sub.return_profit, '$0') normal_return_profit_sub2self,
                coalesce(o_sub_0.extra_return_profit, '$0') extra_return_profit_sub2self,
                pu.id pid,
                pu.name pname,
                pu.username pusername,
                '{$date_from}' date_from,
                '{$date_to}' date_to
            from
                users u
                left join
                orders o_self
                on u.id = o_self.user_id
                and o_self.level <> 0
                and o_self.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                and o_self.is_pay = true and o_self.is_correct = true
                join amounts s_amount
                on s_amount.level = o_self.level and o_self.id = s_amount.order_id
                left join orders o_self_0
                on u.id = o_self_0.user_id
                and o_self_0.level = 0
                and o_self_0.is_pay = true and o_self_0.is_correct = true
                and o_self_0.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                full join (
                select sum(i.return_profit) return_profit, sum(i.volume) volume, i.pid from(
                SELECT Sum(o.return_profit) AS return_profit,
                         Sum(sa.amount)       volume,
                         u.pid,
                         Date(o.finish_time)  finish_time
                  FROM   users u
                         LEFT JOIN orders o
                                ON o.finish_time BETWEEN '{$date_from} 00:00:00'
                                                         AND
                                                         '{$date_to} 23:59:59'
                                   AND o.is_pay = true
                                   AND o.is_correct = true
                                   AND o.user_id = u.id
                         JOIN amounts sa
                           ON sa.level = o.level
                              AND sa.order_id = o.id
                              AND o.level <> 0
                  WHERE  u.pid <> 1
                  GROUP  BY u.pid,
                            Date(o.finish_time)
                  ORDER  BY u.pid) as i
                  group by pid

                ) as o_sub
                on o_sub.pid = u.id
                full join (
                select sum(i.extra_return_profit) extra_return_profit, i.pid from
                    (select
                           sum(o.extra_return_profit) as extra_return_profit,
                           u.pid,
                           date(o.finish_time) finish_time
                       from
                           users u
                           left join orders o
                           on o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                           and o.is_pay = true and o.is_correct = true
                           and o.user_id = u.id
                           where u.pid <> 1
                           and o.level = 0
                       group by u.pid, date(o.finish_time)
                       order by u.pid ) as i
                       group by pid
                ) as o_sub_0
                on o_sub_0.pid = u.id
                join users pu
                on u.pid = pu.id
            where 1 = 1
            and u.id <> 1
            group by u.id, u.username, u.name, u.pid, pu.id, pu.name, pu.username,o_sub.volume,
                o_sub.return_profit, o_sub_0.extra_return_profit
            order by u.id
            {$limit}
            ;
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

    function intGetUserBillsCount($date_from, $date_to)
    {
        $query_sql = "
            select
                count(distinct(o.user_id))
            from
                orders o
            where 1 = 1
            and o.user_id <> 1
            and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
            and o.is_pay = true and o.is_correct = true
        ";
        $query = $this->objDB->query($query_sql);


        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        } else {
            return 0;
        }

        $query->free_result();

        return $count;

    }

}