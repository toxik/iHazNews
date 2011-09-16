<?php
    global $themeDir;

    global $hook_suffix;
    echo "<!-- The hook for the current page is \"";
    print_r( $hook_suffix );
    echo "\" -->\n";

    //Empty message
    $message = '';

    if(!empty($_POST) && wp_verify_nonce($_POST['_wpnonce'],'update-options')){
        ctx_aj_set_options(array('layout'=>$_POST['master-layout']));
        $message = sprintf(__('<div id="message" class="updated below-h2"><p>Layout updated. <a href="%s">Visit your site</a> to see how it looks.</p></div>'),get_bloginfo('url'));
    }

    //Load default theme variables
    $ctx_theme_opts = get_option('ctx-adventurejournal-options');

    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"><br /></div>
        <h2><?php _e('Layout','adventurejournal') ?></h2>
        <?php echo $message; ?>
        <h3><?php _e('Page Layout','adventurejournal') ?></h3>
        <form method="post" action="">
            <?php wp_nonce_field('update-options'); ?>
            <table cellpadding="0" cellspacing="0" id="admin-layout">
                <tr>
                    <td <?php if($ctx_theme_opts['layout'] == 'col-1'){echo ' class="active-layout"';}?>>
                        <input name="master-layout" type="radio" value="col-1" id="layout-1" class="radial" <?php if($ctx_theme_opts['layout'] == 'col-1'){echo ' checked="checked"';}?>>
                        <label for="layout-1">
                            <div style="text-align:center;padding-bottom:5px;"><?php _e('1 Column<br/>(No Sidebar)','adventurejournal') ?></div>
                            <img src="<?php echo $themeDir; ?>/images/layout-opt-1col.gif" alt="1 Col" />
                        </label>
                    </td>
                    <td <?php if($ctx_theme_opts['layout'] == 'col-2-left'){echo ' class="active-layout"';}?>>
                        <input name="master-layout" type="radio" value="col-2-left" id="layout-3" class="radial" <?php if($ctx_theme_opts['layout'] == 'col-2-left'){echo ' checked="checked"';}?>>
                        <label for="layout-3">
                            <div style="text-align:center;padding-bottom:5px;"><?php _e('2 Columns <br />(Content Left)','adventurejournal') ?></div>
                            <img src="<?php echo $themeDir; ?>/images/layout-opt-2collt.gif" alt="2 Col Lt" />
                        </label>
                    </td>
                    <td <?php if($ctx_theme_opts['layout'] == 'col-2-right'){echo ' class="active-layout"';}?>>
                        <input name="master-layout" type="radio" class="radial" id="layout-2" value="col-2-right" <?php if($ctx_theme_opts['layout'] == 'col-2-right'){echo ' checked="checked"';}?>>
                        <label for="layout-2">
                            <div style="text-align:center;padding-bottom:5px;"><?php _e('2 Columns <br />(Content Right)','adventurejournal') ?></div>
                            <img src="<?php echo $themeDir; ?>/images/layout-opt-2colrt.gif" alt="2 Col Rt" />
                        </label>
                    </td>
                    <td <?php if($ctx_theme_opts['layout'] == 'col-3'){echo ' class="active-layout"';}?>>
                        <input name="master-layout" type="radio" class="radial" id="layout-4" value="col-3" <?php if($ctx_theme_opts['layout'] == 'col-3'){echo ' checked="checked"';}?>>
                        <label for="layout-4">
                            <div style="text-align:center;padding-bottom:5px;"><?php _e('3 Columns <br/>(Content Middle)','adventurejournal') ?></div>
                            <img src="<?php echo $themeDir; ?>/images/layout-opt-3col.gif" alt="3 Col" />
                        </label>
                    </td>
                    <td <?php if($ctx_theme_opts['layout'] == 'col-3-left'){echo ' class="active-layout"';}?>>
                        <input name="master-layout" type="radio" class="radial" id="layout-5" value="col-3-left" <?php if($ctx_theme_opts['layout'] == 'col-3-left'){echo ' checked="checked"';}?>>
                        <label for="layout-5">
                            <div style="text-align:center;padding-bottom:5px;"><?php _e('3 Columns <br/>(Content Left)','adventurejournal') ?></div>
                            <img src="<?php echo $themeDir; ?>/images/layout-opt-3col-lt.gif" alt="3 Col Lt" />
                        </label>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" name="save-layout-options" value="Save Changes"/>
            </p>
        </form>
    </div>