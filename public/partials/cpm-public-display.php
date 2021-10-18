<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://ameer.ir
 * @since      1.0.0
 *
 * @package    Cpm
 * @subpackage Cpm/public/partials
 */
if ( !is_user_logged_in() ) {
    wp_redirect( wp_login_url() );
    exit;
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="cpm-payment-form" class="cpm-container">
    <div class="cpm-overlay" v-if="loading">
        <img src="<?= plugin_dir_url(__DIR__) . '../assets/img/loading.gif' ?>" alt="loading animation" width="64px">
    </div>
    <h3 class="cpm-mb-4"><?= _e('Custom Payment:', 'cpm') ?></h3>
    <div id="cpm-confirm-form" v-if="showConfirm">

        <table class="cpm-table">
            <tr>
                <th><?= _e('Request Result', 'cpm') ?></th>
                <td><?= _e('Successful', 'cpm') ?></td>
            </tr>
            <tr>
                <th><?= _e('Full Name:', 'cpm') ?></th>
                <td>{{fullname}}</td>
            </tr>
            <tr>
                <th><?= _e('Mobile Number:', 'cpm') ?></th>
                <td>{{mobileNumber}}</td>
            </tr>
            <tr>
                <th><?= _e('Amount:', 'cpm') ?></th>
                <td>{{amount}}</td>
            </tr>
            <tr>
                <th><?= _e('Reference Number:', 'cpm') ?> </th>
                <td>{{orderId}}</td>
            </tr>
        </table>
        <form ref="paymentConfirm" method="post" :action="paymentURL">
            <div class="cpm-mb-4 text-center cc-container">
                <input type="hidden" name="RefId" :value="RefId">
                <input class="cpm-mb-4" type="submit" value="<?= _e('Confirm Request', 'cpm') ?>">
            </div>
        </form>
    </div>
    <div id="cpm-payment-form" class="cpm-form" v-else>
        <form ref="cpmPaymentForm" @submit.prevent="submitPayment()">
            <div>
                <div class="cpm-mb-8 text-center">
                    <label class="d-block" for="cpm-fullname"><?= _e('Full Name:', 'cpm') ?></label>
                    <input type="text" name="cpm[fullname]" id="cpm-fullname" v-model="$v.fullname.$model" :class="status($v.fullname)" required @keyup="validate">
                </div>
                <div class="cpm-mb-8 text-center">
                    <label class="d-block" for="cpm-nationalCode"><?= _e('National Code:', 'cpm') ?></label>
                    <input type="text" name="cpm[nationalCode]" id="cpm-nationalCode" v-model="$v.nationalCode.$model" :class="status($v.nationalCode)" required @keyup="validate" minlength="10" maxlength="10">
                    <div class="error" :class="{show: showErrors}" v-if="!$v.nationalCode.minLength"><?= _e('This field must have at least', 'cpm') ?> {{$v.nationalCode.$params.minLength.min}} <?= _e('digits.', 'cpm') ?></div>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.nationalCode.maxLength"><?= _e('This field must not have more than ', 'cpm') ?> {{$v.nationalCode.$params.maxLength.max}} <?= _e('digits.', 'cpm') ?></div>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.nationalCode.numeric"><?= _e('This field must contain only digits.', 'cpm') ?></div>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.nationalCode.nationalCodeChecker"><?= _e('The national code is incorrect.', 'cpm') ?></div>
                </div>
                <div class="cpm-mb-8 text-center">
                    <label class="d-block" for="cpm-mobile-number"><?= _e('Mobile Number:', 'cpm') ?></label>
                    <input type="text" name="cpm[mobileNumber]" id="cpm-mobile-number" v-model="$v.mobileNumber.$model" :class="status($v.mobileNumber)" required maxlength="11" minlength="11" @keyup="validate">
                    <div class="error" :class="{show: showErrors}" v-if="!$v.mobileNumber.minLength"><?= _e('This field must have at least', 'cpm') ?> {{$v.mobileNumber.$params.minLength.min}} <?= _e('digits.', 'cpm') ?></div>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.mobileNumber.maxLength"><?= _e('This field must not have more than ', 'cpm') ?> {{$v.mobileNumber.$params.maxLength.max}} <?= _e('digits.', 'cpm') ?></div>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.mobileNumber.numeric"><?= _e('This field must contain only digits.', 'cpm') ?></div>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.mobileNumber.phone"><?= _e('The phone number must starts with 09', 'cpm') ?></div>
                </div>
                <div class="cpm-mb-4 text-center">
                    <label class="d-block" for="cpm-custom-amount"><?= _e('Custom Amount:', 'cpm') ?></label>
                    <input type="text" id="cpm-custom-amount" v-model.number="$v.amount.$model" :class="status($v.amount)" required @keyup="validate">
                    <p class="cpm-mt-2 cpm-mb-1 fa-price">{{amountInFa}}</p>
                    <div class="error" :class="{show: showErrors}" v-if="!$v.amount.numeric"><?= _e('This field must contain only digits.', 'cpm') ?></div>
                </div>
                <div class="cpm-mb-4 text-center" v-if="!$v.mobileNumber.$invalid">
                    <input type="submit" value="<?= _e('Submit Request', 'cpm') ?>">
                </div>
            </div>
        </form>
    </div>
</div>