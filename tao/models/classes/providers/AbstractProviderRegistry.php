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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 */

namespace oat\tao\model\providers;

use oat\oatbox\AbstractRegistry;

/**
 * Store the <b>available</b> providers modules, even if not activated, providers have to be registered.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
abstract class AbstractProviderRegistry extends AbstractRegistry
{
    /**
     * Register a provider
     * @param ProviderModule $provider the provider to register
     * @return boolean true if registered
     */
    public function register(ProviderModule $provider)
    {
        if (!is_null($provider) && !empty($provider->getModule())) {

            //encode the provider into an assoc array
            $providerData = $provider->toArray();

            self::getRegistry()->set($provider->getModule(), $providerData);

            return true;
        }
        return false;
    }
}
