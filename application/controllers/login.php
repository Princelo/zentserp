<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    private $objDB;
    public function __construct(){
        parent::__construct();
        $this->objDB = $this->load->database('default', true);
        $this->load->model('MUser', 'MUser');
        $this->load->helper('captcha');
        //$this->load->library('pagination');
        $this->load->helper('string');
    }

    public function index($error = '')
    {
        if($this->session->userdata('user') != ""){
            redirect('forecast/index');
        }else{
            $this->session->sess_destroy();
        }
        $vals = array(
            //'word' => random_string('alnum', 5),
            'word' => random_string('numeric', 5),
            'img_path' => './captcha/',
            'img_url' => base_url().'captcha/',
            'img_widget' => '130',
            'img_height' => '44',
        );

        $cap = create_captcha($vals);

        $data = array(
            'captcha_time' => $cap['time'],
            'ip_address' => $this->input->ip_address(),
            'word' => $cap['word']
        );

        $query = $this->objDB->insert_string('captcha', $data);
        $this->objDB->query($query);
        $data['captcha'] = $cap['image'];
        $data['error'] = $error;
        $this->load->view('login/index', $data);
    }

    public function check(){
        if(isset($_POST['captcha']) && isset($_POST['login_id']) && isset($_POST['password'])){
            $_POST['password'] = md5($_POST['password']);
            if($this->__validate_captcha() === true){
                if($this->MUser->boolVerify($_POST['login_id'], $_POST['password'])){
                    $this->session->set_userdata('user', $this->input->post('login_id'));
                    $this->session->set_userdata('role', $this->MUser->strGetRoleType($this->input->post('login_id')));
                    $this->session->set_userdata('current_user_id', $this->MUser->intGetCurrentUserId($this->input->post('login_id')));
                    redirect('forecast/index', 'refresh');
                }else{
                    $this->index('用戶或密码错误');
                }
            }else{
                $this->index('验证码错误');
            }
        }
    }

    private function __validate_captcha(){
        $expiration = time()-7200;
        $this->objDB->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);

        $sql = "SELECT COUNT(1) AS count FROM captcha WHERE lower(word) = ? AND ip_address = ? AND captcha_time > ?";
        $binds = array(strtolower($_POST['captcha']), $this->input->ip_address(), $expiration);
        $query = $this->objDB->query($sql, $binds);
        $row = $query->row();

        if ($row->count == 0)
        {
            return false;
        }else{
            return true;
        }
    }
}