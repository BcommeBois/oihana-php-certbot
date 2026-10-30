<?php

namespace oihana\certbot\traits;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeInterface;

use oihana\commands\enums\ExitCode;
use oihana\commands\traits\CommandTrait;

use oihana\certbot\entities\CertbotCertificate;
use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotCertificatesOptions;

use ReflectionException;
use function oihana\certbot\helpers\parseCertbotCertificates;

/**
 * Lists the certificates certbot manages, with the `certbot certificates` command, and finds one
 * of them by its name or one of its domains.
 *
 * Example command executed:
 * ```
 * sudo certbot certificates
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - The command runs with sudo.
 */
trait CertbotCertificatesTrait
{
    use CommandTrait ;

    /**
     * Holds the default options for the `certbot certificates` command.
     * These can be preconfigured and reused across multiple invocations.
     *
     * @var CertbotCertificatesOptions|null
     */
    public ?CertbotCertificatesOptions $certbotCertificatesOptions  = null ;

    /**
     * Returns the first certificate whose name, or one of whose domains, is one of the needs.
     *
     * @param null|string|array                      $needs      A single domain/cert-name or an array of them.
     * @param array|CertbotCertificatesOptions|null  $options    Optional certbot options as array or `CertbotCertificatesOptions` instance.
     * @param bool                                   $verbose    If true, outputs the full constructed command to the console.
     * @param bool                                   $silent     Kept for the callers: the list is read from the output, so it is never hidden from the trait.
     * @param bool                                   $dryRun     If true, simulates the execution without actually running the command. Always returns null.
     * @param string|null                            $timezone   The timezone identifier (e.g., 'Europe/Paris'). Defaults to 'Europe/Paris'.
     * @param string|null                            $dateFormat The date format string compatible with DateTime::format(). Defaults to 'Y-m-d\TH:i:s'.
     *
     * @return CertbotCertificate|null The matched certificate or null if not found.
     *
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     *
     * @example
     * ```php
     * $cert = $this->certbotCertificate('example.com');
     * if ( $cert )
     * {
     *     echo $cert->name ;
     * }
     * ```
     */
    public function certbotCertificate
    (
        null|string|array                     $needs      = null ,
        array|CertbotCertificatesOptions|null $options    = null ,
        bool                                  $verbose    = false ,
        bool                                  $silent     = false ,
        bool                                  $dryRun     = false ,
        string|null                           $timezone   = 'Europe/Paris' ,
        string|null                           $dateFormat = 'Y-m-d\TH:i:s'
    )
    : ?CertbotCertificate
    {
        if( $needs === null )
        {
            return null ;
        }

        $needs = (array) $needs ;

        $certificates = $this->certbotCertificates
        (
            $options    ,
            $verbose    ,
            $silent     ,
            $dryRun     ,
            $timezone   ,
            $dateFormat ,
        ) ;

        foreach( $certificates as $certificate )
        {
            $exist = array_any
            (
                $needs ,
                fn( $item ) => $certificate->name === $item
                            || in_array( $item , (array) $certificate->domains , true )
            ) ;

            if ( $exist )
            {
                return $certificate;
            }
        }

        return null;
    }

    /**
     * Check if a certificate exists for the given domain or certificate name.
     *
     * @param null|string|array                      $needs    A single domain/cert-name or an array of them.
     * @param array|CertbotCertificatesOptions|null  $options  Optional certbot options as array or `CertbotCertificatesOptions` instance.
     * @param bool                                   $verbose  If true, outputs the full constructed command to the console.
     * @param bool                                   $silent   Kept for the callers: the list is read from the output, so it is never hidden from the trait.
     * @param bool                                   $dryRun   If true, simulates the execution without actually running the command. Always returns false.
     *
     * @return bool True if the certificate exists, false otherwise.
     *
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     *
     * @example
     * ```php
     * $this->certbotCertificateExists('example.com');  // true if domain found
     * $this->certbotCertificateExists('my-cert-name'); // true if cert-name matches
     * $this->certbotCertificateExists(['example.com', 'foo.org']);  // true if any domain or cert-name is found
     * ```
     */
    public function certbotCertificateExists
    (
        null|string|array                     $needs   = null ,
        array|CertbotCertificatesOptions|null $options = null ,
        bool                                  $verbose = false ,
        bool                                  $silent  = false ,
        bool                                  $dryRun  = false ,
    )
    : bool
    {
        return $this->certbotCertificate( $needs , $options , $verbose , $silent , $dryRun ) != null ;
    }

