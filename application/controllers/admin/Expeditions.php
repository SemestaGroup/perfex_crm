<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expeditions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('expedition_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('expeditions');
        }

        $data['title'] = _l('expeditions_list');
        $this->load->view('admin/expeditions/manage', $data);
    }

    public function expedition($id = '')
    {
        if ($this->input->post()) {
                $message = '';
                $data    = $this->input->post();

                // Determine whether this is an update or an add.
                // The form when editing sets the form action to include the ID in the URL
                // (see edit_expedition JS). Also support a posted 'id' if present.
                $is_update = false;
                $record_id = null;

                if (!empty($id)) {
                    $is_update = true;
                    $record_id = $id;
                }

                if ($this->input->post('id')) {
                    $is_update = true;
                    $record_id = $this->input->post('id');
                }

                if (!$is_update) {
                    // Create
                    $insert_id = $this->expedition_model->add($data);
                    if ($insert_id) {
                        $message = _l('added_successfully', _l('expedition'));
                        set_alert('success', $message);
                        redirect(admin_url('expeditions'));
                    } else {
                        // Insert failed (could be duplicate unique key or DB error)
                        set_alert('warning', _l('problem_adding', _l('expedition')));
                        redirect(admin_url('expeditions'));
                    }
                } else {
                    // Update
                    // Prefer record_id determined above; ensure it's set
                    if ($record_id) {
                        // Remove posted id from data if present
                        if (isset($data['id'])) {
                            unset($data['id']);
                        }

                        $success = $this->expedition_model->update($data, $record_id);
                        if ($success) {
                            $message = _l('updated_successfully', _l('expedition'));
                            set_alert('success', $message);
                            redirect(admin_url('expeditions'));
                        } else {
                            set_alert('warning', _l('problem_updating', _l('expedition')));
                            redirect(admin_url('expeditions'));
                        }
                    } else {
                        // No ID provided, cannot update
                        set_alert('warning', _l('problem_updating', _l('expedition')));
                        redirect(admin_url('expeditions'));
                    }
                }
            die;
        }

    }

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('expeditions'));
        }

        $response = $this->expedition_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('expedition')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expedition')));
        }
        redirect(admin_url('expeditions'));
    }

    public function test()
    {
        $this->load->model('expedition_model');
        $data['expeditions'] = $this->expedition_model->get_expeditions();
        echo('<pre>');
        var_dump($data['expeditions']);
    }


    // public function table($id = '')
    // {
    //     if (!has_permission('deliveries', '', 'view')) {
    //         ajax_access_denied();
    //     }

    //     $this->load->model('expedition_model');
    //     $data['expeditions'] = $this->expedition_model->get_expeditions();
    //     App_table::find('expeditions')->output([
    //         'id'   => $id,
    //         'data' => $data['expeditions'],
    //     ]);
    // }

    public function add_expedition()
    {

    }

    public function import()
    {

    }

}