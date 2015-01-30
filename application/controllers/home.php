<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct(){
        parent::__construct();
        echo 'haha';exit;
        //$this->load->model('MThx', 'MThx');
    }

    public function index()
    {
        $data = array();
        $data['flash'] = $this->MFlash->objGetFlashInfo();
        $data['playerlist'] = $this->MPlayer->objGetPlayers(" order by vote desc, sort desc limit 0, 8");
        //$data['thxlist'] = $this->MThx->objGetThx(" limit 0, 10 ");
        $data['current'] = "home";
        $this->load->view('templates/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('templates/footer');
    }
}

