<?php
/**
 *
 **/
class MUser extends CI_Model
{
    private $objDB;

    function __construct()
    {
        parent::__construct();
        $this->objDB = $this->load->database("default", true);
    }

    public function boolVerify( $login_id, $password)
    {
        $query_sql = "";
        $query_sql .= "
            select
                count(1) as count
            from
                users
                where
                username = ?
                and password = ?
                and is_valid = true
        ";
        $binds = array($login_id, $password);
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

    public function boolUpdatePassword($password, $login_id){
        $update_data = array("password" => $password);
        $update_sql =  $this->objDB->update_string('users', $update_data, array("id"=>$login_id));
        $result = $this->objDB->query($update_sql);
        if($result === true)
            return true;
        else
            return false;
    }

    public function addRootUser($main_data)
    {
        $current_user_id = $this->session->userdata('current_user_id');
        $now = now();
        $insert_sql_root_id = "";
        $insert_sql_root_id .= "
            insert into root_ids (create_time) values ('{$now}');
        ";
        $insert_sql_user = "";
        $insert_sql_user .= "
            insert into users
                (username, password, level, basic_level, name, citizen_id, mobile_no, wechat_id, qq_no, is_valid,
                root_id, pid, lft, rgt)
            values
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, currval('root_ids_id_seq'), 1, 1, 2);
            update users set initiation = true where level <> 0 and id = currval('users_id_seq');
        ";
        $binds = array(
            $main_data['username'], $main_data['password'], $main_data['level'], $main_data['level'],
            $main_data['name'],
            $main_data['citizen_id'], $main_data['mobile_no'], $main_data['wechat_id'], $main_data['qq_no'],
            $main_data['is_valid'],
        );

        $this->objDB->trans_start();

        $this->objDB->query($insert_sql_root_id);
        $this->objDB->query($insert_sql_user, $binds);
        $this->objDB->query("insert into cart(user_id) values(currval('users_id_seq'));");

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true){
            return true;
        }else{
            return false;
        }
    }

    public function add($main_data)
    {
        $current_user_id = $this->session->userdata('current_user_id');
        $function = "
        CREATE OR REPLACE FUNCTION update_left_right(pic int)
            RETURNS void AS $$
            declare
                update_node int
                current_root_id int;
            BEGIN
            select
                rgt, root_id
                into update_node, current_root_id
            from users where id = pid
            ;
            update users set lft = case when lft >= update_node then lft + 2
                                              else lft end,
                                 rgt = rgt + 2
                where rgt >= update_node -- - 1
                      and root_id = current_root_id;
            END;
            $$ LANGUAGE plpgsql;
        ";
        $update_left_right_sql = "";
        $update_left_right_sql .= "
            --with variables as (select rgt, root_id from users where id = {$current_user_id})
            select rgt, root_id into temp table variables from users where id = {$current_user_id};
            update users set lft = case when lft >= (select rgt from variables) then lft + 2
                                      else lft end,
                              rgt = rgt + 2
                   where rgt >= (select rgt from variables)
                         and root_id = (select root_id from variables);
            --call update_left_right({$current_user_id});
        ";
        $insert_sql_user = "";
        $insert_sql_user .= "
            --with variables as (select rgt-2 rgt, root_id from users where id = {$current_user_id})
            insert into users
            (username, password, name, citizen_id, mobile_no, wechat_id, qq_no, root_id, pid, lft, rgt)
            values
            (?, ?, ?, ?, ?, ?, ?, (select root_id from variables), {$current_user_id},
            (select rgt from variables), (select rgt + 1 from variables));
        ";
        $binds = array(
            $main_data['username'], $main_data['password'], $main_data['name'],
            $main_data['citizen_id'], $main_data['mobile_no'], $main_data['wechat_id'], $main_data['qq_no'],
        );

        //debug($update_left_right_sql);
        //debug($insert_sql_user);
        $this->objDB->trans_start();

        $this->objDB->query("set constraints all deferred;");
        $this->objDB->query($update_left_right_sql);
        $this->objDB->query($insert_sql_user, $binds);
        $this->objDB->query("insert into cart(user_id) values(currval('users_id_seq'));");

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true){
            return true;
        }else{
            return false;
        }
    }

    public function strGetRoleType($username)
    {
        $query_sql = "";
        $query_sql .= "
            select is_admin from users where username = ?;
        ";
        $binds = array($username);
        $result = $this->objDB->query($query_sql, $binds);
        if($result->result()[0]->is_admin === 't')
            return 'admin';
        else
            return 'user';
    }

    public function intGetCurrentUserId($username)
    {
        $query_sql = "";
        $query_sql .= "
            select id from users where username = ?;
        ";
        $binds = array($username);
        $result = $this->objDB->query($query_sql, $binds);
        if($result->result()[0]->id > 0)
            return $result->result()[0]->id;
        else
            exit('error');
    }

    public function intGetCurrentUserLevel($id)
    {
        $query_sql = "";
        $query_sql .= "
            select level from users where id = ?;
        ";
        $binds = array($id);
        $result = $this->objDB->query($query_sql, $binds);
        if($result->result()[0]->level >= 0)
            return $result->result()[0]->level;
        else
            exit('error');
    }

    public function intGetUsersCount($where = '')
    {
        $query_sql = "
            select count(1) from users u
            where 1 = 1
            {$where};
        ";
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        }else{
            return 0;
        }

        $query->free_result();

        return $count;
    }

    public function objGetUserList($where = '', $order = '', $limit = '')
    {
        $query_sql = "";
        $query_sql .= "
            select
                u.*
            from
                users u
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
        }else{
            return 0;
        }
        $query->free_result();

        //debug($data);
        return $data;
    }

    public function objGetUserInfo($id = '')
    {
        $query_sql = "";
        $query_sql .= "
            select
                *
            from
                users
            where
                id = ?
        ";
        $binds = array($id);
        $data = array();
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0){
            return $query->result()[0];
        }else{
        }
        $query->free_result();

        //debug($data);
        return $data;
    }

    public function update($data, $id)
    {
        $update_sql = $this->objDB->update_string("users", $data, array("id" => $id));
        if($data['level'] != '0')
            $update_sql_initiation = "update users set initiation = true where initiation = false and id = {$id};";
        else
            $update_sql_initiation = "";
        $this->objDB->trans_start();

        $this->objDB->query($update_sql);
        if($data['level'] != '0')
            $this->objDB->query($update_sql_initiation);

        $this->objDB->trans_complete();

        $result = $this->objDB->trans_status();

        if($result === true) {
            return true;
        }else {
            return false;
        }
    }

    public function objGetSubUserList($where = '', $iwhere = '', $order = '', $limit = '')
    {
        $query_sql = "
            SELECT u.*
                FROM users AS u,
                        users AS p,
                        users AS sub_parent,
                        (
                                SELECT p.id, (COUNT(iparent.id) - 1) AS idepth
                                FROM users AS p,
                                        users AS iparent
                                WHERE p.lft BETWEEN iparent.lft AND iparent.rgt
                                        --and p.rgt between iparent.lft and iparent.rgt
                                        and p.root_id = iparent.root_id
                                        {$iwhere}
                                GROUP BY p.id
                                ORDER BY p.lft
                        )AS sub_tree
                WHERE u.lft BETWEEN p.lft AND p.rgt
                        AND u.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                        --and node.rgt between sub_parent.lft and sub_parent.rgt
                        AND sub_parent.id = sub_tree.id
                        and u.root_id = sub_parent.root_id
                        and p.root_id = u.root_id
                        {$where}
                GROUP BY u.id, sub_tree.idepth
                HAVING  (COUNT(p.id) - (sub_tree.idepth + 1)) = 1
                {$order}
                {$limit}
        ";
        $query = $this->objDB->query($query_sql);
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $val) {
                $data[] = $val;
            }
        }else{
            return 0;
        }
        $query->free_result();

        return $data;
    }

    public function intGetSubUsersCount($where = '', $iwhere = '')
    {
        $query_sql = "
            SELECT count(u.id)
                FROM users AS u,
                        users AS p,
                        users AS sub_parent,
                        (
                                SELECT p.id, (COUNT(iparent.id) - 1) AS idepth
                                FROM users AS p,
                                        users AS iparent
                                WHERE p.lft BETWEEN iparent.lft AND iparent.rgt
                                        --and p.rgt between iparent.lft and iparent.rgt
                                        and p.root_id = iparent.root_id
                                        {$iwhere}
                                GROUP BY p.id
                                ORDER BY p.lft
                        )AS sub_tree
                WHERE u.lft BETWEEN p.lft AND p.rgt
                        AND u.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                        --and node.rgt between sub_parent.lft and sub_parent.rgt
                        AND sub_parent.id = sub_tree.id
                        and u.root_id = 8
                        and sub_parent.root_id = 8
                        and p.root_id= 8
                        {$where}
                GROUP BY u.id, sub_tree.idepth
                HAVING  (COUNT(p.id) - (sub_tree.idepth + 1)) = 1
                limit 1;
        ";
        //debug($query_sql);exit;
        $query = $this->objDB->query($query_sql);
        $count = 0;
        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        }

        $query->free_result();

        return $count;
    }

    public function isParent($pid, $id)
    {
        $query_sql = "";
        $query_sql .= "
            select count(p.id)
                from users as p, users as u
                where
                1 = 1
                and u.pid = p.id
                and p.id = ?
                and u.id = ?
        ";
        $binds = array($pid, $id);
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0) {
            $count = $query->row()->count;
        }else{
            return false;
        }

        $query->free_result();
        if($count > 0)
            return true;
    }

    public function getSuperiorInfo($id)
    {
        $query_sql = "
            select * from users where id = (select pid from users where id = ?);
        ";
        $binds = array($id);
        $query = $this->objDB->query($query_sql, $binds);
        if($query->num_rows() > 0){
            $data = $query->result()[0];
        }else{
        }
        $query->free_result();

        //debug($data);
        return $data;
    }

}
