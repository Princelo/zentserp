<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

    private $objDB;
    public function __construct(){
        parent::__construct();
        session_destroy();
        redirect('login');
    }

    /*public function logout(){
        $this->index();
    }*/
}
