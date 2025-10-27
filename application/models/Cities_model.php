<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cities_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $this->db->select(db_prefix() . '_provinsi.id as provinsi_id, ' . db_prefix() . '_provinsi.nama as provinsi_nama, ' . db_prefix() . '_kabupaten_kota.id as kota_id, ' . db_prefix() . '_kabupaten_kota.nama as kota_nama,' . db_prefix() . '_kecamatan.id as kecamatan_id, ' . db_prefix() . '_kecamatan.nama as kecamatan_nama');
        $this->db->from(db_prefix() . '_provinsi');
        $this->db->join(db_prefix() . '_kabupaten_kota', db_prefix() . '_kabupaten_kota.provinsi_id = ' . db_prefix() . '_provinsi.id', 'left');
        $this->db->join(db_prefix() . '_kecamatan', db_prefix() . '_kecamatan.kabupaten_kota_id = ' . db_prefix() . '_kabupaten_kota.id', 'left');

        $query = $this->db->get();

        return $query->result_array();
        echo '<pre>';
        var_dump($query->result_array());
    }

    /**
     * Return list of provinces for dropdowns
     *
     * @return array
     */
    public function get_provinces()
    {
        $this->db->select('id, nama');
        $this->db->from(db_prefix() . '_provinsi');
        $this->db->order_by('nama', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_cities($province_id)
    {
        $this->db->select('id, nama');
        $this->db->from(db_prefix() . '_kabupaten_kota');
        $this->db->where('provinsi_id', $province_id);
        $this->db->order_by('nama', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_kec($city_id)
    {
        $this->db->select('id, nama');
        $this->db->from(db_prefix() . '_kecamatan');
        $this->db->where('kabupaten_kota_id', $city_id);
        $this->db->order_by('nama', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_expedition_rates_by_kecamatan_id($kecamatan_id)
    {
        $this->db->select('
            T1.id,
            T1.ekspedisi_id,
            T1.asal_kecamatan_id,
            T1.tujuan_kecamatan_id,
            T1.harga_per_kg,
            T1.min_berat_kg,
            TK_ASAL.nama as asal_kecamatan_nama,
            TK_TUJUAN.nama as tujuan_kecamatan_nama,
            TE.nama_ekspedisi
        ');
        $this->db->from(db_prefix() . '_tarif_ekspedisi T1');
        
        // Join ke tbl_kecamatan untuk mendapatkan nama Kecamatan ASAL
        $this->db->join(db_prefix() . '_kecamatan TK_ASAL', 'TK_ASAL.id = T1.asal_kecamatan_id', 'left');
        
        // Join ke tbl_kecamatan untuk mendapatkan nama Kecamatan TUJUAN
        $this->db->join(db_prefix() . '_kecamatan TK_TUJUAN', 'TK_TUJUAN.id = T1.tujuan_kecamatan_id', 'left');

        // Join ke tbl_ekspedisi untuk mendapatkan nama Ekspedisi
        $this->db->join(db_prefix() . '_ekspedisi TE', 'TE.id = T1.ekspedisi_id', 'left');

        $this->db->where('T1.asal_kecamatan_id', $kecamatan_id);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add_city($data)
    {
        // Cek apakah 'nama' dan 'provinsi_id' ada di $data
        if (empty($data['nama']) || empty($data['provinsi_id'])) {
            return false;
        }
        
        // Membersihkan data sebelum insert
        $insert_data = [
            'nama' => $data['nama'],
            'provinsi_id' => $data['provinsi_id'],
            // Tambahkan field lain jika ada (e.g., kode_pos, dll.)
        ];

        // Lakukan insert ke tabel kabupaten_kota
        $this->db->insert(db_prefix() . '_kabupaten_kota', $insert_data);
        $insert_id = $this->db->insert_id();

        // Opsional: tambahkan logging
        if ($insert_id) {
            log_activity('New City Added [ID: ' . $insert_id . ', Nama: ' . $insert_data['nama'] . ']');
        }

        return $insert_id;
    }

    public function add_kecamatan($data)
    {
        // Cek apakah 'nama' dan 'kabupaten_kota_id' ada di $data
        if (empty($data['nama']) || empty($data['kabupaten_kota_id'])) {
            return false;
        }

        // Membersihkan data sebelum insert
        $insert_data = [
            'nama' => $data['nama'],
            'kabupaten_kota_id' => $data['kabupaten_kota_id'],
            // Tambahkan field lain jika ada
        ];
        
        // Lakukan insert ke tabel kecamatan
        $this->db->insert(db_prefix() . '_kecamatan', $insert_data);
        $insert_id = $this->db->insert_id();

        // Opsional: tambahkan logging
        if ($insert_id) {
            log_activity('New Kecamatan Added [ID: ' . $insert_id . ', Nama: ' . $insert_data['nama'] . ']');
        }

        return $insert_id;
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
        $data = hooks()->apply_filters('before_cities_updated', $data, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_kabupaten_kota', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_cities_updated', $id);
            log_activity('Cities Updated ['. $data['nama'] . ' ID: ' . $id . ']');
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