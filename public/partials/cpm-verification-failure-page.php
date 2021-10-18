<?php
if ( !is_user_logged_in() ) {
    auth_redirect();
    exit;
}
?>
<table class="cpm-table">
    <tr>
        <th><?= _e('Transaction Stauts', 'cpm') ?></th>
        <td><?= _e('Verification Failed', 'cpm') ?></td>
    </tr>
    <tr>
        <th><?= _e('Reason', 'cpm') ?></th>
        <td><?=$ex_msg?></td>
    </tr>
    <tr>
        <!-- REMEMBER USER NAME AND USER INFO -->
        <th><?= _e('Full Name', 'cpm') ?></th>
        <td><?php echo $fullname ?></td>
    </tr>
    <tr>
        <th><?= _e('Transaction Reference ID', 'cpm') ?></th>
        <td><?php echo $refId ?></td>
    </tr>
    <tr>
        <th><?= _e('Amount', 'cpm') ?></th>
        <td><?php echo $finalAmount; ?></td>
    </tr>
    <tr>
        <th><?= _e('Order ID', 'cpm') ?></th>
        <td><?php echo $saleOrderId; ?></td>
    </tr>
    <tr>
        <th><?= _e('Reference Number', 'cpm') ?></th>
        <td><?php echo $saleReferenceId; ?></td>
    </tr>
</table>