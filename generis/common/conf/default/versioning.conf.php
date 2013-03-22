<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
/**
 * versioning config
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 * @subpackage conf
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

define('GENERIS_VERSIONING_ENABLED', false);
define('GENERIS_VERSIONED_REPOSITORY_LOGIN' , '');
define('GENERIS_VERSIONED_REPOSITORY_PASSWORD' , '');
define('GENERIS_VERSIONED_REPOSITORY_TYPE' , '');
define('GENERIS_VERSIONED_REPOSITORY_URL' , '');
define('GENERIS_VERSIONED_REPOSITORY_PATH' , GENERIS_FILES_PATH.'versioning' . DIRECTORY_SEPARATOR . 'DEFAULT' . DIRECTORY_SEPARATOR);
define('GENERIS_VERSIONED_REPOSITORY_LABEL' , 'Tao default versioned repository');
define('GENERIS_VERSIONED_REPOSITORY_COMMENT' , 'The default repository used to manage versioned files');