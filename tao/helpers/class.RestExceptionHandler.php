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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

class tao_helpers_RestExceptionHandler extends common_exception_ExceptionHandler
{
	const realm = GENERIS_INSTANCE_NAME;

    public function handle(common_Exception $exception)
	{
		switch (get_class($exception)) {

			case "common_exception_BadRequest":
				header("HTTP/1.0 400 Bad Request");
				break;

			case "common_exception_MissingParameter":
				header("HTTP/1.0 400 Bad Request");
				break;

			case "common_exception_InvalidArgumentType":
				header("HTTP/1.0 400 Bad Request");
				break;

			case "common_exception_Unauthorized":
				header("HTTP/1.0 401 Unauthorized");
				break;

			case "common_exception_UnauthorizedAuth":
				header('HTTP/1.1 401 Unauthorized');
				header('WWW-Authenticate: ' .
					'Digest realm="' . self::realm . '",' .
					'qop="auth",nonce="' . uniqid() . '",' .
					'opaque="' . md5(self::realm) . '"'
				);
				break;

			case "common_exception_UnauthorizedBasic":
				header('HTTP/1.0 401 Unauthorized');
				header('WWW-Authenticate: Basic realm="' . self::realm . '"');
				break;

			case "common_exception_NotFound":
				header("HTTP/1.0 404 Not Found");
				break;

			case "common_exception_MethodNotAllowed":
				header("HTTP/1.0 405 Not Found");
				break;

			case "common_exception_NotAcceptable":
				header("HTTP/1.0 406 Not Acceptable");
				break;

			case "common_exception_TimeOut":
				header("HTTP/1.0 408 Request Timeout");
				break;

			case "common_exception_Conflict":
				header("HTTP/1.0 409 Conflict");
				break;

			case "common_exception_UnsupportedMediaType":
				header("HTTP/1.0 415 Unsupported Media Type");
				break;

			case "common_exception_NotImplemented":
				header("HTTP/1.0 501 Not Implemented" );
				break;

			case "common_exception_PreConditionFailure":
				header("HTTP/1.0 412 Precondition Failed");
				break;

			case "common_exception_NoContent":
				header("HTTP/1.0 204 No Content" );
				break;

			case "common_exception_teapotAprilFirst":
				header("HTTP/1.0 418 I'm a teapot (RFC 2324)");
				break;

			default:
				header("HTTP/1.0 500 Internal Server Error");

		}
    }
}