    /**
     * Checks if the certificate for the given domain or name is valid
     * at a given reference date (or now by default).
     *
     * @param null|string|array|CertbotCertificate   $needs    A CertbotCertificate or a single domain/cert-name or an array of them.
     * @param null|string|DateTimeInterface          $date     Reference date to test against (defaults to now).
     * @param array|CertbotCertificatesOptions|null  $options  Optional certbot options as array or `CertbotCertificatesOptions` instance.
     * @param bool                                   $verbose  Verbose output of the command.
     * @param bool                                   $silent   Kept for the callers: the list is read from the output, so it is never hidden from the trait.
     * @param bool                                   $dryRun   Simulates the command execution.
     * @param string|null                            $timezone The timezone the expiry date is read in.
     *
     * @return bool True if a valid certificate is found, false otherwise.
     *
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     *
     * @example
     * ```php
     * $this->certbotCertificateIsValid('example.com'); // true if valid today
     * $this->certbotCertificateIsValid('example.com', new DateTime('+10 days')); // true if still valid in 10 days
     * ```
     */
    public function certbotCertificateIsValid
    (
        null|string|array|CertbotCertificate  $needs    = null ,
        null|string|DateTimeInterface         $date     = null ,
        array|CertbotCertificatesOptions|null $options  = null ,
        bool                                  $verbose  = false ,
        bool                                  $silent   = false ,
        bool                                  $dryRun   = false ,
        string|null                           $timezone = 'Europe/Paris' ,
    )
    : bool
    {
        $certificate = $needs instanceof CertbotCertificate
                     ? $needs
                     : $this->certbotCertificate( $needs , $options , $verbose , $silent , $dryRun ) ;

        if( $certificate === null )
        {
            return false ;
        }

        return $certificate->isValid( $date , $timezone );
    }

    /**
     * Executes the `certbot certificates` command and reads its output into a list of
     * CertbotCertificate instances.
     *
     * The command runs with `sudo`, with the passed options over the default ones.
     *
     * @param array|CertbotCertificatesOptions|null $options Optional certbot options as array or `CertbotCertificatesOptions` instance.
     * @param bool $verbose If true, outputs the full constructed command to the console.
     * @param bool $silent Kept for the callers: the list is read from the output, so `--quiet`, which would empty it, is never sent.
     * @param bool $dryRun If true, simulates the execution without actually running the command. Always returns an empty list.
     * @param string|null $timezone The timezone identifier (e.g., 'Europe/Paris'). Defaults to 'Europe/Paris'.
     * @param string|null $dateFormat The date format string compatible with DateTime::format(). Defaults to 'Y-m-d\TH:i:s'.
     *
     * @return array<CertbotCertificate> The list of all certificates managed by Certbot; empty when the command fails.
     *
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     * @throws ReflectionException
     *
     * @example
     * ```php
     * $certificates = $this->certbotCertificates();
     *
     * echo json_encode( $certificates , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ;
     * ```
     *
     * @see parseCertbotCertificates
     */
    public function certbotCertificates
    (
        array|CertbotCertificatesOptions|null $options    = null ,
        bool                                  $verbose    = false ,
        bool                                  $silent     = false ,
        bool                                  $dryRun     = false ,
        string|null                           $timezone   = 'Europe/Paris' ,
        string|null                           $dateFormat = 'Y-m-d\TH:i:s'
    )
    :array
    {
        $args = CertbotCertificatesOptions::resolve( $this->certbotCertificatesOptions , $options ) ;

        $process = $this->proc
        (
            command : CertbotSubCommand::CERTIFICATES ,
            args    : (string) $args ,
            verbose : $verbose ,
            sudo    : true ,
            dryRun  : $dryRun
        ) ;

        if( $process->status == ExitCode::SUCCESS )
        {
            return parseCertbotCertificates( $process->output , $timezone , $dateFormat ) ;
        }

        return [] ;
    }

    /**
     * Initializes internal options for the `certbot certificates` command using an optional array.
     *
     * Reads `[ 'certbot' => [ 'certificates' => [ ...options ] ] ]`, else `[ 'certbot' => [ ...options ] ]`,
     * else the array itself.
     *
     * @param array $init Optional associative array of certbot options.
     * @return static Returns the current instance for method chaining.
     */
    public function initializeCertbotCertificatesOptions( array $init = [] ) :static
    {
        $this->certbotCertificatesOptions = new CertbotCertificatesOptions
        (
            $init[ CertbotParam::CERTBOT ][ CertbotParam::CERTIFICATES ]
             ?? $init[ CertbotParam::CERTBOT ]
             ?? $init
        ) ;
        return $this ;
    }
}
