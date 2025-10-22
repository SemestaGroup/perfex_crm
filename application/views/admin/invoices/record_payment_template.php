<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s">
    <div class="horizontal-scrollable-tabs">
        <ul class="nav nav-tabs no-margin" role="tablist">
            <li role="presentation" class="active">
                <a href="#record_payment"
                    aria-controls="record_payment"
                    role="tab"
                    data-toggle="tab">
                    <?= _l('record_payment'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#connect_payment"
                    aria-controls="connect_payment"
                    role="tab"
                    data-toggle="tab">
                    <?= _l('connect_payment'); ?>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- tab record payment -->
            <div role="tabpanel" class="tab-pane active" id="record_payment">
                <?= form_open(admin_url('invoices/record_payment'), ['id' => 'record_payment_form']); ?>
                        <?= form_hidden('invoiceid', $invoice->id); ?>
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <?= _l('record_payment_for_invoice'); ?>
                                <?= e(format_invoice_number($invoice->id)); ?>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $amount = $invoice->total_left_to_pay;
                                    $totalAllowed = 0;
                                    echo render_input('amount', 'record_payment_amount_received', $amount, 'number', ['max' => $amount]);
                                    ?>
                                    <?= render_date_input('date', 'record_payment_date', _d(date('Y-m-d'))); ?>
                                    <div class="form-group">
                                        <label for="paymentmode" class="control-label"><?= _l('payment_mode'); ?></label>
                                        <select class="selectpicker" name="paymentmode" data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($payment_modes as $mode) { ?>
                                                <?php if (is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
                                                    $totalAllowed++; ?>
                                                    <option value="<?= e($mode['id']); ?>">
                                                        <?= e($mode['name']); ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php if ($totalAllowed === 0) { ?>
                                        <div class="alert alert-info">
                                            Allowed payment modes not found for this invoice.<br />
                                            Click <a href="<?= admin_url('invoices/invoice/' . $invoice->id . '?allowed_payment_modes=1'); ?>" class="alert-link">here</a> to edit the invoice and allow payment modes.
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-6">
                                    <?= render_input('transactionid', 'payment_transaction_id', 'TX' . date('YmdHis') . $invoice->id, 'text', ['readonly' => 'readonly']); ?>
                                    <div class="form-group">
                                        <label for="note" class="control-label"><?= _l('record_payment_leave_note'); ?></label>
                                        <textarea name="note" class="form-control" rows="8" placeholder="<?= _l('invoice_record_payment_note_placeholder'); ?>" id="note"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 tw-mt-3">
                                    <?php
                                    $pr_template = is_email_template_active('invoice-payment-recorded');
                                    $sms_trigger = is_sms_trigger_active(SMS_TRIGGER_PAYMENT_RECORDED);
                                    if ($pr_template || $sms_trigger) { ?>
                                        <div class="checkbox checkbox-primary mtop15">
                                            <input type="checkbox" name="do_not_send_email_template" id="do_not_send_email_template">
                                            <label for="do_not_send_email_template">
                                                <?php
                                                if ($pr_template) {
                                                    echo _l('do_not_send_invoice_payment_email_template_contact');
                                                    if ($sms_trigger) {
                                                        echo '/';
                                                    }
                                                }
                                                if ($sms_trigger) {
                                                    echo 'SMS ' . _l('invoice_payment_recorded');
                                                }
                                                ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <div class="checkbox checkbox-primary mtop15 do_not_redirect hide">
                                        <input type="checkbox" name="do_not_redirect" id="do_not_redirect" checked>
                                        <label for="do_not_redirect"><?= _l('do_not_redirect_payment'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <?php
                            hooks()->do_action('after_admin_last_record_payment_form_field', $invoice);
                            if ($payments) { ?>
                                <div class="mtop25 inline-block full-width">
                                    <h5 class="bold">
                                        <?= _l('invoice_payments_received'); ?>
                                    </h5>
                                    <?php include_once APPPATH . 'views/admin/invoices/invoice_payments_table.php'; ?>
                                </div>
                            <?php } ?>
                            <?php hooks()->do_action('before_admin_add_payment_form_submit', $invoice); ?>
                        </div>
                        <div class="panel-footer text-right">
                            <a href="#" class="btn btn-danger" onclick="init_invoice(<?= e($invoice->id); ?>); return false;"><?= _l('cancel'); ?></a>
                            <button type="submit" autocomplete="off" data-loading-text="<?= _l('wait_text'); ?>" data-form="#record_payment_form" class="btn btn-success"><?= _l('submit'); ?></button>
                        </div>
                <?= form_close(); ?>
            </div>

            <!-- tab connect payment -->
            <div role="tabpanel" class="tab-pane" id="connect_payment">
                <?= form_open(admin_url('invoices/connect_payment'), ['id' => 'connect_payment_form']); ?>
                        <?= form_hidden('invoiceid', $invoice->id); ?>
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <?= _l('connect_payment'); ?>
                                <?= e(format_invoice_number($invoice->id)); ?>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?= render_date_input('date', 'record_payment_date', _d(date('Y-m-d'))); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= form_hidden('transactionid', 'TX' . date('YmdHis') . $invoice->id); ?>
                                        <label for="bank_account" class="control-label"><?= _l('acc_bank_account'); ?></label>
                                        <select class="selectpicker" id="select_bank_account" onchange="selectBankOnChange()" name="bank_account" data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($banks as $bank) { ?>
                                                <option value="<?= e($bank['id']); ?>">
                                                    <?php 
                                                        echo e($bank['name']);
                                                    ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>  
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="bank_account" class="control-label"><?= _l('transaction_details'); ?></label>
                                        <select class="selectpicker" id="select_transfer_details" onchange="amountOnChange()" name="transfer_details" data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($transfer_from as $transfer) { ?>
                                                <option value="<?= e($transfer['id']); ?>">
                                                    <?php 
                                                        if ($transfer['description'] != '') {
                                                            echo e($transfer['transfer_amount'] . ' - ' . $transfer['description']);
                                                        } else {
                                                            echo e($transfer['transfer_amount']);
                                                        }
                                                    ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <label for="note" class="control-label"><?= _l('record_payment_leave_note'); ?></label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="<?= _l('invoice_record_payment_note_placeholder'); ?>" id="note"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <a href="#" class="btn btn-danger" onclick="init_invoice(<?= e($invoice->id); ?>); return false;"><?= _l('cancel'); ?></a>
                            <button type="submit" autocomplete="off" data-loading-text="<?= _l('wait_text'); ?>" data-form="#connect_payment_form" class="btn btn-success"><?= _l('submit'); ?></button>
                        </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        init_selectpicker();
        init_datepicker();
        appValidateForm($('#record_payment_form'), {
            amount: 'required',
            date: 'required',
            paymentmode: 'required'
        });

        // Validation for the new form
        appValidateForm($('#connect_payment_form'), {
            connect_date: 'required'
        });

        var $sMode = $('select[name="paymentmode"]');
        var total_available_payment_modes = $sMode.find('option').length - 1;
        if (total_available_payment_modes == 1) {
            $sMode.selectpicker('val', $sMode.find('option').eq(1).attr('value'));
            $sMode.trigger('change');
        }
    });
    
    function amountOnChange(){
        var $amount = $('#select_transfer_details');
        console.log($amount.val())
        if (parseFloat($amount.val()) > parseFloat(max)) {
            $amount.val(max);
        } else if (parseFloat($amount.val()) <= 0 || $amount.val() == '') {
            $amount.val(0);
        }
    }

    function selectBankOnChange(){
        var $sMode = $('#select_bank_account');
        console.log($sMode.val())
        
        $.ajax({
            url: admin_url + 'invoices/get_transfer_details/' + $sMode.val(),
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                var $transactionDetails = $('select[name="transfer_details"]');
                $transactionDetails.empty();
                $transactionDetails.append('<option value=""></option>');
                $.each(response, function(index, item) {
                    var optionText = formatRupiah(item.transfer_amount);
                    if (item.description) {
                        optionText += ' - ' + item.description;
                    }
                    $transactionDetails.append('<option value="' + item.id + '">' + optionText + '</option>');
                });
                $transactionDetails.selectpicker('refresh');
            },
        })
    }

    function formatRupiah(amount) {
        if (amount === null || amount === undefined) {
            return 'Rp. 0';
        }
        var s = amount.toString();
        s = s.replace(/[^0-9.,-]/g, '');
        if (s.indexOf('.') !== -1 && s.indexOf(',') !== -1) {
            s = s.replace(/\./g, '');
            s = s.replace(/,/g, '.');
        }
        s = s.replace(/,/g, '');

        var parts = s.split('.');
        var intPart = parts[0];
        var decPart = parts[1] ? parts[1] : '';
        if (intPart === '') intPart = '0';
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'Rp. ' + intPart + (decPart ? ',' + decPart : '');
    }
</script>