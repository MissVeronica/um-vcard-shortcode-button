<?php
/**
 * Plugin Name:     Ultimate Member - vCard shortcode button
 * Description:     Extension to Ultimate Member for creating a User Profile vCard
 * Version:         1.1.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica/repository-content-for-UM
 * Plugin URI:      https://github.com/MissVeronica/um-vcard-shortcode-button
 * Update URI:      https://github.com/MissVeronica/um-vcard-shortcode-button
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.10.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_vCard_Shortcode_Button {

    function __construct() {

        add_shortcode( 'um_vcard_button', array( $this, 'um_vcard_button' ));
    }

    public function um_vcard_button( $atts ) {

        if ( isset( UM()->fields()->editing ) && UM()->fields()->editing === true ) {

            return '';
        }

        if ( is_writable( WP_CONTENT_DIR . '/uploads/ultimatemember/' )) {

            $atts = array_map( 'sanitize_text_field',
                                    shortcode_atts( array(
                                        'street'     => 'street',
                                        'city'       => 'city',
                                        'zip'        => 'zip',
                                        'state'      => 'state',
                                        'address'    => 'address',
                                        'org'        => 'company',
                                        'title'      => 'title',
                                        'url'        => 'user_url',
                                        'revision'   => 'last_update',
                                        'vcard'      => 'download',
                                        'button'     => 'Download vCard',
                                        'html_title' => esc_html__( 'Create the vCard for this user profile', 'ultimate-member' ),
                                        '#place'     => 'WORK',
                                    ), $atts 
                                )); 

            $html = '
                        <div id="vcard" class="vcard">
                            <form method="post" action="">
                                <input type="hidden" name="vcard_button"  value="true">
                                <input type="submit" class="vcard_button" value="' . esc_attr( $atts['button'] ) . '" title="' . esc_attr( $atts['html_title'] ) . '">
                            </form>
                        </div>';

        } else {

            $html = '<div><strong>Shortcode "um_vcard_button" error:</strong><br />Your ".../wp-content/uploads/ultimatemember/" directory must be writable</div>';
        }

        if ( isset( $_REQUEST['vcard_button'] ) && $_REQUEST['vcard_button'] == 'true' ) {

            $this->create_user_vcard( $atts );
        }

        return $html;
    }

    public function create_user_vcard( $atts ) {

        global $current_user;

        if ( ! empty( $current_user->ID ) && ! empty( um_profile_id() )) {

            UM()->user()->remove_cache( um_profile_id() );
            um_fetch_user( um_profile_id() );

            $languages = ( ! empty( um_user( 'languages' ) && is_array( um_user( 'languages' )))) ? implode( ',', um_user( 'languages' )) : um_user( 'languages' );

            if ( empty( um_user( 'last_name' )) && empty( um_user( 'first_name' ))) {

                if ( ! empty( um_user( 'display_name' )))   {
                    $n = um_user( 'display_name' ) . ';';

                } elseif ( ! empty( um_user( 'user_login' ))) {
                    $n = um_user( 'user_login' ) . ';';

                } else $n = ';';

            } else {

                $n = um_user( 'last_name' ) . ';' . um_user( 'first_name' );
            }

            $gender = strtoupper( substr( um_user( 'gender' ), 0, 1 ));
            $site   = strtoupper( $atts['#place'] );

            $address = esc_attr( um_user( $atts['address'] ));
            if ( empty( $address )) {
                $address = esc_attr( um_user( $atts['street'] )) . ";" .
                           esc_attr( um_user( $atts['city'] )) . ";" .
                           esc_attr( um_user( $atts['state'] )) . ";" .
                           esc_attr( um_user( $atts['zip'] )) . ";" .
                           esc_attr( um_user( 'country' ));
            }

            $vcard  = "BEGIN:VCARD\r\n";
            $vcard .= "VERSION:3.0\r\n";
            $vcard .= "N:" .                   esc_attr( $n ) . ";;;\r\n";
            $vcard .= "FN:" .                  esc_attr( um_user( 'display_name' )) . "\r\n";
            $vcard .= "NICKNAME:" .            esc_attr( um_user( 'user_login' )) . "\r\n";
            $vcard .= "GENDER:" .              esc_attr( $gender ) . "\r\n";
            $vcard .= "NOTE:" .                esc_attr( um_user( 'description' )) . "\r\n";
            $vcard .= "LANG:" .                esc_attr( $languages ) . "\r\n";
            $vcard .= "ORG:" .                 esc_attr( um_user( $atts['org'] )) . "\r\n";
            $vcard .= "TITLE:" .               esc_attr( um_user( $atts['title'] )) . "\r\n";
            $vcard .= "URL:" .                 esc_attr( um_user( $atts['url'] )) . "\r\n";
            $vcard .= "TEL;{$site}:" .         esc_attr( um_user( 'phone_number' )) . "\r\n";
            $vcard .= "TEL;CELL:" .            esc_attr( um_user( 'mobile_number' )) . "\r\n";
            $vcard .= "ADR;{$site}:;;" .       $address . "\r\n";
            $vcard .= "EMAIL;PREF=1:" .        esc_attr( um_user( 'user_email' )) . "\r\n";
            $vcard .= "EMAIL:" .               esc_attr( um_user( 'secondary_user_email' )) . "\r\n";
            $vcard .= "TZ:" .                  esc_attr( um_user( 'timezone' )) . "\r\n";

            $data = um_get_user_avatar_data( um_profile_id() );
            if ( substr( $data['url'], 0, 4 ) != 'http' ) {
                $data['url'] = $data['default'];
            }

            $photo_path = realpath( $_SERVER['DOCUMENT_ROOT'] . parse_url( $data['url'], PHP_URL_PATH ));
            $photo_content = file_get_contents( $photo_path );

            if ( ! empty( $photo_content )) {

                $path_parts = pathinfo( $photo_path );
                $extension = explode( '?', $path_parts['extension'] );
                $extension = strtoupper( $extension[0] );

                if ( $extension == 'JPG' ) {
                     $extension = 'JPEG';
                }

                $photo_base64_encoded = base64_encode( $photo_content );
                $vcard .= "PHOTO;ENCODING=BASE64;TYPE=" . $extension . ":" . $photo_base64_encoded . "\r\n";
            }

            if ( ! empty( um_user( 'birth_date' ) )) {

                $unixtimestamp = strtotime( um_user( 'birth_date' ) );
                if ( ! empty( $unixtimestamp )) {
                    $birth_date = wp_date( 'Ymd', $unixtimestamp );
                    $vcard .= "BDAY:" . esc_attr( $birth_date ) . "\r\n";
                }
            }

            $last_update = ( ! empty( um_user( $atts['revision'] ))) ? um_user( $atts['revision'] ) : strtotime( um_user( 'user_registered' ));

            $vcard .= "REV:" . esc_attr( wp_date( 'Ymd', $last_update ) . 'T' . $last_update  . "Z" ) . "\r\n";
            $vcard .= "PROFILE:VCARD\r\n";
            $vcard .= "PRODID:" . esc_attr( get_bloginfo( 'name' )) . "\r\n";
            $vcard .= "NOTE;CHARSET=UTF8;ENCODING=QUOTED-PRINTABLE:\r\n";
            $vcard .= "END:VCARD\r\n";

            file_put_contents( WP_CONTENT_DIR . '/uploads/ultimatemember/' . um_profile_id() . '/vcard.vcf', $vcard );

            if ( $atts['vcard'] == 'download' ) {

                $url_vcard_user = esc_url(( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . '/wp-content/uploads/ultimatemember/' . um_profile_id()  . '/' );
                exit( wp_redirect( $url_vcard_user . 'vcard.vcf' ));
            }

            if ( $atts['vcard'] == 'email' ) {

                $user_email = $current_user->user_email;
                $subject = sprintf( esc_html__( 'vCard %s', 'ultimate-member' ), str_replace( ';', ' ', $n ));
                $message = sprintf( esc_html__( "Attachment with vCard for the user %s", 'ultimate-member' ), str_replace( ';', ' ', $n )) . 
                                        '<br><br>' . sprintf( esc_html__( 'Your %s team.', 'ultimate-member' ), get_bloginfo( 'name' ));
                $headers = array( 'Content-Type: text/html; charset=UTF-8',
                                  'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>' );
                $attachments = array( WP_CONTENT_DIR . '/uploads/ultimatemember/' . um_profile_id()  . '/vcard.vcf' );

                wp_mail( $user_email, $subject, $message, $headers, $attachments );
            }
        }
    }

}

new UM_vCard_Shortcode_Button();
