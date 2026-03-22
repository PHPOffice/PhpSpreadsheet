<?php

/**
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace {
    if (!class_exists(DateMalformedStringException::class)) {
        /**
         * Class DateMalformedStringException
         * Polyfill class for the DateMalformedStringException.
         */
        class DateMalformedStringException extends Exception
        {
        }
    }
}
