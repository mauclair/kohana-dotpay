<?php echo Form::open($paymentURL, array('method' => 'post', 'name' => 'dotpay')); ?>
    <fieldset> 
    <?php
    foreach ($hidden as $key => $value)
        echo Form::hidden($key, $value) . "\n";
    ?>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
                <?php if (isset($paymentChannels)): ?>
                <tr>
                    <td class="payment-left">
                        <span>Wybierz kanał płatności:</span><br>
                    </td>
                    <td style="vertical-align: top;">
                        <table width="100%">
                            <tbody>
                                <?php foreach ($paymentChannels as $channelKey => $channelName): ?>
                                <tr>
                                    <td><input type="radio" name="channel" value="<?php echo $channelKey; ?>" id="channel<?php echo $channelKey; ?>"></td>
                                    <td><label for="channel<?php echo $channelKey; ?>"><?php echo $channelName; ?></label></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Kwota</td>
                    <td><strong><?php echo $amount; ?> zł</strong></td>
                </tr>
                <tr>
                    <td>Opis transakcji:</td>
                    <td><?php echo $description; ?></td>
                </tr>
            </tbody>
        </table>
        <div class="buttonHolder"> 
            <?php echo Form::submit('pay', __('Zapłać teraz'), array('class' => 'primaryAction')); ?>
        </div> 
    </fieldset>
<?php echo Form::close(); ?>