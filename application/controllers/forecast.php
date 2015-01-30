<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forecast extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('MForecast', 'MForecast');
    }

    public function index()
    {
        $data = array();
        $data['forecasts'] = $this->MForecast->objGetForecastList();
        $this->load->view('templates/header', $data);
        $this->load->view('forecast/index', $data);
    }
}

