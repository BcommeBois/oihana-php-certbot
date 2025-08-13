<?php

namespace oihana\certbot\traits;

use oihana\commands\traits\CommandTrait;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotCertOnlyOptions;

use RuntimeException;

// sudo certbot revoke $CERTDOM --cert-path /etc/letsencrypt/live/$CERT/fullchain.pem --quiet
//
//     # Remove folders and files
//     rm -rf /etc/letsencrypt/archive/$CERT
//     rm -rf /etc/letsencrypt/live/$CERT
//     rm -rf /etc/letsencrypt/renewal/$CERT.conf

/**
 * Provides support for managing SSL certificates using the `certbot certonly` command,
 * typically in webroot mode with sudo privileges. This trait includes initialization
 * logic for certificate generation options and a wrapper for executing the command.
 *
 * Expected use-case:
 * - Automate the generation (or renewal) of Let's Encrypt certificates.
 * - Leverage webroot verification method (using `--webroot -w <path>`).
 * - Use sudo context for system-wide certificate generation.
 *
 * Example command executed:
 * ```
 * sudo certbot certonly --webroot -w /var/www/site -d example.com -d www.example.com --rsa-key-size 4096 --agree-tos --email contact@example.com --quiet
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - Webroot path must be accessible and correspond to the domain DNS.
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
     * Initializes internal options for the `certbot certonly` command using an optional array.
     *
     * Accepts a flat array or a nested structure using:
     * ```php
     * [ 'certbot' => [ 'certonly' => [ ...options ] ] ]
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

    /**
     * Executes the `certbot certonly` command using resolved options.
     *
     * This method builds and runs the certbot command in `certonly` mode with `sudo`,
     * using either the passed options or the internal default ones.
     *
     * If `$dryRun` is enabled in the `system()` call (default true here), the command is
     * *simulated* and not actually executed. Change to `dryRun: false` to perform the operation.
     *
     * @param array|CertbotCertOnlyOptions|null $options Optional certbot options as array or `CertbotCertOnlyOptions` instance.
     * @param bool                              $verbose If true, outputs the full constructed command to the console.
     * @param bool                              $silent  If true, suppresses any output from the command.
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
}