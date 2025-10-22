<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expeditions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('deliveries_model');
    }

    public function index($id='')
    {

        $this->list_expeditions($id);
    }

    public function list_expeditions($id='')
    {
        if (!has_permission('deliveries', '', 'view')) {
            access_denied('deliveries');
        }
        
        $this->load->model('deliveries_model');
        $data['expeditions'] = $this->deliveries_model->get_expeditions();
        $data['expedition_id'] = $id;
        $data['table'] = App_table::find('_ekspedisi');
        $data['title'] = _l('expeditions_list');
        
        $this->load->view('admin/expeditions/manage', $data);
    }


    public function table($id = '')
    {
        if (!has_permission('deliveries', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('deliveries_model');
        $data['expeditions'] = $this->deliveries_model->get_expeditions();
        App_table::find('_ekspedisi')->output([
            'id'   => $id,
            'data' => $data['expeditions'],
        ]);
    }

    public function add_expedition()
    {

    }

    public function import()
    {

    }

}