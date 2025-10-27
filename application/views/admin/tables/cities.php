<?php
 
defined('BASEPATH') or exit('No direct script access allowed');
 

$aColumns = [
    db_prefix() . '_provinsi.nama as provinsi_nama',
    db_prefix() . '_kabupaten_kota.nama as kota_nama',
    db_prefix() . '_kecamatan.nama as kecamatan_nama',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . '_provinsi';
$joins = [
    'LEFT JOIN ' . db_prefix() . '_kabupaten_kota ON ' . db_prefix() . '_kabupaten_kota.provinsi_id = ' . db_prefix() . '_provinsi.id',
    'LEFT JOIN ' . db_prefix() . '_kecamatan ON ' . db_prefix() . '_kecamatan.kabupaten_kota_id = ' . db_prefix() . '_kabupaten_kota.id',
];
$select = [
    db_prefix() . '_provinsi.nama as provinsi_nama',
    db_prefix() . '_kabupaten_kota.nama as kota_nama',
    db_prefix() . '_kecamatan.nama as kecamatan_nama',
    db_prefix() . '_kecamatan.id as kecamatan_id',
    db_prefix() . '_kabupaten_kota.id as kota_id',
    db_prefix() . '_provinsi.id as provinsi_id',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $joins, [], $select);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Iterate through the defined columns and use their alias (if any) to pull data
    for ($iCol = 0; $iCol < count($aColumns); $iCol++) {
        $col = $aColumns[$iCol];

        // Extract alias if column contains " as "
        $alias = $col;
        if (strpos($col, ' as ') !== false) {
            $alias = trim(substr($col, strpos($col, ' as ') + 4));
        }

        // Fallback if key missing
        $_data = isset($aRow[$alias]) ? $aRow[$alias] : '';

        // Make the kecamatan (subdistrict) name clickable to edit the row
        if ($alias === 'kecamatan_nama') {
            // Kita perlu mengambil ID dari kolom 'kecamatan_id' yang diasumsikan diambil di model
            $record_id = isset($aRow['kecamatan_id']) ? $aRow['kecamatan_id'] : '';
            
            // Ambil ID Provinsi dan Kota yang juga dibutuhkan untuk edit
            $provinsi_id = isset($aRow['provinsi_id']) ? $aRow['provinsi_id'] : '';
            $kota_id = isset($aRow['kota_id']) ? $aRow['kota_id'] : '';

            // --- BARIS YANG ANDA RUJUK DENGAN PERBAIKAN DATA ID ---
            $_data = '<a href="#" class="tw-font-medium" onclick="show_details(this,' . e($record_id) . '); return false" '
                . 'data-provinsi-nama="' . e($aRow['provinsi_nama'] ?? '') . '" '
                . 'data-kota-nama="' . e($aRow['kota_nama'] ?? '') . '" '
                . 'data-kecamatan-nama="' . e($aRow['kecamatan_nama'] ?? '') . '"'
                . 'data-kecamatan-id="' . e($record_id) . '">' . e($_data) . '</a>'; // <-- Simpan ID Kecamatan di sini
        }

        $row[] = $_data;
    }

    // Pastikan ID digunakan untuk options adalah ID Kecamatan
    $record_id = isset($aRow['kecamatan_id']) ? $aRow['kecamatan_id'] : '';

    // Pastikan ID Provinsi dan Kota diambil agar bisa digunakan saat Edit
    $provinsi_id = isset($aRow['provinsi_id']) ? $aRow['provinsi_id'] : '';
    $kota_id = isset($aRow['kota_id']) ? $aRow['kota_id'] : '';
    
    $options = '<div class="tw-flex tw-items-center tw-space-x-2">';
    $options .= '<a href="' . admin_url('cities/city/' . $record_id) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" ' . _attributes_to_string([
        'onclick' => 'edit_cities(this,' . e($record_id) . '); return false', 
        'data-provinsi-id' => e($provinsi_id), 
        'data-kota-id' => e($kota_id), 
        'data-kecamatan-id' => e($record_id),
    ]) . '>
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>';
    $options .= '<a href="' . admin_url('cities/delete/' . $record_id) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
        <i class="fa-regular fa-trash-can fa-lg"></i>
    </a>';
    $options .= '</div>';

    $row[] = $options;
    $output['aaData'][] = $row;
}