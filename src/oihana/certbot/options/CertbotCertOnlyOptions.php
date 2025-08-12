<?php

namespace oihana\certbot\options;

use oihana\certbot\options\traits\CertbotAutomationOptions;
use oihana\certbot\options\traits\CertbotPluginsOptions;
use oihana\certbot\options\traits\CertbotRegisterOptions;
use oihana\certbot\options\traits\CertbotSecurityOptions;
use oihana\certbot\options\traits\CertbotWebrootOptions;

use ReflectionException;

/**
 * The 'certbot certonly' options definition.
 */
class CertbotCertOnlyOptions extends CertbotOptions
{
    use CertbotAutomationOptions ,
        CertbotPluginsOptions    ,
        CertbotRegisterOptions   ,
        CertbotSecurityOptions   ,
        CertbotWebrootOptions    ;

    /**
     * Path to a Certificate Signing Request (CSR) in DER or PEM format.
     * Currently --csr only works with the 'certonly' subcommand. (default: None)
     * @var string|null
     */
    public ?string $csr ;

    /**
     * JSON dictionary mapping domains to webroot paths;
     *
     * this implies -d for each entry. You may need to escape this from your shell.
     *
     * E.g.: --webroot-map '{"eg1.is,m.eg1.is":"/www/eg1/", "eg2.is":"/www/eg2"}'
     * This option is merged with, but takes precedence over, -w / -d entries.
     *
     * At present, if you put webroot-map in a config file, it needs to be on a single line, like: webroot-map = {"example.com":"/var/www"}. (default: {})
     * @return string
     * @throws ReflectionException
     */
    public function __toString() : string
    {
        return $this->getOptions
        (
            clazz    : CertbotOption::class ,
            excludes : [ CertbotOption::ENABLED ] ,
            order    :
            [
                CertbotOption::ENABLED ,
                CertbotOption::WEBROOT ,
                CertbotOption::WEBROOT_PATH ,
                CertbotOption::DOMAINS ,
                CertbotOption::AGREE_TOS ,
                CertbotOption::RSA_KEY_SIZE ,
                CertbotOption::EMAIL ,
                CertbotOption::DRY_RUN ,
                CertbotOption::QUIET ,
            ] ,
            reverseOrder : true
        ) ;
    }
}