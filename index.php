<?php
/**
 * Plugin Name:       CodeSoup ACF Admin Categories
 * Plugin URI:        https://github.com/code-soup/acf-admin-categories
 * Description:       A WordPress plugin that adds category organization to Advanced Custom Fields (ACF) field groups for better management and organization.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Code Soup
 * Author URI:        https://www.codesoup.co
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       codesoup-acf-admin-categories
 * Domain Path:       /languages
 * Network:           false
 * Update URI:        https://github.com/code-soup/acf-admin-categories
 *
 * @package           CodeSoup\ACFAdminCategories
 * @author            Code Soup <hi@codesoup.co>
 * @copyright         2025 Code Soup
 * @license           GPL-3.0-or-later
 * @link              https://github.com/code-soup/acf-admin-categories
 *
 * ACF Admin Categories is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * CodeSoup ACF Admin Categories is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ACF Admin Categories. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */

defined( 'WPINC' ) || die;

// No activation/deactivation hooks needed - plugin doesn't register custom post types or rewrite rules.

require 'run.php';
