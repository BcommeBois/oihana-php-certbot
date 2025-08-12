<?php

namespace oihana\certbot\traits;

/**
 * Initialize all certbot subcommand traits.
 */
trait CertbotTrait
{
    use CertbotCertificatesTrait ,
        CertbotCertOnlyTrait     ,
        CertbotDeleteTrait       ,
        CertbotRenewTrait        ,
        CertbotRevokeTrait       ;

    /**
     * Initializes all the Certbot subcommand options.
     *
     * @param array $init The associative array definition to initialize all commands.
     *
     * @return static
     */
    protected function initializeCertbotOptions( array $init = [] ):static
    {
        return $this->initializeCertbotCertOnlyOptions     ( $init )
                    ->initializeCertbotCertificatesOptions ( $init )
                    ->initializeCertbotDeleteOptions       ( $init )
                    ->initializeCertbotRenewOptions        ( $init )
                    ->initializeCertbotRevokeOptions       ( $init ) ;
    }
}