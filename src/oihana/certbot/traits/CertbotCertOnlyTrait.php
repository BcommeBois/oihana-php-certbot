<?php

namespace oihana\certbot\traits;

use oihana\commands\traits\CommandTrait;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotCertOnlyOptions;

use RuntimeException;

/**
 * Obtains a certificate with the `certbot certonly` command, without installing it, typically in
 * webroot mode.
 *
 * Example command executed:
 * ```
 * sudo certbot certonly --webroot -w /var/www/site -d example.com -d www.example.com --agree-tos -m contact@example.com
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - The webroot path must be served for every domain asked for.
 * - The command runs with sudo.
 */
trait CertbotCertOnlyTrait
{
    use CommandTrait ;

    /**
     * Holds the default options for the `certbot certonly` command.
     * These can be preconfigured and reused across multiple invocations.
     *
     * @var CertbotCertOnlyOptions|null
     */
    public ?CertbotCertOnlyOptions $certbotCertOnlyOptions  = null ;

    /**
     * Executes the `certbot certonly` command, with `sudo`, with the passed options over the
     * default ones.
     *
     * @param array|CertbotCertOnlyOptions|null $options Optional certbot options as array or `CertbotCertOnlyOptions` instance.
     * @param bool                              $verbose If true, outputs the full constructed command to the console.
     * @param bool                              $silent  If true, hides the output of the command.
     * @param bool                              $dryRun  If true, simulates the execution without actually running the command. Always returns 0.
     *
     * @return int Exit code of the executed command. `0` means success.
     *
     * @throws RuntimeException If the command fails (non-zero exit code) and dryRun is disabled.
     *
     * @example
     * ```php
     * $this->certbotCertOnly
     * ([
     *     'webroot'     => true ,
     *     'webrootPath' => '/var/www/site',
     *     'domains'     => ['example.com', 'www.example.com'],
     *     'email'       => 'contact@example.com',
     *     'agreeTos'    => true,
     * ]);
     * ```
     *
     * @see getCertbotDomains
     */
    public function certbotCertOnly
    (
        array|CertbotCertOnlyOptions|null $options = null ,
        bool                              $verbose = false ,
        bool                              $silent  = false ,
        bool                              $dryRun  = false ,
    ):int
    {
        $args = CertbotCertOnlyOptions::resolve( $this->certbotCertOnlyOptions , $options ) ;
        return $this->system
        (
            command : CertbotSubCommand::CERT_ONLY ,
            args    : (string) $args ,
            silent  : $silent ,
            verbose : $verbose ,
            sudo    : true ,
            dryRun  : $dryRun
        ) ;
    }

    /**
     * Initializes internal options for the `certbot certonly` command using an optional array.
     *
     * Accepts a flat array or a nested structure using:
     * ```php
     * [ 'certbot' => [ 'certOnly' => [ ...options ] ] ]
     * ```
     * or
     * ```php
     * [ 'certbot' => [ ...options ] ]
     * ```
     * or
     * ```php
     * [ ...options ]
     * ```
     *
     * @param array $init Optional associative array of certbot options.
     * @return static Returns the current instance for method chaining.
     *
     * @example
     * ```php
     * $this->initializeCertbotCertOnlyOptions
     * ([
     *     'webrootPath' => '/var/www/site',
     *     'domains' => ['example.com', 'www.example.com'],
     *     'email' => 'contact@example.com',
     * ]);
     * ```
     */
    public function initializeCertbotCertOnlyOptions( array $init = [] ) :static
    {
        $this->certbotCertOnlyOptions = new CertbotCertOnlyOptions
        (
           $init[ CertbotParam::CERTBOT ][ CertbotParam::CERT_ONLY ]
            ?? $init[ CertbotParam::CERTBOT ]
            ?? $init
        ) ;
        return $this ;
    }
}
