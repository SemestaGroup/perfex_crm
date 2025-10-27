<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-bold tw-text-xl tw-mb-3">
                    <?= _l('expeditions_list'); ?>
                </h4>
                <div class="tw-mb-2">
                    <div class="_buttons sm:tw-space-x-1 rtl:sm:tw-space-x-reverse">
                        <a href="#" onclick="new_expedition(); return false;"
                            class="btn btn-primary">
                            <i class="fa-regular fa-plus"></i>
                            <?= _l('add_expedition'); ?>
                        </a>
                        <!-- <a href="<?= admin_url('expeditions/import'); ?>"
                            class="hidden-xs btn btn-default ">
                            <i class="fa-solid fa-upload tw-mr-1"></i>
                            <?= _l('import_expeditions'); ?>
                        </a> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body panel-table-full">
                                <?php render_datatable([
                                    _l('no'),
                                    _l('expedition_code'),
                                    _l('expedition_name'),
                                    _l('website'),
                                    _l('active'),
                                    _l('options'),
                                    ], 'expeditions'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="expedition_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('expeditions/expedition'), ['id' => 'expedition-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('edit_expedition'); ?></span>
                    <span class="add-title"><?= _l('add_expedition'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('kode_ekspedisi', 'expedition_code'); ?>
                        <?= render_input('nama_ekspedisi', 'expedition_name'); ?>
                        <?= render_input('website', 'website'); ?>
                        <?php
                        $active_options = [
                            ['id' => 1, 'name' => _l('active')],
                            ['id' => 0, 'name' => _l('inactive')],
                        ];
                        ?>
                        <?= render_select('active', $active_options, ['id', 'name'], 'active', 1); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>

</div>
<?php init_tail(); ?>
<script>
    Dropzone.autoDiscover = false;
    $(function() {
        initDataTable('.table-expeditions', window.location.href, [5], [5], undefined, [0, 'asc']);
    });

    function new_expedition() {
        $('#expedition_modal').modal('show');
        $('.add-title').removeClass('hide');
        $('.edit-title').addClass('hide');
        $('#expedition_modal input[name="kode_ekspedisi"]').val('');
        $('#expedition_modal input[name="nama_ekspedisi"]').val('');
        $('#expedition_modal input[name="website"]').val('');
        $('#expedition_modal select[name="active"]').val(1).change();
    }

    function edit_expedition(invoker, id) {
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('#expedition_modal').modal('show');
        $('#expedition_modal input[name="kode_ekspedisi"]').val($(invoker).data('kode-ekspedisi'));
        $('#expedition_modal input[name="nama_ekspedisi"]').val($(invoker).data('nama-ekspedisi'));
        $('#expedition_modal input[name="website"]').val($(invoker).data('website'));
        $('#expedition_modal select[name="active"]').val($(invoker).data('active')).change();
        $('#expedition-form').attr('action', '<?= admin_url('expeditions/expedition/'); ?>' + id);
    }
</script>
</body>

</html>