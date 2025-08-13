<?php

namespace oihana\certbot\traits;

use oihana\enums\Char;

use oihana\commands\traits\CommandTrait;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotDeleteOptions;
use oihana\certbot\options\CertbotOption;

/**
 * Provides support for delete a SSL certificates using the `certbot delete` command,
 *
 * Example command executed:
 * ```
 * sudo certbot delete --cert-name example.com
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - The command use sudo
 */
trait CertbotDeleteTrait
{
    use CommandTrait ;

    /**
     * Holds the default options for the `certbot renew` command.
     * These can be preconfigured and reused across multiple invocations.
     *
     * @var CertbotDeleteOptions|null
     */
    public ?CertbotDeleteOptions $certbotDeleteOptions  = null ;

    /**
     * Initializes internal options for the `certbot delete` command using an optional array.
     * @param array $init Optional associative array of certbot options.
     * @return static Returns the current instance for method chaining.
     */
    public function initializeCertbotDeleteOptions( array $init = [] ) :static
    {
        $this->certbotDeleteOptions = new CertbotDeleteOptions
        (
            $init[ CertbotParam::CERTBOT ][ CertbotParam::DELETE ]
             ?? $init[ CertbotParam::CERTBOT ]
             ?? $init
        ) ;
        return $this ;
    }

    /**
     * Attempt to delete a certificate.
     *
     * @param null|string|array                $domains     Optional name of the certificate to renew.
     * @param array|CertbotDeleteOptions|null  $options     Optional certbot options as array or `CertbotRevokeOptions` instance.
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
    public function certbotDelete
    (
        null|string|array               $domains     = null  ,
        array|CertbotDeleteOptions|null $options     = null ,
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

        $args = CertbotDeleteOptions::resolve
        (
            $this->certbotDeleteOptions , $options ,
            !empty( $domains )     ? [ CertbotOption::CERT_NAME => $domains ] : null ,
            $silent                ? [ CertbotOption::QUIET => true         ] : null ,
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