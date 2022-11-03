<?php
/**
 * @package TechSpaceHub
 *
 * @copyright (C) 2022 Tech Space Hub.
 * @license GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

require_once dirname(__FILE__) . '/helper.php';

require ModuleHelper::getLayoutPath('mod_domain_age_checker', $params->get('layout', 'default'));
