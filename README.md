# UM vCard Shortcode Button
Extension to Ultimate Member to create the User Profile vCard via a shortcode button.

## Shortcode Settings
Shortcode <code>[um_vcard_button]</code>. Add the shortcode to a profile field with your UM Forms Builder.

The shortcode will create a vCard file with meta keys from current User's UM content if values are available:
  
Clicking on the shortcode vCard profile form button can either go to the default <code>vcard="download"</code> of the vCard file: <code>vcard.vcf</code>
or can be changed in the shortcode to <code>vcard="email"</code> when the requesting user will receive an email with the <code>vcard.vcf</code> file attached.

Button text can be set in the shortcode with this example: <code>[um_vcard_button button="Download your vCard file"]</code>.
Button title text can be set in the shortcode with this example: <code>[um_vcard_button html_title="Create the vCard for this user profile"]</code>

### Default meta_key values which can be changed like [um_vcard_button org="shop-name"]
*    'street'     => 'street'
*    'city'       => 'city'
*    'zip'        => 'zip'
*    'state'      => 'state'
*    'org'        => 'company'
*    'title'      => 'title'
*    'url'        => 'user_url'
*    'revision'   => 'last_update'
*    'vcard'      => 'download'
*    'button'     => 'Download vCard'
*    'html_title' => esc_html__( 'Create the vCard for this user profile', 'ultimate-member' )
*    '#place'     => 'WORK'
  
## Installation & Updates
Download the zip file from the Green button and install as a new WP Plugin, activate the plugin.
