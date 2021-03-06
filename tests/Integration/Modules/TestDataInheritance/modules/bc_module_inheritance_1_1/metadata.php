<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'bc_module_inheritance_1_1', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 1.1',
    'description'  => 'Both module class and shop class use the old notation without namespaces',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_1_module_1_myclass' => 'bc_module_inheritance_1_1/vendor_1_module_1_myclass.php',
        'vendor_1_module_1_anotherclass' => 'bc_module_inheritance_1_1/vendor_1_module_1_anotherclass.php',
        'vendor_1_module_1_onemoreclass' => 'bc_module_inheritance_1_1/vendor_1_module_1_onemoreclass.php'
    )
);
