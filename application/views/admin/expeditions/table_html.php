<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$hasPermission = staff_can('edit', 'expeditions') || staff_can('edit', 'expeditions');
if ($withBulkActions === true && $hasPermission) { ?>
<a href="#" data-toggle="modal" data-target="#expeditions_bulk_actions" class="hide bulk-actions-btn table-btn"
  data-table=".table-expeditions">
  <?= _l('bulk_actions'); ?>
</a>
<?php } ?>
<?php
$table_data = [
    [
        'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="expeditions"><label></label></div>',
        'th_attrs' => ['class' => $withBulkActions === true && $hasPermission ? '' : 'not_visible'],
    ],
    _l('the_number_sign'),
    _l('expedition_code'),
    _l('expedition_name'),
    _l('weight_minimum'),
    _l('price_per_kg'),    
];

$custom_fields = get_custom_fields('_ekspedisi', ['show_on_table' => 1]);

foreach ($custom_fields as $field) {
    array_push($table_data, [
        'name'     => $field['name'],
        'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
    ]);
}

$table_data = hooks()->apply_filters('expeditions_table_columns', $table_data);
render_datatable($table_data, ($class ?? 'expeditions'), [], [
    'data-last-order-identifier' => 'expeditions',
    'data-default-order'         => get_table_last_order('expeditions'),
    'id'                         => $table_id ?? 'expeditions',
]);