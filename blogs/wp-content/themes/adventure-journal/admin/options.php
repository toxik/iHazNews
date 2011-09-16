<?php
    if ( !current_user_can('manage_options') ) {
        wp_die( __( 'You do not have sufficient permissions to manage options for this site.','contexture-page-security' ) );
    }

    global $themeDir,
           $hook_suffix;

    $newOpts = array();
    $message = '';
    $debugOpts = '';

    echo "<!-- The hook for the current page is \"";
    print_r( $hook_suffix );
    echo "\" -->\n";

    if(!empty($_POST) && wp_verify_nonce($_POST['_wpnonce'],'aj-options')){

        //Get rid of any leading forward slashes
        $_POST['css-path'] = preg_replace('/^\/+/', '', trim( $_POST['css-path'] ) );
		$_POST['logo-path'] = preg_replace('/^\/+/', '', trim( $_POST['logo-path'] ) );

        //Set new options
        $newOpts['css-path'] = $_POST['css-path'];
        $newOpts['logo-path'] = $_POST['logo-path'];
        $newOpts['title-type'] = $_POST['title-type'];
        $newOpts['paper-type'] = $_POST['paper-type'];
        $newOpts['header-height'] = $_POST['header-height'];
        $newOpts['featured-header'] = ( isset( $_POST['featured-header'] ) ) ? 'true' : 'false';
        $newOpts['attrib'] = ( isset( $_POST['attrib'] ) ) ? 'false' : 'true';

        //Update the options
        ctx_aj_set_options($newOpts);
        //Show success message

        //Build error message
        $message = sprintf(__('<div id="message" class="updated below-h2">
            <p>
                Options updated. <a href="%s" target="_blank">View your site</a> to see how it looks.
            </p>%s
        </div>','adventurejournal'),
                home_url(),
                (isset($_POST['attrib'])) ? __('<p>I see you removed the attribution. Does this mean we\'re not friends any more?</p>','adventurejournal') : ''
        );
    }

    //Load default theme variables
    $AJOpts = get_option('ctx-adventurejournal-options');

    if(!empty($_GET['showopts']) && $_GET['showopts']=='true'){
        $debugOpts = '<hr/><pre>'.print_r($AJOpts,true).'</pre><hr/>';
    }

    ?>
    <script type="text/javascript">
        jQuery(function(){
            jQuery('#show-more').click(function(){
                jQuery(this).hide();
                jQuery('#more-opts').show();
            });
                    jQuery('#title-default').click(function(){
                            jQuery('.custom-logo').hide();
            });
                    jQuery('#title-blank').click(function(){
                            jQuery('.custom-logo').hide();
            });
                    jQuery('#title-logo').click(function(){
                            jQuery('.custom-logo').show();
            });
        });
    </script>
    <style type="text/css">
        #ad-msg-auth, #ad-msg-anon { width:500px; }
        #ctx-about {width:326px;float:right;border:1px solid #e5e5e5;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;padding:10px;margin-top:25px;margin-right:20px;margin-left:10px;}
        #ctx-about a.img-block {display:block;text-align:center;}
        #ctx-about p, #ctx-about div {padding-left:10px;color:#9c9c9c;}
        #ctx-about p a { color:gray; }
        #ctx-ps-opts-form {float:left;width:765px;padding-top:0 !important;}
        .ctx-footnote { color:#9C9C9C; font-style:italic; }
        #show-more { cursor:pointer; color:gray; visibility:hidden; }
        #ctx-opts-table { }
    </style>
<div class="wrap">
<table cellpadding="0" cellspacing="0" id="ctx-opts-table" style="border:none;width:100%;">
    <tr>
        <td id="ctx-ps-opts-form">
                <div class="icon32" id="icon-users"><br/></div>
                <h2><?php _e('Adventure Journal Options','adventurejournal') ?></h2>
                <?php echo $message,$debugOpts; ?>
                <p></p>
                <form method="post" action="">
                    <?php wp_nonce_field('aj-options'); ?>
                    <h3 class="title"><?php _e('Custom Stylesheet','adventurejournal'); ?></h3>
                    <p><?php _e('You can use a custom stylesheet to override the default Adventure Journal styles.
                    We suggest you keep the custom css file in the <code>/wp-content/</code>
                    directory so that upgrades to WordPress or Adventure Journal don\'t erase your CSS.','adventurejournal') ?></p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="filter-menu"><?php _e('CSS File Location:','adventurejournal') ?></label>
                            </th>
                            <td>
                                <label>
                                    <?php echo 'http://'.$_SERVER['SERVER_NAME'].'/' ?>
                                    <input type="text" name="css-path" id="css-path" title="Example: wp-content/adventurejournal_override.css" style="width:300px;font-size:10px;" value="<?php echo $AJOpts['css-path']; ?>" /> <br /><span style="color:red;"><?php if(!file_exists(ABSPATH.$AJOpts['css-path'])){ _e('Notice: File does not exist! ('.'http://'.$_SERVER['SERVER_NAME'].'/'.$AJOpts['css-path'].')','adventurejournal'); } ?></span><br/>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <div style="border-bottom:1px dotted silver;margin:1em 0 -1em 0;"></div>
                    <h3 class="title"><?php _e('Site Title &amp; Description','adventurejournal'); ?></h3>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ctx-table site-title">
                    <tr>
                        <td <?php if($AJOpts['title-type'] == 'title-default'){echo ' class="active-layout"';}?>><p><strong><?php _e('Default','adventurejournal'); ?></strong></p>
                        <input name="title-type" value="title-default" id="title-default" class="radial" type="radio" <?php if($AJOpts['title-type'] == 'title-default'){echo ' checked="checked"';}?>>
                        <img src="<?php echo $themeDir; ?>/images/title-default.jpg" alt="Default Site Title" width="200" height="122" /> </td>
                        <td <?php if($AJOpts['title-type'] == 'title-blank'){echo ' class="active-layout"';}?>><p><strong><?php _e('No Title','adventurejournal'); ?></strong></p>
                        <input name="title-type" value="title-blank" id="title-blank" class="radial" type="radio" <?php if($AJOpts['title-type'] == 'title-blank'){echo ' checked="checked"';}?>>
                        <img src="<?php echo $themeDir; ?>/images/title-blank.jpg" alt="No Site Title" width="200" height="122"  /></td>
                        <td <?php if($AJOpts['title-type'] == 'title-logo'){echo ' class="active-layout"';}?>><p><strong><?php _e('Custom Logo','adventurejournal'); ?></strong></p>
                        <input name="title-type" value="title-logo" id="title-logo" class="radial" type="radio" <?php if($AJOpts['title-type'] == 'title-logo'){echo ' checked="checked"';}?>>
                        <img src="<?php echo $themeDir; ?>/images/title-logo.jpg" alt="Custom Logo Site Title" width="200" height="122"  /></td>
                      </tr>
                    </table>

                    <table class="form-table custom-logo" <?php if($AJOpts['title-type'] != 'title-logo'){echo ' style="display:none;"';}?>>
                    <tr valign="top">
                    <th scope="row"> <label for="css-path-logo"><?php _e('Custom Logo Location:','adventurejournal'); ?></label><br />
                    <p style="font-size:10px;"><?php _e('Image height should be <br />less than 90 pixels.','adventurejournal'); ?></p>
                      </th>
                        <td><label> <em>  <?php echo 'http://'.$_SERVER['SERVER_NAME'].'/' ?></em>
                          <input type="text" name="logo-path" id="logo-path" title="Example: wp-content/adventurejournal_override.css" style="width:300px;font-size:10px;" value="<?php echo $AJOpts['logo-path']; ?>" />
                          <span style="color:red;"><?php if(!file_exists(ABSPATH.$AJOpts['logo-path'])){ _e('<br /> Notice: File does not exist! ('.'http://'.$_SERVER['SERVER_NAME'].'/'.$AJOpts['logo-path'].')','adventurejournal'); } ?></span> </label><br />
                    <p style="font-size:10px;"><?php _e('For best results, use a image file with a transparent background such as gif or png.','adventurejournal'); ?></p>
                      </td>
                      </tr>
                    </table>

                    <div style="border-bottom:1px dotted silver;margin:2em 0 -1em 0;"></div>
                    <h3 class="title"><?php _e('Crinkled Paper Background','adventurejournal'); ?></h3>
                    <p><?php _e('By default, Adventure Journal only uses the "crinkled paper" texture to all stickied posts. If you would like this texture to appear for ALL post listings, you can set that here.','adventurejournal'); ?></p>
                    <table class="form-table" >
                        <tr valign="top">
                            <td>
                                <p>
                                    <label>
                                        <input name="paper-type" value="paper-sticky" id="paper-sticky" class="radial" type="radio" <?php if($AJOpts['paper-type'] == 'paper-sticky' || !isset($AJOpts['paper-type'])){echo ' checked="checked"';}?>>
                                        <?php _e('<strong>Stickied Posts Only</strong> (default)','adventurejournal'); ?>
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input name="paper-type" value="paper-all" id="paper-all" class="radial" type="radio" <?php if($AJOpts['paper-type'] == 'paper-all'){echo ' checked="checked"';}?>>
                                        <?php _e('<strong>All Posts</strong>','adventurejournal'); ?>
                                    </label>
                                </p>
                            </td>
                            <td>
                                 <img src="<?php echo $themeDir; ?>/images/crinkled-paper.jpg" alt="Crinkled Paper Background" width="200" height="122" style="border: 1px solid #999999;" />
                            </td>
                        </tr>
                    </table>

                    <div style="border-bottom:1px dotted silver;margin:2em 0 -1em 0;"></div>
                    <h3 class="title"><?php _e('Custom Header Size','adventurejournal'); ?></h3>
                    <p><?php echo sprintf(__('To change the vertical size of the header image, first enter a value in below, save changes and then upload your image on the <a href="%s">header page</a>. The default value is 360.','adventurejournal'),admin_url().'themes.php?page=custom-header'); ?></p>
                    <p>920 x <input type="text" name="header-height" id="header-height" value="<?php echo (!empty($AJOpts['header-height'])) ? $AJOpts['header-height'] :'360'; ?>" size="6" /> pixels</p>


                    <div style="border-bottom:1px dotted silver;margin:2em 0 -1em 0;"></div>
                    <h3 class="title"><?php _e('Featured Images','adventurejournal'); ?></h3>
                    <p><?php _e('Enable this option if you would like featured images to replace your site header on single pages. When enabled, featured images will no longer appear in blog listings.','adventurejournal'); ?></p>
                    <p><label><input type="checkbox" name="featured-header" <?php echo ($AJOpts['featured-header']==='true') ? 'checked="checked"' : ''; ?>/> <strong><?php _e('Use featured images as custom site header, if available','adventurejournal'); ?></strong></label></p>



                    <div style="border-bottom:1px dotted silver;margin:2em 0 -1em 0;"></div>
                    <h3 class="title"><?php _e('Layout','adventurejournal'); ?></h3>
                    <p><?php echo sprintf(__('You can change the site\'s layout from the <a href="%s">layout screen</a>.','adventurejournal'),admin_url().'themes.php?page=theme-layouts'); ?></p>

                    <p>
                        <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    </p>
            </form>
        </td>
        <td style="vertical-align:top;">
            <div id="ctx-about">
                <a class="img-block" href="http://www.contextureintl.com"><img src="<?php echo get_template_directory_uri().'/images/ctx-logo.gif'; ?>" alt="Contexture International" /></a>
                <p>Contexture International is an all-in-one agency specializing in <a href="http://www.contextureintl.com/portfolio/graphic-design/">graphic design</a>, <a href="http://www.contextureintl.com/portfolio/web-interactive/">web design</a>, and <a href="http://www.contextureintl.com/portfolio/broadcast-video-production/">broadcast and video production</a>, with an unparalleled ability to connect with the heart of your audience.</p>
                <p>Contexture's staff has successfully promoted organizations and visionaries for more than 2 decades through exceptional storytelling, in just the right contexts for their respective audiences, with overwhelming returns on investment.  See the proof in our <a href="http://www.contextureintl.com/portfolio/">portfolio </a>or learn more <a href="http://www.contextureintl.com/about-us/">about us</a>.</p>
                <div><a href="http://www.contextureintl.com/">Need a custom web or video project?</a></div>
                <div><a href="http://www.contextureintl.com/wordpress/adventure-journal-wordpress-theme/">Need help with Adventure Journal?</a></div>
            </div>
        </td>
    </tr>
</table>
</div>