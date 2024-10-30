<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Maxmail for Wordpress
 * @author    Igor <igor.n@optimizerhq.com>
 * @license   GPL-2.0+
 * @link      http://maxmailhq.com
 * @copyright 2013 Optimizer
 */

$maxmail = $this;
/* @var $maxmail Maxmail */


if(count($_POST) > 0){
	if(isset($_POST['credentials-submit'])){
		$maxmail->setAccountEmail($_POST['accountEmail']);
		$maxmail->setApiKey($_POST['apiKey']);
	}
	
	if(isset($_POST['show-submit'])){
		$maxmail->setList($_POST['list-id']);
		foreach($maxmail->getFields($maxmail->getListId()) as $fieldName => $spec){
			$show = false;
			if(isset($spec['mandatory']) && $spec['mandatory'] == 'yes'){
				$show = true;
			}else if(isset($_POST['show']) && isset($_POST['show'][$fieldName])){
				$show = $_POST['show'][$fieldName] == 'on';
			}
			$maxmail->setIsVisible($fieldName, $show);
		}
	}
}



?>
<div class="wrap maxmail">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h3>Credentials</h3>
	<div class="section">
	
		<?php if($maxmail->hasCredentials() && $maxmail->isMisconfigured()){ ?>
			<div class="error">
				<p>Whoops, these credentials are incorrect! Please check.</p>
			</div>
		<?php } ?>
	
		<form method="post" id="credentials-form">
		
			<div class="form-field">
				<label>Maxmail Email</label>
				<input type="text" name="accountEmail" value="<?=$maxmail->getAccountEmail()?>" />
				<p class="description">This is the email address you use to log in to Maxmail</p>
			</div>
		
			<div class="form-field">
				<label>Maxmail API Key</label>
				<input type="text" name="apiKey" value="<?=$maxmail->getApiKey()?>" />
				<p class="description">Find this in your Maxmail login under Settings -> API Credentials</p>
			</div>
			
			<div class="form-actions">
				<input type="submit" class="button button-primary" name="credentials-submit" value="Save" />
			</div>
			
		</form>
	</div>
	
	<h3>Configuration</h3>
	<div class="section">
	
		<form method="post" id="config-form">
		<?php if($maxmail->hasCredentials() && !$maxmail->isMisconfigured()){ 
			?>
		
			<div class="form-field">
				<label>List</label>
				<select name="list-id">
					<?php foreach($maxmail->getLists() as $id => $name){ 
						if($maxmail->getListId() === null) $maxmail->setList($id); // default to first option
						$selected = $maxmail->getListId() == $id;
						?>
						<option value="<?php echo $id;?>" <?php echo ($selected?'selected="selected"':'')?>><?php echo $name;?></option>
					<?php } ?>
				</select>
			</div>
			
			<h4>Which fields to Show</h4>
			<div class="visible-fields">
				<table>
					<thead>
						<tr>
							<th>Field</th>
							<th>Type</th>
							<th>Required</th>
							<th>Show in Subscription Form</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$listId = $maxmail->getListId();
						if(isset($_REQUEST['show_fields_for_list_id'])){
							// help with ajax loading of this form
							$listId = $_REQUEST['show_fields_for_list_id'];
						}
						foreach($maxmail->getFields($listId) as $name => $spec){ 
							$type = $spec['type'];
							$mandatory = isset($spec['mandatory']) && $spec['mandatory'] == 'yes';
							$values = isset($spec['values']) ? $spec['values'] : array();
							$label = isset($spec['label']) ? $spec['label'] : $name;
							$show = $maxmail->isVisible($name);
							if($mandatory) $show = true;
							?>
							<tr>
								<td>
									<?php echo $label; ?>
								</td>
								<td>
									<?php echo $type; ?>
								</td>
								<td>
									<?php echo $mandatory?'Yes':'No'; ?>
								</td>
								<td>
									<input type="checkbox" 
											name="show[<?php echo $name; ?>]" 
											<?php echo $show?'checked="checked"':''; ?> 
											<?php echo $mandatory?'disabled="disabled"':''; ?>
											/>
								</td>
							</tr>
							<?php
						} 
						?>
					</tbody>
				</table>
			</div>
			
			<div class="form-actions">
				<input type="submit" class="button button-primary" name="show-submit" value="Save" />
			</div>
			
		<?php }else{ ?>
			<p class="info">Please Save your Credentials first</p>
			
		<?php } ?>
		</form>
	</div>
	
	<h3>Usage Instructions</h3>
	<div class="section">
		<p>Simply add [maxmail] to any post to display the subscription form.</p>
		<p><a href="http://en.support.wordpress.com/shortcodes/">Help with shortcodes</a></p>
	</div>
	
</div>
