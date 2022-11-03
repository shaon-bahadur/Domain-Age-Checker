<?php
/**
 * @package TechSpaceHub
 *
 * @copyright (C) 2022 Tech Space Hub.
 * @license GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

$backgroundcolor = $params->get('backgroundcolor');
$subtitletext = $params->get('subtitletext');
$errormessage = $params->get('errormessage');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base( true ).'/modules/mod_domain_age_checker/res/style.css');
$doc->addScript(JURI::base( true ).'/modules/mod_domain_age_checker/res/jquery-3.1.1.min.js');
?>
<div class="mod-domain-age-checker domainAgeCheckWrapper">
	<h3 class="domain-heading-title"><?php echo $subtitletext; ?></h3>
	<div class="domain-age-checker-Wrapper">
		<div class="domain-age-search" id="domain-age-finder" style="background: <?php echo $backgroundcolor; ?>;">
			<input id="domain_age_input" type="text" name="domain_url" placeholder="Enter a domain name">
			<button type="button" id="domain_age_check_landing">
				<img src="<?php echo JURI::base( true ).'/modules/mod_domain_age_checker/'; ?>res/arrow.svg" alt="arrow" class="arrow">
				<img src="<?php echo JURI::base( true ).'/modules/mod_domain_age_checker/'; ?>res/loading.svg" alt="arrow" class="loading" style="display: none;">
			</button>
		</div>
	</div>
	<div class="domainSearchResult"></div>
</div>
<script>
jQuery(document).ready(function () { 
	jQuery(document).on('keypress',function(e) { 
		if(e.which == 13) {
			jQuery(".domainAgeCheckWrapper .domain-age-search #domain_age_check_landing").trigger("click");
		}
	});
	jQuery('.domainAgeCheckWrapper .domain-age-search #domain_age_check_landing').on('click', function () {
		if(jQuery('#domain_age_input').val()==""){
			jQuery('#domain_age_input').css('border', '2px solid red');
			jQuery("#domain_age_input").focus();
			return false;
		}
		var ajax_url = '<?php echo JURI::root(); ?>index.php?option=com_ajax&module=domain_age_checker&method=getData&format=raw';
		jQuery('.domainAgeCheckWrapper .domain-age-search #domain_age_check_landing .arrow').hide();
		jQuery('.domainAgeCheckWrapper .domain-age-search #domain_age_check_landing .loading').show();
		jQuery('.domainAgeCheckWrapper .domainSearchResult').html('<div style="text-align: center;"><img src="<?php echo JURI::base( true ).'/modules/mod_domain_age_checker/'; ?>res/loading-window.svg" style="width: 120px;margin: auto;text-align: center;" /></div>');
		var domain_age_input = jQuery('#domain_age_input').val();
		domain_age_input = domain_from_url(domain_age_input);
		var data = {
			action: '<?php echo JURI::root(); ?>index.php?option=com_ajax&module=domain_age_checker&method=getData&format=raw',
			domain_age_input: domain_age_input,
		};
		jQuery.post(ajax_url, data, function (response) {
			jQuery('.domainAgeCheckWrapper .domain-age-search #domain_age_check_landing .arrow').show();
			jQuery('.domainAgeCheckWrapper .domain-age-search #domain_age_check_landing .loading').hide();
			if (response == "404") {
				jQuery('.domainAgeCheckWrapper .domainSearchResult').html('<span class="errorNotFound">Something went wrong. Please, try again later.</span>');
			} 
			if (response == "notFound") {
				jQuery('.domainAgeCheckWrapper .domainSearchResult').html('<span class="errorNotFound"><?php echo $errormessage; ?></span>');
			}
			else {
				jQuery('.domainAgeCheckWrapper .domainSearchResult').html(response);
				jQuery('#domain_age_input').css('border', '2px solid transparent');
			}
		});
	});
});
function domain_from_url(url) {
    var result
    var match
    if (match = url.match(/^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n\?\=]+)/im)) {
        result = match[1]
        if (match = result.match(/^[^\.]+\.(.+\..+)$/)) {
            result = match[1]
        }
    }
    return result
}
</script>