<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        $this->load->model('MUser', 'MUser');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    public function listpage_admin($offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not admin.');
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
            $where = ' and is_admin = false ';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $level);
            $config['total_rows'] = $this->MUser->intGetUsersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $where = ' and is_admin = false ';
            $order = '';
            $data['users'] = $this->MUser->objGetUserList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('user/listpage_admin', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."user/listpage_admin/";
            $where = ' and is_admin = false ';
            $config['total_rows'] = $this->MUser->intGetUsersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = ' and p.is_valid = true ';
            $order = '';
            $data['users'] = $this->MUser->objGetUserList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('user/listpage_admin', $data);
        }
    }

    public function addRootUser($error = '')
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not admin.');
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'username',
                'label'   => '代理账号',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean|min_length[5]|max_length[12]|is_unique[users.username]'
            ),
            array(
                'field'   => 'password',
                'label'   => '代理密码',
                'rules'   => 'trim|xss_clean|required|min_length[8]|max_length[30]'
            ),
            array(
                'field'   => 'level',
                'label'   => '代理级別',
                'rules'   => 'trim|xss_clean|is_natural|required|greater_than[0]|less_than[4]'
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
            }else{
                $main_data = array(
                    'username' => $this->input->post('username'),
                    'password' => md5($this->input->post('password')),
                    'level' => $this->input->post('level'),
                    'name' => $this->input->post('name'),
                    'citizen_id' => $this->input->post('citizen_id'),
                    'mobile_no' => $this->input->post('mobile_no'),
                    'wechat_id' => $this->input->post('wechat_id'),
                    'qq_no' => $this->input->post('qq_no'),
                    'is_valid' => $this->input->post('is_valid'),
                );
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
        if($this->session->userdata('user') == ""){
            redirect('login', 'refresh');
        }
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'username',
                'label'   => '代理账号',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean|min_length[5]|max_length[12]|is_unique[users.username]'
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
            }else{
                $main_data = array(
                    'username' => $this->input->post('username'),
                    'password' => md5($this->input->post('password')),
                    'name' => $this->input->post('name'),
                    'citizen_id' => $this->input->post('citizen_id'),
                    'mobile_no' => $this->input->post('mobile_no'),
                    'wechat_id' => $this->input->post('wechat_id'),
                    'qq_no' => $this->input->post('qq_no'),
                    //'is_valid' => $this->input->post('is_valid'),
                );
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
            $this->load->view('templates/header', $data);
            $this->load->view('user/add', $data);
        }

    }

    private function __get_search_str($search = '', $level = '')
    {
        $where = '';
        if($search != '')
            $where .= " username like '%{$search}%' ";
        if($level != '')
            $where .= " level = {$level} ";
        return $where;
    }
}

