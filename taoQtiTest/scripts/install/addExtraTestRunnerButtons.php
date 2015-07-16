<?php

/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

$registry = oat\taoQtiTest\models\TestRunnerClientConfigRegistry::getRegistry();

$registry->registerQtiTools('markForReview', array(
    'label' => 'Mark for review',
    'icon' => 'anchor',
    'hook' => 'taoQtiTest/testRunner/actionBar/markForReview'
));

$registry->registerQtiTools('collapseReview', array(
    'title' => 'Show/Hide the review screen',
    'label' => 'Review',
    'icon' => 'mobile-menu',
    'hook' => 'taoQtiTest/testRunner/actionBar/collapseReview',
    'order' => -1
));
