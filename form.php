<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>
<div class="wrap">
  <h2>Change your domain</h2>
  <div class="notice notice-warning"><p><strong>Important:</strong> before you change your domain, please <a href="https://codex.wordpress.org/Backing_Up_Your_Database">back up your database</a>. </div>
  
  <?php 
      if($err['current_domain']==1 ){
          echo('<div class="notice notice-error"><p><strong>Error:</strong> You must fill in your current domain.</div>');
      }

      if($err['new_domain']==1 ){
          echo('<div class="notice notice-error"><p><strong>Error:</strong> You must fill in your new domain.</div>');
      }
  ?>

  <form method="post" action="<?php echo domainChangePage(); ?>" id="domainChange">
    <table class="optiontable form-table">
    	<tr valign="top">
    		<th scope="row"><label for="current_domain">Current domain</label></th>
    		<td>
    			<input name="current_domain" type="text" id="current_domain" value="<?php echo $currentDomain; ?>" size="256" class="regular-text"  placeholder="http://yourdomain.com" />
    		</td>
    	</tr>
    	<tr valign="top">
    		<th scope="row"><label for="new_domain">New domain</label></th>
    		<td>
    			<input name="new_domain" type="text" id="new_domain" value="<?php echo $newDomain; ?>" size="256" class="regular-text"  placeholder="http://yournewdomain.com"  />
    		</td>
    	</tr>
    </table>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Change domain" /></p>
  </form>

</div>