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

use RuntimeException;

/**
 * Renews certificates with the `certbot renew` command.
 *
 * The 'renew' subcommand renews the certificates previously obtained that are close to expiry —
 * all of them, or, with `--cert-name`, the one named — and prints a summary of the results.
 *
 * By default, 'renew' will reuse the plugins and options used to obtain or most recently renew each certificate.
 *
 * You can test whether future renewals will succeed with `--dry-run`.
 *
 * Hooks are available to run commands before and after renewal.
 *
 * Example command executed:
 * ```
 * sudo certbot renew --cert-name example.com
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - The command runs with sudo.
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
     * Renews the certificate named, or, without a name, every certificate close to its expiry.
     *
     * @param null|string|array              $domains    The name of the certificate to renew; of a list, the first. Without it, every certificate is considered.
     * @param array|CertbotRenewOptions|null $options    Optional certbot options as array or `CertbotRenewOptions` instance.
     * @param bool                           $verbose    Verbose output of the command.
     * @param bool                           $silent     Hides the output of the command.
     * @param bool                           $assertable If true and a name is given, refuses a name certbot does not know, before renewing anything.
     * @param bool                           $dryRun     Simulates the command execution.
     *
     * @return int Exit code of the executed command. `0` means success.
     *
     * @throws ValidationException If the certificate is asserted and does not exist.
     * @throws RuntimeException If the command fails and dryRun is disabled.
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
            throw new ValidationException( 'The certificate ' . $domains . ' does not exist.' ) ;
        }

        $args = CertbotRenewOptions::resolve
        (
            $this->certbotRenewOptions ,
            $options ,
            !empty( $domains ) ? [ CertbotOption::CERT_NAME => $domains ] : null ,
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

    /**
     * Initializes internal options for the `certbot renew` command using an optional array.
     *
     * Reads `[ 'certbot' => [ 'renew' => [ ...options ] ] ]`, else `[ 'certbot' => [ ...options ] ]`,
     * else the array itself.
     *
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
}
