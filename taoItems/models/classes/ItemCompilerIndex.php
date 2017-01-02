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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */

namespace oat\taoItems\model;


/**
 * Class ItemCompilerIndex
 * @package oat\taoItems\model
 */
interface ItemCompilerIndex
{
    /**
     * @param string $id
     * @param string $language
     * @param mixed $data
     * @return $this
     */
    public function setItem($id, $language, $data);

    /**
     * @param string $id
     * @param string $language
     * @return mixed
     */
    public function getItem($id, $language);
}
