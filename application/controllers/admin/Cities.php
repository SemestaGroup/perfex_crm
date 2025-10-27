<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cities extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cities_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('cities');
        }

        $data['title'] = _l('cities_list');
        $this->load->model('cities_model');
        $data['provinces'] = $this->cities_model->get_provinces();
        $this->load->view('admin/cities/manage', $data);
    }
    
    public function test()
    {
        $this->cities_model->get();
    }

    public function get_city($id)
    {
        // Accept both AJAX and regular GET requests (some environments strip X-Requested-With header)
        $province_id = $id;
        if (empty($province_id)) {
            // No province selected - return empty list
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $this->load->model('cities_model');
        $cities = $this->cities_model->get_cities($province_id);

        $this->output->set_content_type('application/json')->set_output(json_encode($cities));
    }

    public function get_kecamatan($id)
    {
        $kabupaten_id = $id;
        if (empty($kabupaten_id)) {
            // No city selected - return empty list
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $this->load->model('cities_model');
        $kecamatan = $this->cities_model->get_kec($kabupaten_id);

        $this->output->set_content_type('application/json')->set_output(json_encode($kecamatan));
    }

    public function get_expedition_rates($id)
    {
        // $id di sini adalah ID Kecamatan ASAL
        $kecamatan_id = $id;
        if (empty($kecamatan_id) || !is_numeric($kecamatan_id)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $this->load->model('cities_model');
        $rates = $this->cities_model->get_expedition_rates_by_kecamatan_id($kecamatan_id);

        $this->output->set_content_type('application/json')->set_output(json_encode($rates));
    }

    public function city($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Tentukan operasi: UPDATE, ADD CITY, atau ADD KECAMATAN

            // 1. Logika Update
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

            if ($is_update) {
                // UPDATE (Asumsi: Update menggunakan ID yang dikirim melalui URL/form)
                if ($record_id) {
                    if (isset($data['id'])) {
                        unset($data['id']);
                    }

                    // Anda perlu menambahkan logika di sini untuk menentukan apakah yang di-update adalah
                    // Province, City, atau Kecamatan, dan memanggil fungsi model yang sesuai.
                    // Saat ini, model Anda hanya memiliki `update` yang menargetkan tbl_ekspedisi.
                    // Saya akan membiarkan pemanggilan `update` yang sudah ada, tapi ini HARUS disesuaikan 
                    // di Cities_model.php agar bisa meng-update tabel yang benar (kecamatan/kota).

                    // Hapus input yang tidak relevan untuk update agar tidak mengganggu model
                    $clean_data = $this->_clean_update_data($data);
                    
                    $success = $this->cities_model->update($clean_data, $record_id); // Saat ini memanggil update ekspedisi
                    
                    if ($success) {
                        set_alert('success', _l('updated_successfully', _l('cities')));
                    } else {
                        set_alert('warning', _l('problem_updating', _l('cities')));
                    }
                } else {
                    set_alert('warning', _l('problem_updating', _l('cities')));
                }
            } 
            // 2. Logika Add City/Kecamatan
            else { 
                // Logika Add City (Memeriksa input yang khas untuk Add City)
                if (isset($data['cities_name_input']) && !empty($data['cities_name_input'])) {
                    // ASUMSI: province_name adalah ID provinsi, cities_name_input adalah nama kota baru.
                    $insert_data = [
                        'provinsi_id' => $data['province_name_kota'],
                        'nama'        => $data['cities_name_custom_kota'],
                    ];
                    $insert_id = $this->cities_model->add_city($insert_data); // Fungsi ini HARUS DITAMBAHKAN di model
                    $message_key = 'Kota'; // Sesuaikan pesan
                    set_alert('success', _l('added_successfully', $message_key));
                    redirect(admin_url('cities'));
                }
                // Logika Add Kecamatan (Memeriksa input yang khas untuk Add Kecamatan)
                else if (isset($data['kecamatan_name_input']) && !empty($data['kecamatan_name_input'])) {
                    // ASUMSI: cities_name adalah ID kabupaten/kota, kecamatan_name_input adalah nama kecamatan baru.
                    $insert_data = [
                        'kabupaten_kota_id' => $data['cities_name'],
                        'nama'              => $data['kecamatan_name_input'],
                    ];
                    $insert_id = $this->cities_model->add_kecamatan($insert_data); // Fungsi ini HARUS DITAMBAHKAN di model
                    $message_key = 'Kecamatan'; // Sesuaikan pesan
                }
                else {
                    // Tidak ada input yang jelas
                    set_alert('warning', 'Tipe operasi tidak dikenali.');
                    redirect(admin_url('cities'));
                }
                
                // Menangani hasil Add City/Kecamatan
                if (isset($insert_id) && $insert_id) {
                    set_alert('success', _l('added_successfully', $message_key));
                } else if (isset($insert_id) && $insert_id === false) {
                     set_alert('danger', 'Gagal menambahkan ' . $message_key . '. Data mungkin sudah ada atau terjadi kesalahan database.');
                } else if (isset($insert_id)) {
                    set_alert('warning', _l('problem_adding', $message_key));
                }
            }

            redirect(admin_url('cities'));
        }
    }
    
    // Fungsi pembantu untuk membersihkan data update (sesuaikan dengan kebutuhan Anda)
    private function _clean_update_data($data) {
        $clean = [];
        // Logika sederhana: ambil hanya field yang berisi data untuk di-update, 
        // dengan asumsi form edit akan mengisi input yang relevan.
        
        if (!empty($data['province_name'])) {
            $clean['provinsi_id'] = $data['province_name'];
        }
        if (!empty($data['cities_name']) && !isset($data['cities_name_input'])) {
            $clean['kabupaten_kota_id'] = $data['cities_name'];
        }
        if (!empty($data['cities_name_input'])) {
            $clean['nama'] = $data['cities_name_input']; // Jika mengedit nama kota
        }
        if (!empty($data['kecamatan_name_input'])) {
            $clean['nama'] = $data['kecamatan_name_input']; // Jika mengedit nama kecamatan
        }

        // Catatan: Logika update ini sangat SENSITIF terhadap input form. 
        // Disarankan menggunakan hidden field ID yang berbeda (e.g., city_id, kecamatan_id)
        // di modal saat mode edit. Saat ini, saya hanya mengandalkan `nama` untuk menyimpan
        // nilai yang di-update (apakah kota atau kecamatan).
        
        // Kembalikan semua data untuk membiarkan model yang menanganinya, tapi hilangkan yang tidak perlu
        unset($data['province_name']);
        unset($data['cities_name_input']);
        unset($data['kecamatan_name_input']);
        
        return $data; // Mengembalikan data POST utuh (setelah menghapus id di atas)
    }

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('cities'));
        }

        $response = $this->cities_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('cities')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('cities')));
        }
        redirect(admin_url('cities'));
    }

}