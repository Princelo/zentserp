<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class Report extends MY_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'user')
            redirect('login');
        $this->load->model('MProduct', 'MProduct');
        $this->load->model('MBill', 'MBill');
        $this->load->model('MUser', 'MUser');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    public function index()
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $this->load->view('templates/header_user');
        $this->load->view('report/index');
    }

    public function index_admin($offset = 0)
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
            $config['base_url'] = base_url()."report/index_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
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
            //$where = ' and is_admin = false ';
            $order = '';
            $data['users'] = $this->MUser->objGetUserList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('report/listuser_admin', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."report/index_admin/";
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
            $order = '';
            $data['users'] = $this->MUser->objGetUserList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('report/listuser_admin', $data);
        }
        //$this->load->view('templates/header');
        //$this->load->view('report/index_admin');
    }

    public function index_user()
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $user_id = $this->input->get('user');
        if(!is_numeric($user_id))
            exit('ERROR');
        $this->load->view('templates/header');
        $this->load->view('report/index_user');
    }

    public function index_all_users()
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $this->load->view('templates/header');
        $this->load->view('report/index_all_users');
    }

    public function index_zents()
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $this->load->view('templates/header');
        $this->load->view('report/index_zents');
    }

    public function index_sub($offset = 0)
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
            $config['base_url'] = base_url()."report/index_sub/";
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
            $order = '';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('report/index_sub', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."report/index_sub/";
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
            $order = '';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('report/index_sub', $data);
        }
    }

    public function query_sub($id)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $pid = $this->session->userdata('current_user_id');
        if(!is_numeric($id))
            exit('ERROR');
        if(!$this->MUser->isParent($pid, $id))
            exit('You are not the Superior of this user');
        $data['id'] = $id;
        $this->load->view('templates/header_user');
        $this->load->view('report/query_sub', $data);
    }

    public function listpage_sub($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $user_id = $this->input->get('user');
        if(!is_numeric($user_id))
            exit('ERROR');
        $pid = $this->session->userdata('current_user_id');
        if(!$this->MUser->isParent($pid, $user_id))
            exit('You are not the Superior of this user');
        $get_config = array(
            array(
                'field' => 'is_filter',
                'label' => 'Is Filter',
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' =>  'report_type',
                'label' =>  'Report Type',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' => 'bill_type',
                'label' => 'Bill Type',
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' =>  'date_from',
                'label' =>  'Date From',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_to',
                'label' =>  'Date To',
                'rules' =>  'trim|xss_clean|required'
            ),
        );
        $this->form_validation->set_rules($get_config);
        $report_type = $this->input->get('report_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != ''
        )
        {
            $date_from = date('Y-m-d', $date_from);
            $date_to = date('Y-m-d', $date_to);
            //$search = $this->db->escape_like_str($search);
            $data = array();
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."report/listpage_sub/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            //$where = '';
            //$where .= ' and p.is_valid = true ';
            //$where .= $this->__get_search_str($search, $price_low, $price_high);
            //$config['total_rows'] = $this->MProduct->intGetProductsCount($where);

            $config['per_page'] = 30;
            switch($report_type)
            {
                case "day":
                    $config['total_rows'] = $interval->days + 1;
                    //if($this->input->get('is_filter') == 'on')
                    //$config['total_rows'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $limit);
                    break;
                case "month":
                    $config['total_rows'] = $interval->y*12 + $interval->m + 1;
                    //if($this->input->get('is_filter') == 'on')
                    //$config['total_rows'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $limit);
                    break;
                case "year":
                    $config['total_rows'] = $interval->y + 1;;
                    break;
                default:
                    $report_type = "";
                    break;
            }
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            switch($report_type)
            {
                case 'day':
                    if($this->input->get('is_filter') == 'on') {
                        $data['bills'] = $this->MBill->objGetBillsOfDayWithFilter($date_from, $date_to, $user_id, $limit);
                        $config['total_rows'] = $this->MBill->objGetBillsOfDayWithFilterCount($date_from, $date_to, $user_id);
                        $this->pagination->initialize($config);
                        $data['page'] = $this->pagination->create_links();
                    }else
                        $data['bills'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $user_id, $limit);
                    $this->load->view('templates/header_user', $data);
                    $this->load->view('report/listpage_day', $data);
                    break;
                case 'month':
                    if($this->input->get('is_filter') == 'on') {
                        $data['bills'] = $this->MBill->objGetBillsOfMonthWithFilter($date_from, $date_to, $user_id, $limit);
                        $config['total_rows'] = $this->MBill->objGetBillsOfMonthWithFilterCount($date_from, $date_to, $user_id);
                        $this->pagination->initialize($config);
                        $data['page'] = $this->pagination->create_links();
                    } else
                        $data['bills'] = $this->MBill->objGetBillsOfMonth($date_from, $date_to, $user_id, $limit);
                    $this->load->view('templates/header_user', $data);
                    $this->load->view('report/listpage_month', $data);
                    break;
                case 'year':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfYearWithFilter($date_from, $date_to, $user_id);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfYear($date_from, $date_to, $user_id);
                    $this->load->view('templates/header_user', $data);
                    $this->load->view('report/listpage_year', $data);
                    break;
                default:
                    break;
            }
            //$this->load->view('templates/header', $data);
            //$this->load->view('report/listpage', $data);
        }else{
            $this->session->set_flashdata('flashdata', '参数错误');
            redirect('report/index');
        }
    }

    public function listpage_user($offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $user_id = $this->input->get('user');
        if(!is_numeric($user_id))
            exit('ERROR');
        $get_config = array(
            array(
                'field' => 'is_filter',
                'label' => 'Is Filter',
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' =>  'report_type',
                'label' =>  'Report Type',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_from',
                'label' =>  'Date From',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_to',
                'label' =>  'Date To',
                'rules' =>  'trim|xss_clean|required'
            ),
        );
        $this->form_validation->set_rules($get_config);
        $report_type = $this->input->get('report_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != ''
        )
        {
            $date_from = date('Y-m-d', $date_from);
            $date_to = date('Y-m-d', $date_to);
            //$search = $this->db->escape_like_str($search);
            $data = array();
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."report/listpage_user/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            //$where = '';
            //$where .= ' and p.is_valid = true ';
            //$where .= $this->__get_search_str($search, $price_low, $price_high);
            //$config['total_rows'] = $this->MProduct->intGetProductsCount($where);

            $config['per_page'] = 30;
            switch($report_type)
            {
                case "day":
                    $config['total_rows'] = $interval->days + 1;
                    //if($this->input->get('is_filter') == 'on')
                    //$config['total_rows'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $limit);
                    break;
                case "month":
                    $config['total_rows'] = $interval->y*12 + $interval->m + 1;
                    //if($this->input->get('is_filter') == 'on')
                    //$config['total_rows'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $limit);
                    break;
                case "year":
                    $config['total_rows'] = $interval->y + 1;;
                    break;
                default:
                    $report_type = "";
                    break;
            }
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = " u.id = {$user_id} ";
            $order = '';
            switch($report_type)
            {
                case 'day':
                    if($this->input->get('is_filter') == 'on') {
                        $data['bills'] = $this->MBill->objGetBillsOfDayWithFilter($date_from, $date_to, $user_id, $limit);
                        $config['total_rows'] = $this->MBill->objGetBillsOfDayWithFilterCount($date_from, $date_to, $user_id);
                        $this->pagination->initialize($config);
                        $data['page'] = $this->pagination->create_links();
                    } else
                        $data['bills'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $user_id, $limit);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_day_users', $data);
                    break;
                case 'month':
                    if($this->input->get('is_filter') == 'on') {
                        $data['bills'] = $this->MBill->objGetBillsOfMonthWithFilter($date_from, $date_to, $user_id, $limit);
                        $config['total_rows'] = $this->MBill->objGetBillsOfMonthWithFilterCount($date_from, $date_to, $user_id);
                        $this->pagination->initialize($config);
                        $data['page'] = $this->pagination->create_links();
                    } else
                        $data['bills'] = $this->MBill->objGetBillsOfMonth($date_from, $date_to, $user_id, $limit);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_month_users', $data);
                    break;
                case 'year':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfYearWithFilter($date_from, $date_to, $user_id);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfYear($date_from, $date_to, $user_id);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_year_users', $data);
                    break;

                default:
                    break;
            }
            //$this->load->view('templates/header', $data);
            //$this->load->view('report/listpage', $data);
        }else{
            $this->session->set_flashdata('flashdata', '参数错误');
            redirect('report/index_admin');
        }
    }

    public function listpage($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $user_id = $this->session->userdata('current_user_id', true);
        $get_config = array(
            array(
                'field' => 'is_filter',
                'label' => 'Is Filter',
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' =>  'report_type',
                'label' =>  'Report Type',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_from',
                'label' =>  'Date From',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_to',
                'label' =>  'Date To',
                'rules' =>  'trim|xss_clean|required'
            ),
        );
        $this->form_validation->set_rules($get_config);
        $report_type = $this->input->get('report_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != ''
        )
        {
            $date_from = date('Y-m-d', $date_from);
            $date_to = date('Y-m-d', $date_to);
            //$search = $this->db->escape_like_str($search);
            $data = array();
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."report/listpage/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);

            $config['per_page'] = 30;
            switch($report_type)
            {
                case "day":
                    $config['total_rows'] = $interval->days + 1;
                    break;
                case "month":
                    $config['total_rows'] = $interval->y*12 + $interval->m + 1;
                    break;
                case "year":
                    $config['total_rows'] = $interval->y + 1;;
                    break;
                case "products":
                    $config['total_rows'] = $this->MBill->objGetProductBillsItemCount($date_from, $date_to);
                    break;
                default:
                    $report_type = "";
                    break;
            }
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            switch($report_type)
            {
                case 'day':
                    if($this->input->get('is_filter') == 'on') {
                        $data['bills'] = $this->MBill->objGetBillsOfDayWithFilter($date_from, $date_to, $user_id, $limit);
                        $config['total_rows'] = $this->MBill->objGetBillsOfDayWithFilterCount($date_from, $date_to, $user_id);
                        $this->pagination->initialize($config);
                        $data['page'] = $this->pagination->create_links();
                    }else
                        $data['bills'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $user_id, $limit);
                    $this->load->view('templates/header_user', $data);
                    $this->load->view('report/listpage_day', $data);
                    break;
                case 'month':
                    if($this->input->get('is_filter') == 'on') {
                        $data['bills'] = $this->MBill->objGetBillsOfMonthWithFilter($date_from, $date_to, $user_id, $limit);
                        $config['total_rows'] = $this->MBill->objGetBillsOfMonthWithFilterCount($date_from, $date_to, $user_id);
                        $this->pagination->initialize($config);
                        $data['page'] = $this->pagination->create_links();
                    } else
                        $data['bills'] = $this->MBill->objGetBillsOfMonth($date_from, $date_to, $user_id, $limit);
                    $this->load->view('templates/header_user', $data);
                    $this->load->view('report/listpage_month', $data);
                    break;
                case 'year':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfYearWithFilter($date_from, $date_to, $user_id);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfYear($date_from, $date_to, $user_id);
                    $this->load->view('templates/header_user', $data);
                    $this->load->view('report/listpage_year', $data);
                    break;

                default:
                    break;
            }
            //$this->load->view('templates/header', $data);
            //$this->load->view('report/listpage', $data);
        }else{
            $this->session->set_flashdata('flashdata', '参数错误');
            redirect('report/index');
        }
    }

    public function listpage_admin($offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        //$current_user_id = $this->session->flashdata('current_user_id', true);
        $get_config = array(
            array(
                'field' =>  'report_type',
                'label' =>  'Report Type',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_from',
                'label' =>  'Date From',
                'rules' =>  'trim|xss_clean|required'
            ),
            array(
                'field' =>  'date_to',
                'label' =>  'Date To',
                'rules' =>  'trim|xss_clean|required'
            ),
        );
        $this->form_validation->set_rules($get_config);
        $report_type = $this->input->get('report_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != ''
        )
        {
            $date_from = date('Y-m-d', $date_from);
            $date_to = date('Y-m-d', $date_to);
            //$search = $this->db->escape_like_str($search);
            $data = array();
            $data['report_type'] = $report_type;
            $data['date_from'] = $date_from;
            $data['date_to'] = $date_to;
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."report/listpage_admin/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            //$where = '';
            //$where .= ' and p.is_valid = true ';
            //$where .= $this->__get_search_str($search, $price_low, $price_high);
            //$config['total_rows'] = $this->MProduct->intGetProductsCount($where);

            $config['per_page'] = 30;
            switch($report_type)
            {
                case "day":
                    $config['total_rows'] = $interval->days + 1;
                    //if($this->input->get('is_filter') == 'on')
                    //$config['total_rows'] = $this->MBill->objGetZentsBillsOfDay($date_from, $date_to, $limit)->count;
                    break;
                case "month":
                    $config['total_rows'] = $interval->y*12 + $interval->m + 1;
                    break;
                case "year":
                    $config['total_rows'] = $interval->y + 1;;
                    break;
                case "products":
                    $config['per_page'] = 9999;
                    $config['total_rows'] = 9999;
                    break;
                case "users":
                    $config['total_rows'] = $this->MBill->intGetUserBillsCount($date_from, $date_to);
                    break;
                default:
                    $report_type = "";
                    break;
            }
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            switch($report_type)
            {
                case 'day':
                    $data['bills'] = $this->MBill->objGetZentsBillsOfDay($date_from, $date_to, $limit);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_day_admin', $data);
                    break;
                case 'month':
                    $data['bills'] = $this->MBill->objGetZentsBillsOfMonth($date_from, $date_to, $limit);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_month_admin', $data);
                    break;
                case 'year':
                    $data['bills'] = $this->MBill->objGetZentsBillsOfYear($date_from, $date_to);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_year_admin', $data);
                    break;
                case 'products':
                    $data['bills'] = $this->MBill->objGetProductBills($date_from, $date_to);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_productbills', $data);
                    break;
                case 'users':
                    $data['bills'] = $this->MBill->objGetUserBills($date_from, $date_to, $limit);
                    $this->load->view('templates/header', $data);
                    $this->load->view('report/listpage_userbills', $data);
                    break;
                default:
                    break;
            }
            //$this->load->view('templates/header', $data);
            //$this->load->view('report/listpage', $data);
        }else{
            $this->session->set_flashdata('flashdata', '参数错误');
            redirect('report/index_zents');
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


    public function download_xls()
    {
        $report_type = $this->input->post('report_type');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        if($report_type != '' &&
            $date_from != '' && $date_to != ''
        ) {
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $title = "untitled";
            switch($report_type) {
                case 'day':
                    $title = "ZENTSERP 日报表 $date_from - $date_to";
                    $bills = $this->MBill->objGetZentsBillsOfDay($date_from, $date_to);
                    break;
                case 'month':
                    $bills = $this->MBill->objGetZentsBillsOfMonth($date_from, $date_to);
                    $date_from = date('Y-m', strtotime($date_from));
                    $date_to = date('Y-m', strtotime($date_to));
                    $title = "ZENTSERP 月报表 $date_from - $date_to";
                    break;
                case 'year':
                    $bills = $this->MBill->objGetZentsBillsOfYear($date_from, $date_to);
                    $date_from = date('Y', strtotime($date_from));
                    $date_to = date('Y', strtotime($date_to));
                    $title = "ZENTSERP 年报表 $date_from - $date_to";
                    break;
                case 'products':
                    $bills = $this->MBill->objGetProductBills($date_from, $date_to);
                    $title = "ZENTSERP 产品报表 $date_from - $date_to";
                    break;
                case 'users':
                    $bills = $this->MBill->objGetUserBills($date_from, $date_to);
                    $title = "ZENTSERP 代理交易统计报表 $date_from - $date_to";
                    break;
                default:
                    break;
            }
            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Princelo Lamkimcheung@gmail.com")
                ->setLastModifiedBy("Princelo Lamkimcheung@gmail.com")
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription($title)
                ->setKeywords("Princelo lamkimcheung@gmail.com")
                ->setCategory($report_type);


            if($report_type == 'products') {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $title)
                    ->setCellValue('A2', '产品ID')
                    ->setCellValue('B2', '产品名称')
                    ->setCellValue('C2', '总出货量')
                    ->setCellValue('D2', '零售出货量')
                    ->setCellValue('E2', '经销出货量')
                    ->setCellValue('F2', '市代出货量')
                    ->setCellValue('E2', '总代出货量')
                    ->setCellValue('H2', '试用品出货量')
                    ->setCellValue('I2', '涉总金额')
                    ->setCellValue('J2', '涉零售金额')
                    ->setCellValue('K2', '涉经销金额')
                    ->setCellValue('L2', '涉市代金额')
                    ->setCellValue('M2', '涉总代金额')
                    ->setCellValue('N2', '涉试用品金额');
                foreach ($bills as $k => $v) {
                    $i = $k + 3;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("A$i", $v->product_id)
                        ->setCellValue("B$i", $v->title)
                        ->setCellValue("C$i", $v->total_quantity)
                        ->setCellValue("D$i", $v->quantity_0)
                        ->setCellValue("E$i", $v->quantity_3)
                        ->setCellValue("F$i", $v->quantity_2)
                        ->setCellValue("G$i", $v->quantity_1)
                        ->setCellValue("H$i", $v->quantity_t)
                        ->setCellValue("I$i", $v->amount)
                        ->setCellValue("J$i", $v->amount_0)
                        ->setCellValue("K$i", $v->amount_3)
                        ->setCellValue("L$i", $v->amount_2)
                        ->setCellValue("M$i", $v->amount_1)
                        ->setCellValue("N$i", $v->amount_t);
                }
            } elseif ($report_type == 'users') {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $title)
                    ->setCellValue('A2', '代理')
                    ->setCellValue('B2', '自身业绩增量')
                    ->setCellValue('C2', '下级业绩增量')
                    ->setCellValue('D2', '实际业绩增量')
                    ->setCellValue('E2', '收益增量(不含推荐)')
                    ->setCellValue('F2', '推荐收益增量')
                    ->setCellValue('E2', '总收益增量')
                    ->setCellValue('H2', '至上级收益')
                    ->setCellValue('I2', '至上级推荐收益')
                    ->setCellValue('J2', '至上级总收益')
                    ->setCellValue('K2', '上级代理');
                foreach ($bills as $k => $v) {
                    $i = $k + 3;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("A$i", $v->name."(".$v->username."/".$v->id.")")
                        ->setCellValue("B$i", cny($v->self_turnover))
                        ->setCellValue("C$i", cny($v->sub_turnover))
                        ->setCellValue("D$i", "￥".bcadd(money($v->self_turnover), money($v->sub_turnover), 2))
                        ->setCellValue("E$i", cny($v->normal_return_profit_sub2self))
                        ->setCellValue("F$i", cny($v->extra_return_profit_sub2self))
                        ->setCellValue("G$i", "￥".bcadd(money($v->normal_return_profit_sub2self), money($v->extra_return_profit_sub2self), 2))
                        ->setCellValue("H$i", cny($v->normal_return_profit_self2parent))
                        ->setCellValue("I$i", cny($v->extra_return_profit_self2parent))
                        ->setCellValue("J$i", "￥".bcadd(money($v->normal_return_profit_self2parent), money($v->extra_return_profit_self2parent), 2));
                    if($v->pid != '1')
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue("K$i", $v->pname."(".$v->pusername."/".$v->pid.")");
                    else
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue("K$i", "无上级");

                }
            } else {
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $title)
                    ->setCellValue('A2', '日期')
                    ->setCellValue('B2', '总金额(含运费)')
                    ->setCellValue('C2', '产品总金额')
                    ->setCellValue('D2', '运费总金额')
                    ->setCellValue('E2', '成本金额')
                    ->setCellValue('F2', '回扣总量(含推荐)')
                    ->setCellValue('E2', '回扣总量(不含推荐)')
                    ->setCellValue('H2', '回扣(经总差价)')
                    ->setCellValue('I2', '回扣(经市差价)')
                    ->setCellValue('J2', '回扣(市总差价)')
                    ->setCellValue('K2', '推荐回扣')
                    ->setCellValue('L2', '订单数');
                // Miscellaneous glyphs, UTF-8
                foreach($bills as $k => $v)
                {
                    $i = $k + 3;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("A$i", $v->date)
                        ->setCellValue("B$i", $v->total_volume)
                        ->setCellValue("C$i", $v->products_volume)
                        ->setCellValue("D$i", $v->post_fee)
                        ->setCellValue("E$i", $v->products_cost)
                        ->setCellValue("F$i", $v->return_profit_volume)
                        ->setCellValue("G$i", $v->normal_return_profit_volume)
                        ->setCellValue("H$i", $v->return_profit_3_1)
                        ->setCellValue("I$i", $v->return_profit_3_2)
                        ->setCellValue("J$i", $v->return_profit_2_1)
                        ->setCellValue("K$i", $v->extra_return_profit_volume)
                        ->setCellValue("L$i", $v->order_quantity);
                }
            }

            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('REPORT');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$title.'.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else {
            exit('This page is expired !');
        }
    }
}

