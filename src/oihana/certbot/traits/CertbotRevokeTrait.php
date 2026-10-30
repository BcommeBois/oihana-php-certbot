<?php

namespace oihana\certbot\traits;

use oihana\certbot\enums\CertbotParam;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotRevokeOptions;
use oihana\certbot\options\CertbotOption;

use RuntimeException;

/**
 * Revokes a certificate at the certificate authority with the `certbot revoke` command. This
 * cannot be undone: a revoked certificate is refused by browsers from then on. To only remove a
 * certificate from the machine, see CertbotDeleteTrait.
 *
 * Example command executed:
 * ```
 * sudo certbot revoke --cert-name example.com
 * ```
 *
 * Requirements:
 * - Certbot must be installed and available in the system path.
 * - The command runs with sudo.
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
     * Revokes a certificate.
     *
     * @param null|string|array               $domains     The name of the certificate to revoke; of a list, the first.
     * @param ?bool                           $deleteAfter True deletes the certificate from the machine after revoking it, false keeps it; null leaves certbot's own default.
     * @param array|CertbotRevokeOptions|null $options     Optional certbot options as array or `CertbotRevokeOptions` instance.
     * @param bool                            $verbose     Verbose output of the command.
     * @param bool                            $silent      Hides the output of the command.
     * @param bool                            $dryRun      Simulates the command execution.
     *
     * @return int Exit code of the executed command. `0` means success.
     *
     * @throws RuntimeException If the command fails and dryRun is disabled.
     *
     * @example
     * ```php
     * $this->certbotRevoke( 'example.com' , deleteAfter : true ) ;
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

        $args = CertbotRevokeOptions::resolve
        (
            $this->certbotRevokeOptions , $options ,
            !empty( $domains )     ? [ CertbotOption::CERT_NAME              => $domains ] : null ,
            $deleteAfter === true  ? [ CertbotOption::DELETE_AFTER_REVOKE    => true     ] : null ,
            $deleteAfter === false ? [ CertbotOption::NO_DELETE_AFTER_REVOKE => true     ] : null ,
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

    /**
     * Initializes internal options for the `certbot revoke` command using an optional array.
     *
     * Reads `[ 'certbot' => [ 'revoke' => [ ...options ] ] ]`, else `[ 'certbot' => [ ...options ] ]`,
     * else the array itself.
     *
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
}
