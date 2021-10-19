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
        <th><?= _e('Amount (IRT)', 'cpm') ?></th>
        <td><?php echo number_format($finalAmount, 0, ',', ','); ?></td>
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