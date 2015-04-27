<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class User extends MY_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'user')
            redirect('login');
        $this->load->model('MUser', 'MUser');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    public function listpage($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $current_user_id = $this->session->userdata('current_user_id');
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  '用戶名',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'level',
                'label' =>  'Level',
                'rules' =>  'trim|xss_clean|is_natural'
            ),
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('level', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $level = $this->input->get('level', true);
            $data = array();
            $config['base_url'] = base_url()."user/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $iwhere = " and p.id = {$current_user_id} ";
            //$where .= ' and p.is_valid = true ';
            $where = "";
            $where .= $this->__get_search_str($search, $level);
            $config['total_rows'] = $this->MUser->intGetSubUsersCount($where, $iwhere);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            //$where = ' and is_admin = false ';
            $order = ' order by u.id ';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('user/listpage', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."user/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $iwhere = " and p.id = {$current_user_id} ";
            $where = "";
            $config['total_rows'] = $this->MUser->intGetSubUsersCount($where, $iwhere);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = ' and p.is_valid = true ';
            $order = ' order by u.id ';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('user/listpage', $data);
        }
    }

    public function sublistpage($id, $offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $current_user_id = //$this->session->userdata('current_user_id');
            $id;
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  '用戶名',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'level',
                'label' =>  'Level',
                'rules' =>  'trim|xss_clean|is_natural'
            ),
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('level', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $level = $this->input->get('level', true);
            $data = array();
            $config['base_url'] = base_url()."user/sublistpage/".$id;
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $iwhere = " and p.id = {$current_user_id} ";
            //$where .= ' and p.is_valid = true ';
            $where = "";
            $where .= $this->__get_search_str($search, $level);
            $config['total_rows'] = $this->MUser->intGetSubUsersCount($where, $iwhere);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            //$where = ' and is_admin = false ';
            $order = ' order by u.id ';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('user/sublistpage', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."user/sublistpage/".$id;
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $iwhere = " and p.id = {$current_user_id} ";
            $where = "";
            $config['total_rows'] = $this->MUser->intGetSubUsersCount($where, $iwhere);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = ' and p.is_valid = true ';
            $order = ' order by u.id ';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('user/sublistpage', $data);
        }
    }

    public function listpage_admin($offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  '用戶名',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'level',
                'label' =>  'Level',
                'rules' =>  'trim|xss_clean|numeric'
            ),
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('level', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $level = $this->input->get('level', true);
            $data = array();
            $config['base_url'] = base_url()."user/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = ' and u.is_admin = false ';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $level);
            $config['total_rows'] = $this->MUser->intGetUsersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            //$where = ' and is_admin = false ';
            $order = ' order by u.id ';
            $data['users'] = $this->MUser->objGetUserList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('user/listpage_admin', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."user/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = ' and u.is_admin = false ';
            $config['total_rows'] = $this->MUser->intGetUsersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = ' and p.is_valid = true ';
            $order = ' order by u.id ';
            $data['users'] = $this->MUser->objGetUserList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('user/listpage_admin', $data);
        }
    }

    public function addRootUser($error = '')
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'username',
                'label'   => '代理账号',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean|min_length[5]|max_length[16]|is_unique[users.username]'
            ),
            array(
                'field'   => 'level',
                'label'   => '代理级別',
                'rules'   => 'trim|xss_clean|is_natural|required|less_than[4]'
            ),
            array(
                'field'   => 'name',
                'label'   => '姓名',
                'rules'   => 'trim|xss_clean|required|min_length[2]|max_length[10]'
            ),
            array(
                'field'   => 'citizen_id',
                'label'   => '身份证',
                'rules'   => 'trim|xss_clean|min_length[10]|max_length[20]'
            ),
            array(
                'field'   => 'mobile_no',
                'label'   => '移动电话',
                'rules'   => 'trim|xss_clean|required|min_length[10]|max_length[20]|is_unique[users.mobile_no]'
            ),
            array(
                'field'   => 'wechat_id',
                'label'   => '微信号',
                'rules'   => 'trim|xss_clean|max_length[50]|'
            ),
            array(
                'field'   => 'qq_no',
                'label'   => 'QQ号',
                'rules'   => 'trim|xss_clean|required|min_length[5]|max_length[50]|is_unique[users.qq_no]'
            ),
            array(
                'field'   => 'is_valid',
                'label'   => '是否生效',
                'rules'   => 'trim|xss_clean|required|boolean'
            ),
        );

        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('user/add_root_user', $data);
                exit;
            }else{
                $main_data = array(
                    'username' => $this->input->post('username'),
                    'password' => md5($this->input->post('password')),
                    'level' => $this->input->post('level'),
                    //'assign_level' => $this->input->post('assign_level'),
                    'name' => $this->input->post('name'),
                    'citizen_id' => $this->input->post('citizen_id'),
                    'mobile_no' => $this->input->post('mobile_no'),
                    'wechat_id' => $this->input->post('wechat_id'),
                    'qq_no' => $this->input->post('qq_no'),
                    'is_valid' => $this->input->post('is_valid'),
                );
                if(  $_POST['password'] != $_POST['password2'])
                {
                    $this->session->set_flashdata('flashdata', '兩次輸入密碼不一致');
                    redirect('user/addRootUser');
                }
                $result = $this->MUser->addRootUser($main_data);
                if($result){
                    $this->session->set_flashdata('flashdata', '代理账号添加成功');
                    redirect('user/addRootUser');
                }
                else{
                    $this->session->set_flashdata('flashdata', '代理账号添加失败');
                    redirect('user/addRootUser');
                }
            }
        }else{
            $this->load->view('templates/header', $data);
            $this->load->view('user/add_root_user', $data);
        }

    }


    public function add($error = '')
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'username',
                'label'   => '代理账号',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean|min_length[5]|max_length[16]|is_unique[users.username]'
            ),
            array(
                'field'   => 'password',
                'label'   => '代理密码',
                'rules'   => 'trim|xss_clean|required|min_length[8]|max_length[30]'
            ),
            array(
                'field'   => 'name',
                'label'   => '姓名',
                'rules'   => 'trim|xss_clean|required|min_length[2]|max_length[10]'
            ),
            array(
                'field'   => 'citizen_id',
                'label'   => '身份证',
                'rules'   => 'trim|xss_clean|min_length[10]|max_length[20]'
            ),
            array(
                'field'   => 'mobile_no',
                'label'   => '移动电话',
                'rules'   => 'trim|xss_clean|required|min_length[10]|max_length[20]|is_unique[users.mobile_no]'
            ),
            array(
                'field'   => 'wechat_id',
                'label'   => '微信号',
                'rules'   => 'trim|xss_clean|max_length[50]|'
            ),
            array(
                'field'   => 'qq_no',
                'label'   => 'QQ号',
                'rules'   => 'trim|xss_clean|required|min_length[5]|max_length[50]|is_unique[users.qq_no]'
            ),
            /*array(
                'field'  => 'level',
                'label'  => 'Level',
                'rules'  => 'trim|xss_clean|required|is_natural|greater_than[0]|less_than[4]'
            ),*/
            /*array(
                'field'   => 'is_valid',
                'label'   => '是否生效',
                'rules'   => 'trim|xss_clean|required|boolean'
            ),*/
        );

        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('user/add', $data);
                exit;
            }else{
                $main_data = array(
                    'username' => $this->input->post('username'),
                    'password' => md5($this->input->post('password')),
                    'name' => $this->input->post('name'),
                    'citizen_id' => $this->input->post('citizen_id'),
                    'mobile_no' => $this->input->post('mobile_no'),
                    'wechat_id' => $this->input->post('wechat_id'),
                    'qq_no' => $this->input->post('qq_no'),
                    //'assign_level' => $this->input->post('level'),
                    //'is_valid' => $this->input->post('is_valid'),
                );
                if(  $_POST['password'] != $_POST['password2'])
                {
                    $this->session->set_flashdata('flashdata', '兩次輸入密碼不一致');
                    redirect('user/addRootUser');
                }
                $result = $this->MUser->add($main_data);
                if($result){
                    $this->session->set_flashdata('flashdata', '代理账号添加成功');
                    redirect('user/add');
                }
                else{
                    $this->session->set_flashdata('flashdata', '代理账号添加失败');
                    redirect('user/add');
                }
            }
        }else{
            $this->load->view('templates/header_user', $data);
            $this->load->view('user/add', $data);
        }

    }

    public function details_admin($id)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $config = array(
            /*array(
                'field'   => 'username',
                'label'   => '代理账号',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean|min_length[5]|max_length[12]|is_unique[users.username]'
            ),*/
            /*array(
                'field'   => 'password',
                'label'   => '代理密码',
                'rules'   => 'trim|xss_clean|required|min_length[8]|max_length[30]'
            ),*/
            array(
                'field'   => 'level',
                'label'   => '代理级別',
                'rules'   => 'trim|xss_clean|is_natural|required|less_than[4]'
            ),
            array(
                'field'   => 'name',
                'label'   => '姓名',
                'rules'   => 'trim|xss_clean|required|min_length[2]|max_length[10]'
            ),
            array(
                'field'   => 'citizen_id',
                'label'   => '身份证',
                'rules'   => 'trim|xss_clean|min_length[10]|max_length[20]'
            ),
            array(
                'field'   => 'mobile_no',
                'label'   => '移动电话',
                'rules'   => 'trim|xss_clean|required|min_length[10]|max_length[20]'
            ),
            array(
                'field'   => 'wechat_id',
                'label'   => '微信号',
                'rules'   => 'trim|xss_clean|max_length[50]|'
            ),
            array(
                'field'   => 'qq_no',
                'label'   => 'QQ号',
                'rules'   => 'trim|xss_clean|required|min_length[5]|max_length[50]'
            ),
            array(
                'field'   => 'is_valid',
                'label'   => '是否生效',
                'rules'   => 'trim|xss_clean|required|boolean'
            ),
        );
        $data = array();
        $data['id'] = $id;
        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            $data['v'] = $this->MUser->objGetUserInfo($id);
            if($this->input->post('level') == 0 && $data['v']->level != 0)
            {
                $this->session->set_flashdata('flashdata', '错误：不能设代理降级至零售商');
                redirect('user/details_admin/' . $id);
            }

            if ($this->form_validation->run() == FALSE)
            {
                //$this->load->view('templates/header', $data);
                //$this->load->view('user/details_admin', $data);
                redirect('user/details_admin/' . $id);
            }else{
                $main_data = array(
                    'username' => $this->input->post('username'),
                    'level' => $this->input->post('level'),
                    'basic_level' => $this->input->post('level'),
                    'name' => $this->input->post('name'),
                    'citizen_id' => $this->input->post('citizen_id'),
                    'mobile_no' => $this->input->post('mobile_no'),
                    'wechat_id' => $this->input->post('wechat_id'),
                    'qq_no' => $this->input->post('qq_no'),
                    'is_valid' => $this->input->post('is_valid'),
                );
                $result = $this->MUser->update($main_data, $id);
                if($result){
                    $this->session->set_flashdata('flashdata', '代理账号修改成功');
                    redirect('user/details_admin/'. $id);
                }
                else{
                    $this->session->set_flashdata('flashdata', '代理账号修改失败');
                    redirect('user/details_admin/'. $id);
                }
            }
        }else{
            $data['v'] = $this->MUser->objGetUserInfo($id);
            $data['id'] = $id;
            $this->load->view('templates/header', $data);
            $this->load->view('user/details_admin', $data);
        }

    }

    public function my_superior()
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $data['v'] = $this->MUser->getSuperiorInfo($this->session->userdata('current_user_id'));
        $this->load->view('templates/header_user', $data);
        $this->load->view('user/my_superior', $data);
    }

    public function password($error = ''){
        $data = array();
        $data['error'] = $error;
        if($this->session->userdata('role') == 'admin'){

            $this->load->view('templates/header', $data);

        }
        else{
            $this->load->view('templates/header_user', $data);
        }
        $this->load->view('user/password', $data);
    }


    public function passwordupdate(){
        if(isset($_POST['password-original']) && $_POST['password-original'] != ""
            && isset($_POST['password']) && isset($_POST['password2']) && $_POST['password'] != "" && $_POST['password2'] != ""
            && $_POST['password'] == $_POST['password2']){
            $_POST['password'] = md5($_POST['password']);
            $_POST['password2'] = md5($_POST['password2']);
            $result = false;
            if($this->MUser->boolVerify($this->session->userdata('user'), md5($_POST['password-original']))){
                $result = $this->MUser->boolUpdatePassword($_POST['password'], $this->session->userdata('current_user_id'));
            }else{
                $this->session->set_flashdata('flashdata', '原密码错误');
            }
            if($result === true)
                $this->session->set_flashdata('flashdata', '更改成功');
            else
                $this->session->set_flashdata('flashdata', '未知错误');
            redirect('user/password');
        }else{
            $this->session->set_flashdata('flashdata', '请输入完整信息並保证输入相同密码');
            redirect('user/password');
        }
    }


    private function __get_search_str($search = '', $level = '')
    {
        $where = '';
        if($search != '')
            $where .= " and (u.username like '%{$search}%' or u.name like '%{$search}%' ) ";
        if($level != '')
            $where .= " and u.level = {$level} ";
        return $where;
    }
}

