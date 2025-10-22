<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Deliveries_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_expeditions()
    {
        $e = db_prefix() . '_ekspedisi';
        $t = db_prefix() . '_tarif_ekspedisi';

        $this->db->select(
            "$e.id as ekspedisi_id, $e.kode_ekspedisi, $e.nama_ekspedisi, $t.min_berat_kg, $t.harga_per_kg"
        );
        $this->db->from($e);
        $this->db->join($t, "$t.ekspedisi_id = $e.id", 'left');

        $query = $this->db->get();
        // echo('<pre>');
        // var_dump($query->result_array());
        // die;

        return $query->result_array();
    }
}