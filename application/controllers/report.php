<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class Report extends MY_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'user')
            redirect('error404');
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
            $config['base_url'] = base_url()."report/listuser_admin/";
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
            $config['base_url'] = base_url()."report/listuser_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
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
        $bill_type = $this->input->get('bill_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        if($bill_type!='income'&&$bill_type!='payout')
        {
            $bill_type = '';
        }
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != '' && $bill_type != ''
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
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            if($bill_type == 'income')
            {
                $where = " and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                         and b.type = 2 and u.id = {$user_id}";
                $type = 2;
            }
            if($bill_type == 'payout')
            {
                $where = " and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                         and b.type = 1 and u.id = {$user_id}";
                $type = 1;
            }
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
            switch($report_type)
            {
                case 'day':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfDayWithFilter($date_from, $date_to, $user_id, $limit, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $user_id, $limit, $type);
                    $this->load->view('templates/header_user', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_day_income', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_day_payout', $data);
                    break;
                case 'month':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfMonthWithFilter($date_from, $date_to, $user_id, $limit, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfMonth($date_from, $date_to, $user_id, $limit, $type);
                    $this->load->view('templates/header_user', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_month_income', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_month_payout', $data);
                    break;
                case 'year':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfYearWithFilter($date_from, $date_to, $user_id, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfYear($date_from, $date_to, $user_id, $type);
                    $this->load->view('templates/header_user', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_year_income', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_year_payout', $data);
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
        $bill_type = $this->input->get('bill_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        if($bill_type!='income'&&$bill_type!='payout')
        {
            $bill_type = '';
        }
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != '' && $bill_type != ''
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
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            if($bill_type == 'income')
            {
                $where = " and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                         and b.type = 2 and u.id = {$user_id}";
                $type = 2;
            }
            if($bill_type == 'payout')
            {
                $where = " and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                         and b.type = 1 and u.id = {$user_id}";
                $type = 1;
            }
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
            switch($report_type)
            {
                case 'day':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfDayWithFilter($date_from, $date_to, $user_id, $limit, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $user_id, $limit, $type);
                    $this->load->view('templates/header', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_day_income_admin', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_day_payout_admin', $data);
                    break;
                case 'month':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfMonthWithFilter($date_from, $date_to, $user_id, $limit, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfMonth($date_from, $date_to, $user_id, $limit, $type);
                    $this->load->view('templates/header', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_month_income_admin', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_month_payout_admin', $data);
                    break;
                case 'year':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfYearWithFilter($date_from, $date_to, $user_id, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfYear($date_from, $date_to, $user_id, $type);
                    $this->load->view('templates/header', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_year_income_admin', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_year_payout_admin', $data);
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

    public function listpage($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        /*$data = array();
        $data['products'] = $this->MProduct->objGetProductList();
        $this->load->view('templates/header', $data);
        $this->load->view('product/listpage', $data);*/
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
        $bill_type = $this->input->get('bill_type', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true);
        if($bill_type!='income'&&$bill_type!='payout')
        {
            $bill_type = '';
        }
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        if($this->input->get('report_type', true) != '' &&
            $date_from != '' && $date_to != '' && $bill_type != ''
        )
        {
            $date_from = date('Y-m-d', $date_from);
            $date_to = date('Y-m-d', $date_to);
            //$search = $this->db->escape_like_str($search);
            $data = array();
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."report/listpage/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            //$where = '';
            //$where .= ' and p.is_valid = true ';
            //$where .= $this->__get_search_str($search, $price_low, $price_high);
            //$config['total_rows'] = $this->MProduct->intGetProductsCount($where);

            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            if($bill_type == 'income')
            {
                $where = " and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                         and b.type = 2 and u.id = {$user_id}";
                $type = 2;
            }
            if($bill_type == 'payout')
            {
                $where = " and o.finish_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59'
                         and b.type = 1 and u.id = {$user_id}";
                $type = 1;
            }
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
            switch($report_type)
            {
                case 'day':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfDayWithFilter($date_from, $date_to, $user_id, $limit, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfDay($date_from, $date_to, $user_id, $limit, $type);
                    $this->load->view('templates/header_user', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_day_income', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_day_payout', $data);
                    break;
                case 'month':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfMonthWithFilter($date_from, $date_to, $user_id, $limit, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfMonth($date_from, $date_to, $user_id, $limit, $type);
                    $this->load->view('templates/header_user', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_month_income', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_month_payout', $data);
                    break;
                case 'year':
                    if($this->input->get('is_filter') == 'on')
                        $data['bills'] = $this->MBill->objGetBillsOfYearWithFilter($date_from, $date_to, $user_id, $type);
                    else
                        $data['bills'] = $this->MBill->objGetBillsOfYear($date_from, $date_to, $user_id, $type);
                    $this->load->view('templates/header_user', $data);
                    if($bill_type == 'income')
                        $this->load->view('report/listpage_year_income', $data);
                    if($bill_type == 'payout')
                        $this->load->view('report/listpage_year_payout', $data);
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
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."report/listpage_admin/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            //$where = '';
            //$where .= ' and p.is_valid = true ';
            //$where .= $this->__get_search_str($search, $price_low, $price_high);
            //$config['total_rows'] = $this->MProduct->intGetProductsCount($where);

            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
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
                default:
                    $report_type = "";
                    break;
            }
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

    public function add($error = '')
    {
        /*if(!isset($_SESSION['admin'])){
            redirect('login', 'refresh');
        }*/
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'title',
                'label'   => '产品名称',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'price_special',
                'label'   => '特约代理价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field'   => 'price_last_2',
                'label'   => '一级代理价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field'   => 'price_last_3',
                'label'   => '二级代理价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field'  => 'price_normal',
                'label'  => '零售价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('product/add', $data);
            }else{
                $config['upload_path'] = './uploads/';
                $config['file_name'] = uniqid();
                $config['allowed_types'] = 'jpg';
                $config['max_size']	= '500000';

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('img'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    echo $error['error'];
                    return false;
                }
                else
                {
                    $upload_data = array('upload_data' => $this->upload->data());
                    //$data['avatardir'] = $upload_data['upload_data']['full_path'];
                    $path = $upload_data['upload_data']['file_path'];
                    $fname = $upload_data['upload_data']['file_name'];
                    $this->createThumbs($path, $fname, 100);
                }
                $price_list = array(
                    0 => $this->input->post('price_normal'),
                    1 => $this->input->post('price_special'),
                    2 => $this->input->post('price_last_2'),
                    3 => $this->input->post('price_last_3'),
                );
                $main_data = array(
                    'title' => $this->input->post('title'),
                    'properties' => $this->input->post('properties'),
                    'feature' => $this->input->post('feature'),
                    'usage_method' => $this->input->post('usage_method'),
                    'ingredient' => $this->input->post('ingredient'),
                    //'img' => $this->input->post('img'),
                    'img' => $fname,
                    'is_valid' => $this->input->post('is_valid'),
                );
                $result = $this->MProduct->add($main_data, $price_list);
                if($result){
                    $this->session->set_flashdata('flashdata', '产品添加成功');
                    redirect('product/add');
                }
                else{
                    $this->session->set_flashdata('flashdata', '产品添加失败');
                    redirect('product/add');
                }
            }
        }else{
            $this->load->view('templates/header', $data);
            $this->load->view('product/add', $data);
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

    function createThumbs( $path, $fname, $thumbHeight )
    {
        $info = pathinfo($path . $fname);
        // continue only if this is a JPEG image
        if ( strtolower($info['extension']) == 'jpg' ||  strtolower($info['extension']) == 'jpeg' )
        {
            //echo "Creating thumbnail for {$fname} <br />";

            // load image and get image size
            $img = imagecreatefromjpeg( "{$path}{$fname}" );
            $width = imagesx( $img );
            $height = imagesy( $img );

            // calculate thumbnail size
            //$new_width = $thumbWidth;
            //$new_height = floor( $height * ( $thumbWidth / $width ) );
            $new_height = $thumbHeight;
            $new_width = floor($width * ($thumbHeight / $height));

            // create a new temporary image
            $tmp_img = imagecreatetruecolor( $new_width, $new_height );

            // copy and resize old image into new image
            imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

            // save thumbnail into a file
            //imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
            $nname = substr($fname, 0, strpos($fname, ".")) . "_thumb.png";
            imagepng( $tmp_img, "{$path}{$nname}" );
        }
    }
}

