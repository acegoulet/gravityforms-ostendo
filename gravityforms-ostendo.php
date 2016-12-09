<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://sanbornagency.com
 * @since             1.0.0
 * @package           Gravityforms_Ostendo
 *
 * @wordpress-plugin
 * Plugin Name:       Gravity Forms Ostendo Integration
 * Description:       Send form submissions to Ostendo in formatted XML attachment
 * Version:           1.0.0
 * Author:            Ace Goulet
 * Author URI:        http://www.acegoulet.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gravityforms-ostendo
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GF_OSTENDO_INTEGRATION', '1.0' );
add_action( 'gform_loaded', array( 'GF_Simple_AddOn_Bootstrap', 'load' ), 5 );
class GF_Simple_AddOn_Bootstrap {
    public static function load() {
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }
        require_once( 'class-gfostendo.php' );
        GFAddOn::register( 'GFOstendo' );
    }
}
function gf_ostendo() {
    return GFOstendo::get_instance();
}

//ostendo ID custom field for form fields
add_action( 'gform_field_standard_settings', 'ostendo_field_id', 10, 2 );
function ostendo_field_id( $position, $form_id ) {
    $thisform = GFAPI::get_form( $form_id );
    
    //if ostendo enabled for this form
    if(!empty($thisform['gfostendo']) && $thisform['gfostendo']['ostendo_enabled'] == true){
        //create settings on position 25 (right after Field Label)
        if ( $position == 20 ) {
            ?>
            <li class="ostendo_id field_setting">
                <label for="field_admin_label">
                    <?php _e( 'Ostendo ID', 'gravityforms' ); ?>
                    <?php gform_tooltip( 'form_field_ostendo_id' ) ?>
                </label>
                <input type="text" id="field_ostendo_id" name="field_ostendo_id" onchange="SetFieldProperty('ostendoField', this.value);" />
            </li>
            <?php
        }
    }
    else {
        //echo 'blah';
    }
}

add_action( 'gform_editor_js', 'ostendo_editor_script' );
function ostendo_editor_script(){
    ?>
    <script type='text/javascript'>
        //adding setting to fields of type "text"
        //console.log(fieldSettings);
        fieldSettings["text"] += ", .ostendo_id";
        fieldSettings["email"] += ", .ostendo_id";
        fieldSettings["phone"] += ", .ostendo_id";
        fieldSettings["radio"] += ", .ostendo_id";
        fieldSettings["checkbox"] += ", .ostendo_id";
        fieldSettings["check"] += ", .ostendo_id";
        fieldSettings["textarea"] += ", .ostendo_id";
        fieldSettings["select"] += ", .ostendo_id";
        //binding to the load field settings event to initialize the checkbox
        jQuery(document).bind("gform_load_field_settings", function(event, field, form){
            jQuery("#field_ostendo_id").val(field["ostendoField"]);
        });
    </script>
    <?php
}

add_filter( 'gform_tooltips', 'ostendo_field_id_tooltips' );
function ostendo_field_id_tooltips( $tooltips ) {
   $tooltips['form_field_ostendo_id'] = "<h6>Ostendo ID</h6>Input the Ostendo ID that corresponds to this field";
   return $tooltips;
}

//get form data for testing
/*
add_filter( 'gform_pre_render', 'show_form_data' );
function show_form_data( $form ) {
    print_r($form);
}
*/

//send data to ostendo
add_action( 'gform_after_submission', 'send_to_ostendo', 10, 2 );
function send_to_ostendo( $entry, $form ) {
    $plugin_path = __DIR__;
    if(!empty($form['gfostendo']) && $form['gfostendo']['ostendo_enabled'] == true){
        
        if(!empty($form['gfostendo']['ostendo_recipients'])){
            $entry_value_array = array();
            
            foreach($form['fields'] as $key => $field){
                $field_id = $field['id'];
                if(!empty($field['ostendoField'])){
                    $field_label = sanitize_text_field($field['ostendoField']);
                    
                    if($field['type'] !== 'checkbox'){
                        $entry_value_array[$field_label] = sanitize_text_field($entry[$field_id]);
                    }
                    else {
                        $checkbox_array = array();
                        for ($int = floatval($field_id)+0.1; $int <= floatval($field_id)+.9; $int = $int + 0.1) {
                            if(!empty($entry[(string) $int])){
                                array_push($checkbox_array, sanitize_text_field($entry[(string) $int]));
                            }
                        }
                        $entry_value_array[$field_label] = $checkbox_array;
                    }
                }
            }
            
            //setup xml file
            date_default_timezone_set('Australia/Sydney');
            unlink($plugin_path.'/xml/CustomerNew.xml');
            $xml = new SimpleXMLElement('<ostendoexport></ostendoexport>');
            $customer = $xml->addChild('customermaster');
            foreach($entry_value_array as $key => $entry_value){
                if(!is_array($entry_value)){
                    $customer->addChild($key, htmlspecialchars($entry_value));
                }
                else {
                    foreach($entry_value as $entry_value_item){
                        $customer->addChild(str_replace(' ', '_', $entry_value_item), 'Yes' );
                    }
                }
            }
            $customer->addChild('DATE', date("d/m/Y"));
            $customer->addChild('TIME', date("H:i"));
            
            //Format XML to save indented tree rather than one line
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            
            $dom->save($plugin_path.'/xml/CustomerNew.xml',LIBXML_NOEMPTYTAG);
            $contents = file_get_contents($plugin_path.'/xml/CustomerNew.xml');
            $contents = htmlspecialchars_decode($contents);
            $outfile = fopen($plugin_path.'/xml/CustomerNew.xml', 'w');
            fwrite($outfile, $contents);
            fclose($outfile);
            
            $email_to = $form['gfostendo']['ostendo_recipients'];
            $email_subject = "Customer New";
            $email_message = 'Ostendo Submission';
            $headers = 'From: info@armadillo-co.com<info@armadillo-co.com>'."\r\n".
            'Reply-To: test@test.com'."\r\n" .
            'X-Mailer: PHP/' . phpversion();
            wp_mail($email_to, $email_subject, $email_message, $headers, array($plugin_path.'/xml/CustomerNew.xml')); 
        }
    }
}

?>