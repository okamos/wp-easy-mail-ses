<div class="wrap">
    <h1><?php _e('SES easy mail', WPEM4S_ID) ?></h1>
    <form name="form" method="post" action="">
        <h2><?php _e('AWS settings', WPEM4S_ID) ?></h2>
        <div class="field">
            <label>
                <span><?php _e('AWS access key id', WPEM4S_ID); ?></span>
                <input type="text" name="access_key" autofocus value="<?php echo esc_attr($opts['access_key']); ?>">
            <label>
        </div>

        <div class="field">
            <label>
                <span><?php _e('AWS secret key ID', WPEM4S_ID); ?></span>
                <input type="password" name="secret_key" value="<?php echo esc_attr($opts['secret_key']); ?>">
            </label>
        </div>

        <div class="field">
            <label>
                <span><?php _e('AWS region', WPEM4S_ID); ?></span>
                <select name="region">
                    <option value="us-east-1" <?php
                    if ($opts['region'] == 'us-east-1') {
                        echo 'selected';
                    } ?>>
                        <?php echo _e('United States east 1', WPEM4S_ID); ?>
                    </option>
                    <option value="us-west-2" <?php
                    if ($opts['region'] == 'us-west-2') {
                        echo 'selected';
                    } ?>>
                        <?php echo _e('United States west 2', WPEM4S_ID); ?>
                    </option>
                    <option value="eu-west-1" <?php
                    if ($opts['region'] == 'eu-west-1') {
                        echo 'selected';
                    } ?>>
                        <?php echo _e('European Union west 1', WPEM4S_ID); ?>
                    </option>
                </select>
            </label>
        </div>

        <h2><?php _e('Sender settings', WPEM4S_ID); ?></h2>
        <div class="field">
            <label>
                <span><?php _e('Sender Email address', WPEM4S_ID); ?></span>
                <input type="email" name="from_email" value="<?php echo esc_attr($opts['from_email']); ?>">
            </label>
        </div>

        <div class="field">
            <label>
                <span><?php _e('Sender name', WPEM4S_ID); ?></span>
                <input type="text" name="from_name" value="<?php echo esc_attr($opts['from_name']); ?>">
            </label>
        </div>

        <div class="button-margin">
            <input type="submit" class="button button-primary" name="<?php echo $update_name ?>" value="<?php _e('Update', WPEM4S_ID); ?>">
        </div>
    </form>
    <h3><?php _e('Verification status', WPEM4S_ID); ?></h3>
    <p 
    <?php
    if ($opts['verified']) {
        echo 'class="green font-md"';
    } else {
        echo 'class="red font-md"';
    }
    ?>>
    <?php
    if ($opts['verified']) {
        _e('Verified', WPEM4S_ID);
        echo ':' . $opts['last_verified_at'];
    } else {
        _e('Failed', WPEM4S_ID);
    }
    ?>
    </p>
</div>
