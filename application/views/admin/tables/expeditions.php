<?php
 
defined('BASEPATH') or exit('No direct script access allowed');
 

$aColumns = [
    'id',
    'kode_ekspedisi',
    'nama_ekspedisi',
    'website',
    'active',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . '_ekspedisi';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);
$output  = $result['output'];
$rResult = $result['rResult'];

$i = 1;

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = $i++;

    for ($iCol = 1; $iCol < count($aColumns); $iCol++) {
        $_data = $aRow[$aColumns[$iCol]];
        if ($aColumns[$iCol] == 'nama_ekspedisi') {
            $_data = '<a href="#" class="tw-font-medium" onclick="edit_expedition(this,' . e($aRow['id']) . '); return false" data-kode-ekspedisi="' . e($aRow['kode_ekspedisi']) . '" data-nama-ekspedisi="' . e($aRow['nama_ekspedisi']) . '" data-website="' . e($aRow['website']) . '" data-active="' . e($aRow['active']) . '">' . e($_data) . '</a>';
        }
        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-2">';
    $options .= '<a href="' . admin_url('expeditions/expedition/' . $aRow['id']) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" ' . _attributes_to_string([
        'onclick' => 'edit_expedition(this,' . e($aRow['id']) . '); return false', 'data-kode-ekspedisi' => e($aRow['kode_ekspedisi']), 'data-nama-ekspedisi' => e($aRow['nama_ekspedisi']), 'data-website' => e($aRow['website']), 'data-active' => e($aRow['active']),
    ]) . '>
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>';
    $options .= '<a href="' . admin_url('expeditions/delete/' . $aRow['id']) . '"class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
        <i class="fa-regular fa-trash-can fa-lg"></i>
    </a>';
    $options .= '</div>';

    $row[] = $options;
    $output['aaData'][] = $row;
}