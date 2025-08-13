<?php

namespace oihana\certbot\traits;

use DateInvalidTimeZoneException;
use DateMalformedStringException;

use oihana\enums\Char;

use oihana\exceptions\ValidationException;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotRenewOptions;
use oihana\certbot\options\CertbotOption;

/**
 * Provides support for managing SSL certificates using the `certbot renew` command,
 *
 * The 'renew' subcommand will attempt to renew any certificates previously obtained if they are close to expiry,
 * and print a summary of the results.
 *
 * By default, 'renew' will reuse the plugins and options used to obtain or most recently renew each certificate.
 *
 * You can test whether future renewals will succeed with `--dry-run`.
 *
 * Individual certificates can be renewed with the `--cert-name` option.
 *
 * Hooks are available to run commands before and after renewal;
 *
 * Example command executed:
 * ```
 * sudo certbot renew --quiet
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - Webroot path must be accessible and correspond to the domain DNS.
 *
 * @see https://certbot.eff.org/docs/using.html#renewal for more information on these.
 */
trait CertbotRenewTrait
{
    use CertbotCertificatesTrait ;

    /**
     * Holds the default options for the `certbot renew` command.
     * These can be preconfigured and reused across multiple invocations.
     *
     * @var CertbotRenewOptions|null
     */
    public ?CertbotRenewOptions $certbotRenewOptions  = null ;

    /**
     * Initializes internal options for the `certbot renew` command using an optional array.
     * @param array $init Optional associative array of certbot options.
     * @return static Returns the current instance for method chaining.
     */
    public function initializeCertbotRenewOptions( array $init = [] ) :static
    {
        $this->certbotRenewOptions = new CertbotRenewOptions
        (
            $init[ CertbotParam::CERTBOT ][ CertbotParam::RENEW ]
            ?? $init[ CertbotParam::CERTBOT ]
            ?? $init
        ) ;
        return $this ;
    }

    /**
     * Attempt to renew any certificates previously obtained if they are close to expiry, and print a summary of the results.
     *
     * @param null|string|array $domains Optional name of the certificate to renew.
     * @param array|CertbotRenewOptions|null $options Optional certbot options as array or `CertbotRenewOptions` instance.
     * @param bool $verbose Verbose output of the command.
     * @param bool $silent Suppresses command output.
     * @param bool $assertable Indicates if the method check if the domain exist - If the $domains is not null (default true).
     * @param bool $dryRun Simulates the command execution.
     *
     * @return int Exit code of the executed command. `0` means success.
     *
     * @throws ValidationException If the domain(s) don't exist.
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     *
     * @example
     * ```php
     * $this->certbotRenew( 'example.com' ) ;
     * ```
     */
    public function certbotRenew
    (
        null|string|array              $domains    = null  ,
        array|CertbotRenewOptions|null $options    = null ,
        bool                           $verbose    = false ,
        bool                           $silent     = false ,
        bool                           $assertable = false ,
        bool                           $dryRun     = false ,
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

        if( $assertable && $domains !== null && !$this->certbotCertificateExists( $domains , silent:true ) )
        {
            throw new ValidationException( 'The domains ' . $domains . ' don\' exist.' ) ;
        }

        $args = CertbotRenewOptions::resolve
        (
            $this->certbotRenewOptions ,
            $options ,
            !empty( $name ) ? [ CertbotOption::CERT_NAME => $domains ] : null ,
            $silent ? [ CertbotOption::QUIET => true ] : null
        ) ;

        return $this->system
        (
            command : CertbotSubCommand::RENEW ,
            args    : (string) $args ,
            silent  : $silent ,
            verbose : $verbose ,
            sudo    : true ,
            dryRun  : $dryRun
        ) ;
    }
}