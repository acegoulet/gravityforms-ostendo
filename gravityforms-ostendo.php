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

add_action( 'gform_after_submission', 'send_to_ostendo', 10, 2 );
function send_to_ostendo( $entry, $form ) {
    $plugin_path = __DIR__;
/*
    print_r($form);
    print_r($entry);
*/
    if(!empty($form['gfostendo']) && $form['gfostendo']['ostendo_enabled'] == true){
        
        if(!empty($form['gfostendo']['ostendo_recipients'])){
            $entry_value_array = array();
            foreach($form['fields'] as $key => $field){
                $field_label = sanitize_text_field($field['label']);
                $entry_value_array[$field_label] = sanitize_text_field($entry[$key + 1]);
            }
            //print_r($entry_value_array);
            
            //setup xml file
            date_default_timezone_set('Australia/Sydney');
            unlink($plugin_path.'/xml/CustomerNew.xml');
            $xml = new SimpleXMLElement('<ostendoexport></ostendoexport>');
            $customer = $xml->addChild('customermaster');
            foreach($entry_value_array as $key => $entry_value){
                $customer->addChild($key, htmlspecialchars($entry_value));
            }
            
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
            $email_message = $entry_value_array;
            $headers = 'From: info@armadillo-co.com<info@armadillo-co.com>'."\r\n".
            'Reply-To: test@test.com'."\r\n" .
            'X-Mailer: PHP/' . phpversion();
            wp_mail($email_to, $email_subject, $email_message, $headers, array($plugin_path.'/xml/CustomerNew.xml')); 
        }
        
        
    }
}

?>