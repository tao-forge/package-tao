<?php
/**
 *
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

require_once __DIR__ . '/../vendor/autoload.php';
use \oat\tao\model\websource\FlyTokenWebSource;

//load generis constants
\common_Config::load();

$source = FlyTokenWebSource::createFromUrl();

$path = $source->getFilePathFromUrl();
try {
    $stream = $source->getFileStream($path);
    tao_helpers_Http::returnStream($stream, $source->getMimetype($path));
} catch (\tao_models_classes_FileNotFoundException $e) {
    header("HTTP/1.0 404 Not Found");
}
exit();