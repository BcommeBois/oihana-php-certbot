<?php

namespace oihana\certbot\traits;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotRevokeOptions;
use oihana\certbot\options\CertbotOption;

use oihana\enums\Char;

// sudo certbot revoke $CERTDOM --cert-path /etc/letsencrypt/live/$CERT/fullchain.pem --quiet
//
//     # Remove folders and files
//     rm -rf /etc/letsencrypt/archive/$CERT
//     rm -rf /etc/letsencrypt/live/$CERT
//     rm -rf /etc/letsencrypt/renewal/$CERT.conf

/**
 * Provides support for revoke SSL certificates using the `certbot revoke` command,
 *
 * The 'revoke' subcommand will attempt to revoke any certificates previously obtained.
 *
 * Example command executed:
 * ```
 * sudo certbot revoke --cert-name example.com
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 */
trait CertbotRevokeTrait
{
    use CertbotCertificatesTrait ;

    /**
     * Holds the default options for the `certbot revoke` command.
     * These can be preconfigured and reused across multiple invocations.
     *
     * @var CertbotRevokeOptions|null
     */
    public ?CertbotRevokeOptions $certbotRevokeOptions  = null ;

    /**
     * Initializes internal options for the `certbot revoke` command using an optional array.
     * @param array $init Optional associative array of certbot options.
     * @return static Returns the current instance for method chaining.
     */
    public function initializeCertbotRevokeOptions( array $init = [] ) :static
    {
        $this->certbotRevokeOptions = new CertbotRevokeOptions
        (
            $init[ CertbotParam::CERTBOT ][ CertbotParam::REVOKE ]
            ?? $init[ CertbotParam::CERTBOT ]
            ?? $init
        ) ;
        return $this ;
    }

    /**
     * Attempt to renew any certificates previously obtained.
     *
     * @param null|string|array                $domains     Optional name of the certificate to renew.
     * @param ?bool                            $deleteAfter Enforce to delete or not-delete the certificate after revoking them. If null, the default behavior is applied.
     * @param array|CertbotRevokeOptions|null  $options     Optional certbot options as array or `CertbotRevokeOptions` instance.
     * @param bool                             $verbose     Verbose output of the command.
     * @param bool                             $silent      Suppresses command output.
     * @param bool                             $dryRun      Simulates the command execution.
     *
     * @return int Exit code of the executed command. `0` means success.
     *
     * @example
     * ```php
     * $this->certbotRenew( 'example.com' ) ;
     * ```
     */
    public function certbotRevoke
    (
        null|string|array               $domains     = null  ,
        ?bool                           $deleteAfter = null ,
        array|CertbotRevokeOptions|null $options     = null ,
        bool                            $verbose     = false ,
        bool                            $silent      = false ,
        bool                            $dryRun      = false ,
    )
    :int
    {
        if( is_array( $domains ) )
        {
            $domains = $domains[0] ?? null ;
        }
        else if( is_string( $domains ) && $domains == Char::EMPTY )
        {
            $domains = null ;
        }

        $args = CertbotRevokeOptions::resolve
        (
            $this->certbotRevokeOptions , $options ,
            !empty( $domains )     ? [ CertbotOption::CERT_NAME => $domains          ] : null ,
            $silent                ? [ CertbotOption::QUIET => true                  ] : null ,
            $deleteAfter === true  ? [ CertbotOption::DELETE_AFTER_REVOKE    => true ] : null ,
            $deleteAfter === false ? [ CertbotOption::NO_DELETE_AFTER_REVOKE => true ] : null ,
        ) ;

        return $this->system
        (
            command : CertbotSubCommand::REVOKE ,
            args    : (string) $args ,
            silent  : $silent ,
            verbose : $verbose ,
            sudo    : true ,
            dryRun  : $dryRun
        ) ;
    }
}