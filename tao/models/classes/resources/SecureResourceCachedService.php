<?php

declare(strict_types=1);

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
 * Copyright (c) 2013-2020   (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\tao\model\resources;

use common_cache_Cache;
use common_cache_NotFoundException;
use common_exception_Error;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use oat\tao\model\service\InjectionAwareService;

class SecureResourceCachedService extends InjectionAwareService implements SecureResourceServiceInterface
{
    /** @var User */
    private $user;
    /** @var SecureResourceService */
    private $service;
    /** @var string */
    private $cacheServiceId;
    /** @var int */
    private $ttl;
    /** @var common_cache_Cache */
    private $cache;

    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     *
     * @param SecureResourceService $service
     * @param string                $cacheServiceId
     * @param int                   $ttl
     */
    public function __construct(SecureResourceService $service, string $cacheServiceId, ?int $ttl)
    {
        $this->service = $service;
        $this->cacheServiceId = $cacheServiceId;
        $this->ttl = $ttl;
    }

    /**
     * @param core_kernel_classes_Class $resource
     *
     * @return core_kernel_classes_Resource[]
     * @throws common_exception_Error
     * @throws common_cache_NotFoundException
     */
    public function getAllChildren(core_kernel_classes_Class $resource): array
    {
        $user = $this->getUser();

        $cacheKey = $this->getCacheKeyFactory()->create($resource, $user);

        $cache = $this->getCache();
        if ($cache && $cache->has($cacheKey)) {
            return $cache->get($cacheKey)->getInstances();
        }

        $accessibleInstances = $this->getService()->getAllChildren($resource);

        $this->addToCache($cacheKey, new SecureResourceServiceAllChildrenCacheCollection($accessibleInstances));

        return $accessibleInstances;
    }

    /**
     * @inheritDoc
     *
     * @throws common_exception_Error
     * @throws common_cache_NotFoundException
     */
    public function validatePermissions(iterable $resources, array $permissionsToCheck): void
    {
        foreach ($resources as $resource) {
            $this->validatePermission($resource, $permissionsToCheck);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws common_exception_Error
     * @throws common_cache_NotFoundException
     */
    public function validatePermission($resource, array $permissionsToCheck): void
    {
        $user = $this->getUser();

        $resourceUri = $resource instanceof core_kernel_classes_Resource ? $resource->getUri() : $resource;

        $cacheKey = $this->getValidatePermissionCacheKeyFactory()->create($resourceUri, $user);

        $cache = $this->getCache();
        if ($cache && $cache->has($cacheKey)) {
            $hasAccess = $cache->get($cacheKey);

            if (!$hasAccess) {
                throw new ResourceAccessDeniedException($resourceUri);
            }

            return;
        }

        try {
            $this->getService()->validatePermission($resource, $permissionsToCheck);
        } catch (ResourceAccessDeniedException $e) {
            $this->addToCache($cacheKey, false);

            throw $e;
        }

        $this->addToCache($cacheKey, true);
    }

    /**
     * @return SecureResourceService
     */
    public function getService(): SecureResourceService
    {
        $this->service->setServiceLocator($this->getServiceLocator());

        return $this->service;
    }

    private function addToCache(string $cacheKey, $data)
    {
        $cache = $this->getCache();

        if ($cache) {
            $cache->put(
                $data,
                $cacheKey,
                $this->ttl
            );
        }
    }

    private function getCache(): ?common_cache_Cache
    {
        $cacheEnabled = empty(trim($this->cacheServiceId));

        if ($cacheEnabled) {
            return null;
        }

        $this->cache = $this->getServiceLocator()->get($this->cacheServiceId);

        return $this->cache;
    }

    /**
     * @return User
     *
     * @throws common_exception_Error
     */
    private function getUser(): User
    {
        if ($this->user === null) {
            $this->user = $this
                ->getServiceLocator()
                ->get(SessionService::SERVICE_ID)
                ->getCurrentUser();
        }

        return $this->user;
    }

    private function getCacheKeyFactory(): GetAllChildrenCacheKeyFactory
    {
        return $this->getServiceLocator()->get(GetAllChildrenCacheKeyFactory::class);
    }

    private function getValidatePermissionCacheKeyFactory(): ValidatePermissionsCacheKeyFactory
    {
        return $this->getServiceLocator()->get(ValidatePermissionsCacheKeyFactory::class);
    }
}
