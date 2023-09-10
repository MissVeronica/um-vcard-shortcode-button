# UM vCard Shortcode Button
Extension to Ultimate Member to create the User Profile vCard via a shortcode button.

## Shortcode Settings
Shortcode <code>[um_vcard_button]</code>. Add the shortcode to a profile field with your UM Forms Builder.

The shortcode will create a vCard file with meta keys from current User's UM content if values are available:
  
Clicking on the shortcode vCard profile form button can either go to the default <code>vcard="download"</code> of the vCard file: <code>vcard.vcf</code>
or can be changed in the shortcode to <code>vcard="email"</code> when the requesting user will receive an email with the <code>vcard.vcf</code> file attached.

Button text can be set in the shortcode with this example: <code>[um_vcard_button button="Download your vCard file"]</code>.
Button title text can be set in the shortcode with this example: <code>[um_vcard_button html_title="Create the vCard for this user profile"]</code>

## Installation
Download the zip file and install as a new WP Plugin, activate the plugin.
