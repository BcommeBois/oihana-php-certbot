<?php

namespace oihana\certbot\options;

use oihana\certbot\options\traits\CertbotCommonOptions;
use oihana\options\Options;

use ReflectionException;

/**
 * The default 'certbot' options definition.
 */
class CertbotOptions extends Options
{
    use CertbotCommonOptions ;

    /**
     * Returns a string representation of the options.
     *
     * @return string  Returns the full options expression.
     *
     * @throws ReflectionException
     */
    public function __toString() : string
    {
        return $this->getOptions
        (
            clazz    : CertbotOption::class ,
            excludes : [ CertbotOption::ENABLED ]
        ) ;
    }
}