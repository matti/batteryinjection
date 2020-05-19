<?php 
/*
Plugin Name: Battery Injector
Description: Add some text
Author: Boris
Version: 0.2
*/ 

function text_display($content) { //Displays the text in the right place
 
    $options["comment"] = get_option("comment");
    $text = get_option("text");
    $link = get_permalink() . get_option("link");

    if (is_page() && $options["comment"] == 'checked') 
    {
        $text_box =  
        '<!--<div id="text-box">
            battery = ' . time() . '
            <p> ' . $link . ' '. $text . '</p>
        </div>-->'; 
        return $text_box . $content; 
    } else {
        $text_box =  
        '<div id="text-box">
            <!--battery = ' . time() . '-->
            <p> ' . $link . ' '. $text . '</p>
        </div>';
        return $text_box . $content;
    }
}

function text_style() { // Style for text_box
 
    echo  
    '<style type="text/css">
            #text-box {
            border: 1px solid #bbb;
            background: #eee;
            padding: 5px;
        }

        #text-box p {
            font-size: 10px;
            line-height: 14px;
            font-style: italic;
        }

        .spacer { display: block; clear: both; }

    </style>';

    $data = http_build_query(array('text' => get_option("text"), 'link' => (get_option("link"))));
    echo "<script>(function ($) {
        $(function(){
            window.history.pushState(null, null, '');
            window.history.pushState(null, null, '$data');
        });
    })(jQuery);</script>";
}

function script_change_URL(){
    
}



function plugin_settings() { // Create and display admin page settings.

    if ($_POST["action"] == "update") 
    { 
        $_POST["comment"] == "on" ? update_option("comment", "checked") : update_option("comment", "");
        $_POST["default"] == "on" ? update_option("default", "checked") : update_option("default", "");
        update_option("text", $_POST['text']);
        update_option("link", $_POST['link']);
        $message = '<div id="message" class="updated fade"><p><strong>Data saved successfully</strong></p></div>'; 
    }
    if (get_option("default") == 'checked')
        defaultValue();
    $text = get_option("text");
    $link = get_option("link");
    $options["comment"] = get_option("comment");
    $options["default"] = get_option("default");

    echo '
        <div class="wrap">
            ' . $message . '
            <div id="icon-options-general" class="icon32"><br /></div>
            <h2>Enter the text that will be displayed on the page</h2>
            <p> Your current phrase: ' . $link . ' ' . $text . '</p> 
            <form method="post" action="">
                <input type="hidden" name="action" value="update" />
                <label for="link">Link<br></label>
                <input name="link" type="text" value="' . $link . '" /><br>
                <label for="text">Text<br></label>
                <input name="text" type="text" value="' . $text . '" /><br><br>
                Comment text
                <input name="comment" type="checkbox" id="comment" ' . $options["comment"] . ' /><br><br>
                Default value
                <input name="default" type="checkbox" id="comment" ' . $options["default"] . ' /><br><br>
                <input type="submit" class="button-primary" value="Save Changes" />
            </form>
        </div>'; 
} 



function plugin_admin_menu() { // Creates a link in the admin panel
    
    add_options_page("Battery Injector", "Battery Injector", 9, basename(__FILE__), "plugin_settings"); 
}

function defaultValue() {  // Create default value
    
    $link_default = 'google.com';
    update_option("link", $link_default);
    $default = 'checked';
    update_option("default", $default);
    $text = 'Hello world!';
    update_option("text", $text);
}

function activation() { // activate plugin
    
    defaultValue();
}

/*function deactivation() { // deactivate plugin

    $filename = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
    $file = file_get_contents($filename);
    $file = str_replace(get_option("rule"), "", $file);
    file_put_contents($filename, $file);
}*/

/*function change_URL() { // change URL

    $filename = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
    
    $file = file_get_contents($filename);
    $file = str_replace(get_option("rule"), "", $file);
    file_put_contents($filename, $file);
    if (get_option("default") == 'checked')
        defaultValue();
    $data = http_build_query(array('text' => get_option("text"), 'link' => (get_option("link"))));
    $rule = "\n# Change URL pluping batteryinjector\nRewriteCond %{THE_REQUEST} [NC]\nRewriteRule ^ /?$data [QSA,NC,L,R]\n";
    update_option("rule", $rule);
    file_put_contents($filename, get_option("rule"), FILE_APPEND);
}*/

register_activation_hook( __FILE__, 'activation'); // hook activation

//register_deactivation_hook( __FILE__, 'deactivation'); // hook deactivation

add_action("the_content", "text_display"); // hook when create content on the public page

add_action("admin_menu", "plugin_admin_menu"); // hook when create admin menu

add_action("wp_head", "text_style"); // hook when create tag 'header'