<?php

namespace oihana\certbot\traits;

use oihana\commands\traits\CommandTrait;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotDeleteOptions;
use oihana\certbot\options\CertbotOption;

use RuntimeException;

/**
 * Deletes a certificate from the machine with the `certbot delete` command. The certificate is not
 * revoked: it stays valid at the certificate authority until it expires.
 *
 * Example command executed:
 * ```
 * sudo certbot delete --cert-name example.com
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - The command runs with sudo.
 */
trait CertbotDeleteTrait
{
    use CommandTrait ;

    /**
     * Holds the default options for the `certbot delete` command.
     * These can be preconfigured and reused across multiple invocations.
     *
     * @var CertbotDeleteOptions|null
     */
    public ?CertbotDeleteOptions $certbotDeleteOptions  = null ;

    /**
     * Deletes a certificate from the machine.
     *
     * @param null|string|array               $domains The name of the certificate to delete; of a list, the first.
     * @param array|CertbotDeleteOptions|null $options Optional certbot options as array or `CertbotDeleteOptions` instance.
     * @param bool                            $verbose Verbose output of the command.
     * @param bool                            $silent  Hides the output of the command.
     * @param bool                            $dryRun  Simulates the command execution.
     *
     * @return int Exit code of the executed command. `0` means success.
     *
     * @throws RuntimeException If the command fails and dryRun is disabled.
     *
     * @example
     * ```php
     * $this->certbotDelete( 'example.com' ) ;
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

        $args = CertbotDeleteOptions::resolve
        (
            $this->certbotDeleteOptions , $options ,
            !empty( $domains ) ? [ CertbotOption::CERT_NAME => $domains ] : null ,
        ) ;

        return $this->system
        (
            command : CertbotSubCommand::DELETE ,
            args    : (string) $args ,
            silent  : $silent ,
            verbose : $verbose ,
            sudo    : true ,
            dryRun  : $dryRun
        ) ;
    }

    /**
     * Initializes internal options for the `certbot delete` command using an optional array.
     *
     * Reads `[ 'certbot' => [ 'delete' => [ ...options ] ] ]`, else `[ 'certbot' => [ ...options ] ]`,
     * else the array itself.
     *
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
}
