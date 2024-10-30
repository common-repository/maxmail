<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Maxmail for Wordpress
 * @author    Igor <igor.n@optimizerhq.com>
 * @license   GPL-2.0+
 * @link      http://maxmailhq.com
 * @copyright 2013 Optimizer
 */

$maxmail = $this;
/* @var $maxmail Maxmail */

$subscribed = null;
if(isset($_POST['maxmail_submit'])){
	$subscribed = $maxmail->subscribe($_POST);
}

if($maxmail->isConfigured() && !$maxmail->isMisconfigured()){
	?>
	<div class="maxmail">
	
		<?php if($subscribed === true){ ?>
			<div class="success">Thanks! You are subscribed.</div>
		<?php }else if($subscribed === false){ ?>
			<div class="error">Whoops, something went wrong. Please try again.</div>
		<?php } ?>
		
		<?php if($subscribed !== true){ ?>
			<form class="maxmail-form" method="post">
		
				<?php 
				foreach($maxmail->getFields($maxmail->getListId()) as $name => $spec){ 
					$type = $spec['type'];
					$mandatory = isset($spec['mandatory']) && $spec['mandatory'] == 'yes';
					$values = isset($spec['values']) ? $spec['values'] : array();
					$label = isset($spec['label']) ? $spec['label'] : $name;
					$show = $maxmail->isVisible($name);
					$apiKey = isset($spec['api_key']) ? $spec['api_key'] : $name;
					$value = isset($_POST[$apiKey]) ? $_POST[$apiKey] : '';
					if($mandatory) $show = true;
					if(!$show) continue;
					?>
					<div class="form-field">
						<label class="form-field-label"><?php echo $label?></label>
						<?php
						if($type == 'Text Field'){
							?><input type="text" name="<?php echo $apiKey; ?>" <?php echo $mandatory?'required="required"':''; ?> value="<?php echo $value; ?>" /><?php 
							
						}else if($type == 'Text Area'){
							?><textarea name="<?php echo $apiKey; ?>" <?php echo $mandatory?'required="required"':''; ?>><?php echo $value; ?></textarea><?php
							
						}else if($type == 'Dropdown List'){
							?><select name="<?php echo $apiKey; ?>" <?php echo $mandatory?'required="required"':''; ?>><?php
								if(!$mandatory){
									?><option value=""></option><?php 
								}
								foreach($values as $k => $v){
									?><option <?php echo $k; ?> <?php echo $k==$value?'selected="selected"':''; ?>><?php echo $v; ?></option><?php
								}
							?></select><?php
							
						}else if($type == 'CheckBox'){
							?>
							<div class="checkboxes">
								<?php 
								foreach($values as $k => $v){
									?>
										<div class="checkbox-input-and-label">
											<input type="checkbox" name="<?php echo $apiKey; ?>[]" value="<?php echo $k;?>" id="<?php echo $apiKey; ?>_<?php echo $k; ?>" <?php echo (is_array($value)&&in_array($k, $value))?'checked="checked"':'';?> />
											<label class="checkbox-label" for="<?php echo $apiKey?>_<?php echo $k; ?>"><?php echo $v; ?></label>
										</div>
									<?php
								}
								?>
							</div>
							<?php
						}else if($type == 'Date'){
							?><input type="text" class="date" name="<?php echo $apiKey; ?>" <?php echo $mandatory?'required="required"':''; ?> value="<?php echo $value;?>" /><?php
		
						}
						?>
					</div>
					<?php
				} 
				?>
				
				<div class="form-actions">
					<input type="submit" name="maxmail_submit" value="Subscribe" class="maxmail_submit" />
				</div>
				
			</form>
		<?php } ?>
		
	</div>	
	<?php
}else{
	?>
	<div class="error">
		Error: Maxmail is not <a href="<?php echo admin_url('plugins.php?page=maxmail'); ?>">configured</a>
	</div>
	<?php
}
