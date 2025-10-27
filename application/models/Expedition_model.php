<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expedition_model extends App_Model
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

        return $query->result_array();
    }

    public function add($data)
    {
        $this->db->insert(db_prefix() . '_ekspedisi', $data);
        $insert_id = $this->db->insert_id();

        $data = hooks()->apply_filters('before_expedition_added', $data);
        if ($insert_id) {
            hooks()->do_action('after_expedition_added', $insert_id);
            log_activity('New Expedition Added ['. $data['kode_ekspedisi'] . 'ID: ' . $insert_id . ']');
        }
        return $insert_id;
    }

    public function update($data, $id)
    {
        $data = hooks()->apply_filters('before_expedition_updated', $data, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_ekspedisi', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_expedition_updated', $id);
            log_activity('Expedition Updated ['. $data['kode_ekspedisi'] . ' ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    public function delete($id)
    {
        hooks()->do_action('before_expedition_deleted', $id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . '_ekspedisi');

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_expedition_deleted', $id);
            log_activity('Expedition Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
}