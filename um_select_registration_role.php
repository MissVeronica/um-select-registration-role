<?php
/**
 * Plugin Name:     Ultimate Member - Select Registration Role
 * Description:     Extension to Ultimate Member for selecting user role at registration based on a dropdown field content.
 * Version:         1.0.1
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     um-visitors
 * Domain Path:     /languages
 * UM version:      2.5.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;


add_filter( 'um_settings_structure', 'um_settings_structure_customer_role_selection', 10, 1 );
add_action( 'um_registration_set_extra_data', 'um_registration_set_extra_data_role_selection', 10, 2 );

function um_registration_set_extra_data_role_selection( $user_id, $args ) {

    if( !empty( UM()->options()->get( 'role_selections_form' )) && $args['form_id'] == UM()->options()->get( 'role_selections_form' ) ) {
        
        if( !empty( UM()->options()->get( 'role_selections_field' ) ) ) { 
            $field = UM()->options()->get( 'role_selections_field' );

            if( !empty( $args[$field] )) {
                if( !empty( UM()->options()->get( 'role_selections_list' ) )) {
                    $role_selections = explode( "\n", UM()->options()->get( 'role_selections_list' ));

                    foreach( $role_selections as $role_selection ) {

                        $selection = explode( ':', $role_selection );
                        if( count( $selection ) == 2 ) {

                            if( $args[$field] == trim( $selection[0] )) {
                                $wp_user_object = new WP_User( $user_id );
                                $wp_user_object->set_role( trim( $selection[1] ));
                                UM()->user()->remove_cache( $user_id );
                                um_fetch_user( $user_id );
                                return;
                            }
                        }
                    }
                }
            }
        }
    }
}

function um_settings_structure_customer_role_selection( $settings ) {

    $settings['appearance']['sections']['registration_form']['fields'][] = array(
        'id'      => 'role_selections_form',
        'type'    => 'text',
        'label'   => __( 'Select Registration Role: Form ID', 'ultimate-member' ),
        'tooltip' => __( 'Registration Form ID for selecting different roles depending on field content', 'ultimate-member' ),
        'size'    => 'medium'
    ); 
    
    $settings['appearance']['sections']['registration_form']['fields'][] = array(
        'id'      => 'role_selections_field',
        'type'    => 'text',
        'label'   => __( 'Select Registration Role: Field meta-key', 'ultimate-member' ),
        'tooltip' => __( 'Registration Field for selecting different roles depending on this field content', 'ultimate-member' ),
        'size'    => 'medium'
    ); 

    $settings['appearance']['sections']['registration_form']['fields'][] = array(
        'id'      => 'role_selections_list',
        'type'    => 'textarea',
        'label'   => __( 'Select Registration Role: Selections ', 'ultimate-member' ),
        'tooltip' => __( 'Registration Roles for selecting and one per line: Registration field value:Role ID', 'ultimate-member' ),
    ); 

    return $settings;
}

