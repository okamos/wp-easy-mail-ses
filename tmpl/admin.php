<div class="wrap">
    <h2><?php _e('SES Easy mail', WPEM4S_ID) ?></h2>
    <form name="form" method="post" action="">
        <div>
            <label>
                <?php _e('AWS access key id', WPEM4S_ID); ?>
                <input type="text" name="access_key" value="<?php echo esc_attr($opts['access_key']); ?>">
            <label>
        </div>

        <div>
            <label>
                <?php _e('AWS secret key ID', WPEM4S_ID); ?>
                <input type="text" name="secret_key" value="<?php echo esc_attr($opts['secret_key']); ?>">
            </label>
        </div>

        <div>
            <label>
                <?php _e('AWS region', WPEM4S_ID); ?>
                <input type="text" name="region" value="<?php echo esc_attr($opts['region']); ?>">
            </label>
        </div>

        <div>
            <label>
                <?php _e('Sender Email address', WPEM4S_ID); ?>
                <input type="text" name="from_email" value="<?php echo esc_attr($opts['from_email']); ?>">
            </label>
        </div>

        <div>
            <label>
                <?php _e('Sender name', WPEM4S_ID); ?>
                <input type="text" name="from_name" value="<?php echo esc_attr($opts['from_name']); ?>">
            </label>
        </div>

        <input type="submit" name="<?php echo $update_name ?>" value="<?php _e('Update', WPEM4S_ID); ?>">
    </form>
    <?php
    if ($opts['verified']) {
        echo 'verified';
    } else {
        echo 'failed';
    }
    ?>
</div>
