<?php

namespace oihana\certbot\options;

/**
 * The 'certbot revoke' options.
 *
 * The 'revoke' subcommand will attempt to revoke any certificates previously obtained.
 *
 * Individual certificates can be renewed with the `--cert-name` option.
 */
class CertbotRevokeOptions extends CertbotOptions
{
    /**
     * Path to where certificate is saved (with certonly --csr), installed from, or revoked (default: None)
     * @var string|null
     */
    public ?string $certPath = null ;

    /**
     * Delete certificates after revoking them, along with all previous and later versions of those certificates.
     *
     * @var bool|null
     * @default false
     */
    public bool|null $deleteAfterRevoke = null ;

    /**
     * Path to private key for certificate installation or revocation (if account key is missing) (default: None)
     * @var string|null
     */
    public ?string $keyPath = null ;

    /**
     * Do not delete certificates after revoking them.
     *
     * This option should be used with caution because the 'renew' subcommand
     * will attempt to renew undeleted revoked certificates. (default: None)
     * @var bool|null
     */
    public bool|null $noDeleteAfterRevoke ;

    /**
     * Specify reason for revoking certificate. (default: unspecified)
     * @var string|null
     */
    public ?string $reason ;

    /**
     * Use the staging server to obtain or revoke test (invalid) certificates;
     * equivalent to --server https://acme-staging-v02.api.letsencrypt.org/directory (default: False)
     * @var bool|null
     */
    public ?bool $staging ;

    /**
     * Use the staging server to obtain or revoke test (invalid) certificates;
     * equivalent to --server https://acme-staging-v02.api.letsencrypt.org/directory (default: False)
     * @var bool|null
     */
    public ?bool $testCert ;
}