<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-bold tw-text-xl tw-mb-3">
                    <?= _l('cities_list'); ?>
                </h4>
                <div class="tw-mb-2">
                    <div class="_buttons sm:tw-space-x-1 rtl:sm:tw-space-x-reverse">
                        <a href="#" onclick="new_cities(); return false;"
                            class="btn btn-primary">
                            <i class="fa-regular fa-plus"></i>
                            <?= _l('add_city'); ?>
                        </a>
                        <a href="#" onclick="new_kecamatan(); return false;"
                            class="btn btn-primary">
                            <i class="fa-regular fa-plus"></i>
                            <?= _l('add_kecamatan'); ?>
                        </a>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body panel-table-full">
                                <?php render_datatable([
                                    _l('province'),
                                    _l('kabupaten'),
                                    _l('kecamatan'),
                                    _l('options'),
                                    ], 'cities'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="city_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('cities/city'), ['id' => 'city-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title hide"><?= _l('edit_city'); ?></span>
                    <span class="add-title hide"><?= _l('add_city'); ?></span>
                    <span class="add-title-kec hide"><?= _l('add_kecamatan'); ?></span>
                </h4>
            </div>
            <div class="edit modal-body hide">
                <div class="row">
                    <div class="col-md-12">
                        <label for="province_name" class="control-label"><?= _l('province_name'); ?></label>
                        <select id="province_name" name="province_name" onchange="get_kota()" class="selectpicker" data-live-search="true" data-width="100%" required>
                            <option value=""></option>
                            <?php foreach($provinces as $province){ ?>
                                <option value="<?= $province['id']; ?>"><?= $province['nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row tw-mt-4">
                    <div class="col-md-12" id="kota_row_select">
                        <label for="cities_name" class="control-label"><?= _l('cities_name'); ?></label>
                        <select id="cities_name" name="cities_name" class="selectpicker" data-live-search="true" data-width="100%">
                            <option value=""></option>
                            <?php foreach($cities as $city){ ?>
                                <option value="<?= $city['id']; ?>"><?= $city['nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-12" id="kota_row_input">
                        <div class="form-group">
                            <label for="cities_name_custom" class="control-label"><?= _l('cities_name'); ?></label>
                            <input type="text" id="cities_name_custom" name="cities_name_custom" class="form-control" />
                        </div>
                    </div>
                </div>

                <div class="row tw-mt-4">
                    <div class="col-md-12" id="kecamatan_row_select">
                        <label for="kecamatan_name" class="control-label"><?= _l('kecamatan_name'); ?></label>
                        <select id="kecamatan_name" name="kecamatan_name" class="selectpicker" data-live-search="true" data-width="100%">
                            <option value=""></option>
                            <?php ?>
                        </select>
                    </div>

                    <div class="col-md-12" id="kecamatan_row_input" style="display:none;">
                        <div class="form-group">
                            <label for="kecamatan_name_custom" class="control-label"><?= _l('kecamatan_name'); ?></label>
                            <input type="text" id="kecamatan_name_custom" name="kecamatan_name_custom" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<div class="modal fade" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="details_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="details_modal_label"><?= _l('details'); ?></h4>
            </div>
            <div class="modal-body">
                <p class="text-center"><?= _l('loading'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    Dropzone.autoDiscover = false;
    $(function() {
        initDataTable('.table-cities', window.location.href, [3], [3], undefined, [0, 'asc']);
        
        // Listener untuk mereset konten details_modal saat ditutup
        $('#details_modal').on('hidden.bs.modal', function () {
            // Reset judul
            $(this).find('.modal-title').text('<?= _l('details'); ?>');
            // Reset body ke loading state
            $(this).find('.modal-body').html('<p class="text-center"><?= _l('loading'); ?></p>');
        });

    });

    function show_details(invoker, id) {
        var $inv = $(invoker);
        
        // Ambil ID dan Nama dari data attribute
        var kecamatanId = $inv.data('kecamatan-id');
        var kecamatanNama = $inv.data('kecamatan-nama');
        var finalId = kecamatanId || id; 

        $('#details_modal').modal('show');
        
        // Atur loading state dan judul
        $('#details_modal .modal-body').html('<p class="text-center"><?= _l('loading'); ?></p>');
        $('#details_modal .modal-title').text('Detail Tarif Ekspedisi Asal: ' + (kecamatanNama || '...')); 

        if (!finalId) {
            // Jika ID tidak ada, tampilkan pesan error
            $('#details_modal .modal-body').html('<p class="text-danger">ID Kecamatan tidak ditemukan. Pastikan atribut `data-kecamatan-id` diset pada link.</p>');
            return; 
        }

        $.ajax({
            url: admin_url + 'cities/get_expedition_rates/' + finalId,
            type: 'GET',
            dataType: 'json'
        }).done(function(response) {
            var modalBody = '<h5>Asal: ' + (kecamatanNama || 'N/A') + '</h5>';
            
            if (response.length > 0) {
                modalBody += '<table class="table table-striped table-bordered">';
                modalBody += '<thead><tr><th>Nama Ekspedisi</th><th>Tujuan Kecamatan</th><th>Min Berat (KG)</th><th>Harga/KG</th></tr></thead><tbody>';
                
                $.each(response, function(index, rate) {
                    modalBody += '<tr>';
                    modalBody += '<td>' + (rate.nama_ekspedisi || '-') + '</td>';
                    modalBody += '<td>' + (rate.tujuan_kecamatan_nama || '-') + '</td>';
                    modalBody += '<td>' + (rate.min_berat_kg || '0') + '</td>';
                    // buat format mata uang rupiah
                    modalBody += '<td>Rp ' + parseFloat(rate.harga_per_kg).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                    // modalBody += '<td>' + (typeof format_money === 'function' ? format_money(rate.harga_per_kg) : rate.harga_per_kg) + '</td>'; 
                    modalBody += '</tr>';
                });

                modalBody += '</tbody></table>';
            } else {
                modalBody += '<p class="text-warning">Tidak ada tarif ekspedisi yang ditemukan dari kecamatan ini.</p>';
            }

            $('#details_modal .modal-body').html(modalBody);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            // Log error di console untuk debugging
            console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
            $('#details_modal .modal-body').html('<p class="text-danger">Gagal memuat data tarif ekspedisi. (Lihat Console Log)</p>');
        });
    }

    function new_cities() {
        $('#city_modal').modal('show');
        setKotaMode(false);
        setKecamatanMode(false);
        $('.add-title').removeClass('hide');
        $('.add-title-kec').addClass('hide');
        $('.edit-title').addClass('hide');
        $('#city_modal select[name="province_name"]').val('').change();
        $('#cities_name_custom').val('');
    }

    function new_kecamatan() {
        $('#city_modal').modal('show');
        setKotaMode(true);
        setKecamatanMode(false);
        $('.add-title-kec').removeClass('hide');
        $('.add-title').addClass('hide');
        $('.edit-title').addClass('hide');
        $('#city_modal select[name="province_name"]').val('').change();
        $('#city-form')[0].reset();
    }

    function edit_cities(invoker, id) {
        var $inv = $(invoker);

        // helper to read multiple possible data attribute keys
        function readData(keys) {
            for (var i = 0; i < keys.length; i++) {
                var v = $inv.data(keys[i]);
                if (typeof v !== 'undefined' && v !== null && v !== '') return v;
            }
            return null;
        }

        var provId = readData(['province-id','provinsi-id','provinsi_id','province_id','provinsiId','provinsi-id']);
        var provName = readData(['province-name','provinsi-nama','provinsi_nama','province_name']);
        var cityId = readData(['city-id','kota-id','kota_id','city_id']);
        var cityName = readData(['city-name','kota-nama','kota_nama','city_name']);
        var kecId = readData(['kecamatan-id','kecamatan_id','kecamatan-id']);
        var kecName = readData(['kecamatan-name','kecamatan-nama','kecamatan_nama']);

    $('#city_modal').modal('show');
    // default to select mode; will switch to inputs if necessary
    setKotaMode(true);
    setKecamatanMode(true);
        $('.add-title').addClass('hide');
        $('.add-title-kec').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('.edit').removeClass('hide');
        
        // Reset form before setting values
        $('#city-form')[0].reset();
        
        // If we have a province id use it, otherwise try to match by name
        if (provId) {
            $('#province_name').selectpicker('val', provId).change();
        } else if (provName) {
            // try to find option with this text and set its value
            var provOpt = $('#province_name option').filter(function() { return $(this).text().trim() === provName.trim(); });
            if (provOpt.length) {
                $('#province_name').selectpicker('val', provOpt.val()).change();
            }
        }

        // Chain loading of kota then kecamatan so values exist before setting
        get_kota(provId || $('#province_name').val(), cityId || cityName, function() {
            // After kota populated and selected, load kecamatan
            var selectedCity = $('select[name="cities_name"]').length ? $('select[name="cities_name"]').selectpicker('val') : $('input[name="cities_name"]').val();
            get_kecamatan(selectedCity || cityId || cityName, kecId || kecName, function() {
                // Finally, if kecamatan is an input (no select) set its value
                var kecSelect = $('select[name="kecamatan_name"]');
                if (kecSelect.length === 0) {
                    // set input value using name if available
                    if (kecName) {
                        $('#kecamatan_name_custom').val(kecName);
                        setKecamatanMode(false);
                    }
                }
            });
        });
    }

    // Helper to toggle kota input/select mode and manage required/disabled attributes
    function setKotaMode(useSelect) {
        if (useSelect) {
            $('#kota_row_select').show();
            $('#kota_row_input').hide();
            $('#cities_name').prop('disabled', false).attr('required', true);
            $('#cities_name_custom').prop('disabled', true).removeAttr('required');
        } else {
            $('#kota_row_select').hide();
            $('#kota_row_input').show();
            $('#cities_name').prop('disabled', true).removeAttr('required');
            $('#cities_name_custom').prop('disabled', false).attr('required', true);
        }
        // refresh selectpicker visuals
        if (typeof $('.selectpicker').selectpicker === 'function') $('.selectpicker').selectpicker('refresh');
    }

    // Helper to toggle kecamatan input/select mode and manage required/disabled attributes
    function setKecamatanMode(useSelect) {
        if (useSelect) {
            $('#kecamatan_row_select').show();
            $('#kecamatan_row_input').hide();
            $('#kecamatan_name').prop('disabled', false).attr('required', true);
            $('#kecamatan_name_custom').prop('disabled', true).removeAttr('required');
        } else {
            $('#kecamatan_row_select').hide();
            $('#kecamatan_row_input').show();
            $('#kecamatan_name').prop('disabled', true).removeAttr('required');
            $('#kecamatan_name_custom').prop('disabled', false).attr('required', true);
        }
        if (typeof $('.selectpicker').selectpicker === 'function') $('.selectpicker').selectpicker('refresh');
    }

    // get_kota optionally accepts (province_id, selectedCityIdOrName, callback)
    function get_kota(province_id, selectedCity, callback) {
        province_id = typeof province_id !== 'undefined' && province_id !== null ? province_id : $('#province_name').val();
        if (!province_id) {
            // clear kota select
            var emptyKota = $('select[name="cities_name"]');
            emptyKota.empty().append('<option value=""></option>').selectpicker('refresh');
            // clear kecamatan select as well
            var kecSelect = $('select[name="kecamatan_name"]');
            if (kecSelect.length) {
                kecSelect.empty().append('<option value=""></option>').selectpicker('refresh');
            }
            if (typeof callback === 'function') callback();
            return;
        }
        $.ajax({
            url: admin_url + 'cities/get_city/' + province_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(response) {
            var kotaSelect = $('select[name="cities_name"]');
            kotaSelect.empty();
            kotaSelect.append('<option value=""></option>');
            $.each(response, function(index, kota) {
                kotaSelect.append('<option value="' + kota.id + '">' + kota.nama + '</option>');
            });
            kotaSelect.selectpicker('refresh');
            // ensure proper enabled/required state
            setKotaMode(true);

            if (selectedCity) {
                // selectedCity might be an id or a name; prefer id
                var matched = kotaSelect.find('option[value="' + selectedCity + '"]');
                if (matched.length) {
                    kotaSelect.selectpicker('val', selectedCity).change();
                } else {
                    // try matching by text
                    var byText = kotaSelect.find('option').filter(function() { return $(this).text().trim() === String(selectedCity).trim(); });
                    if (byText.length) {
                        kotaSelect.selectpicker('val', byText.val()).change();
                    }
                }
            }
            if (typeof callback === 'function') callback();
        }).fail(function() {
            if (typeof callback === 'function') callback();
        });
    }

    // get_kecamatan optionally accepts (kota_id, selectedKecIdOrName, callback)
    function get_kecamatan(kota_id, selectedKec, callback) {
        kota_id = typeof kota_id !== 'undefined' && kota_id !== null ? kota_id : $('#cities_name').val();
        if (!kota_id) {
            var kecSelect = $('select[name="kecamatan_name"]');
            if (kecSelect.length) {
                kecSelect.empty().append('<option value=""></option>').selectpicker('refresh');
            }
            if (typeof callback === 'function') callback();
            return;
        }
        $.ajax({
            url: admin_url + 'cities/get_kecamatan/' + kota_id,
            type: 'GET',
            dataType: 'json'
        }).done(function(response) {
            var kecamatanSelect = $('select[name="kecamatan_name"]');
            if (kecamatanSelect.length) {
                kecamatanSelect.empty();
                kecamatanSelect.append('<option value=""></option>');
                $.each(response, function(index, kecamatan) {
                    kecamatanSelect.append('<option value="' + kecamatan.id + '">' + kecamatan.nama + '</option>');
                });
                kecamatanSelect.selectpicker('refresh');

                // ensure kecamatan select is enabled
                setKecamatanMode(true);

                if (selectedKec) {
                    var matched = kecamatanSelect.find('option[value="' + selectedKec + '"]');
                    if (matched.length) {
                        kecamatanSelect.selectpicker('val', selectedKec).change();
                    } else {
                        var byText = kecamatanSelect.find('option').filter(function() { return $(this).text().trim() === String(selectedKec).trim(); });
                        if (byText.length) {
                            kecamatanSelect.selectpicker('val', byText.val()).change();
                        }
                    }
                }
            } else {
                // if there's no select (input only), set input value when we have selectedKec as name
                if (selectedKec) {
                    $('input[name="kecamatan_name"]').val(selectedKec);
                }
            }
            if (typeof callback === 'function') callback();
        }).fail(function() {
            if (typeof callback === 'function') callback();
        });
    }
</script>
</body>

</html>