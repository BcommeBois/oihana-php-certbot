<?php

namespace oihana\certbot\options;

/**
 * The 'certbot renew' options.
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
 * @see https://certbot.eff.org/docs/using.html#renewal for more information on these.
 */
class CertbotRenewOptions extends CertbotOptions
{
    /**
     * When performing domain validation, do not consider it a failure
     * if authorizations can not be obtained for a strict subset of the requested domains.
     *
     * This may be useful for allowing renewals for multiple domains to succeed even
     * if some domains no longer point at this system. This option cannot be used with --csr.
     *
     * @var bool|null
     * @default false
     */
    public bool|null $allowSubsetOfNames = null ;

    /**
     * Command to be run in a shell once for each successfully issued certificate.
     *
     * For this command, the shell variable $RENEWED_LINEAGE will point to the config live subdirectory
     * (for example, "/etc/letsencrypt/live/example.com") containing the new certificates and keys;
     *
     * the shell variable $RENEWED_DOMAINS will contain a space-delimited list of renewed certificate domains
     * (for example,"example.com www.example.com")
     *
     * @var string|null
     * @default None
     */
    public ?string $deployHook ;

    /**
     * Ordinarily the commands specified for --pre-hook/--post-hook/--deploy-hook will be checked for validity,
     * to see if the programs being run are in the $PATH, so that mistakes can be caught early,
     * even when the hooks aren't being run just yet.
     *
     * The validation is rather simplistic and fails if you use more advanced shell constructs,
     * so you can use this switch to disable it.
     *
     * @var bool|null
     * @default false
     */
    public ?bool $disableHookValidation ;

    /**
     * Disable automatic updates to your server configuration that would otherwise
     * be done by the selected installer plugin, and triggered when the user executes "certbot renew",
     * regardless of if the certificate is renewed.
     *
     * This setting does not apply to important TLS configuration updates.e)
     *
     * @var bool|null
     * @default false
     */
    public ?bool $disableRenewsUpdates ;

    /**
     * If a certificate already exists for the requested domains, renew it now,
     * regardless of whether it is near expiry. (Often --keep-until-expiring is more appropriate).
     * Also implies --expand. (default: False)
     * @var bool|null
     */
    public ?bool $forceRenewal = null ;

    /**
     * Disable auto renewal of certificates.
     *
     * @var bool|null
     * @default false
     */
    public ?bool $noAutorenew ;

    /**
     * Disable running executables found in Certbot's hook directories during renewal. (default: False)
     *
     * @var bool|null
     *
     * @default false
     */
    public ?bool $noDirectoryHooks ;

    /**
     * Command to be run in a shell before obtaining any certificates.
     *
     * Intended primarily for renewal, where it can be used
     * to temporarily shut down a webserver that might conflict with the standalone plugin.
     *
     * This will only be called if a certificate is actually to be obtained/renewed.
     * When renewing several certificates that have identical pre-hooks, only the first will be executed.
     *
     * @default None
     * @var string|null
     */
    public ?string $preHook ;

    /**
     * Command to be run in a shell after attempting to obtain/renew certificates.
     *
     * Can be used to deploy renewed certificates, or to restart any servers that were stopped by --pre-hook.
     *
     * This is only run if an attempt was made to obtain/renew a certificate.
     * If multiple renewed certificates have identical post-hooks, only one will be run.
     *
     * @default None
     * @var string|null
     */
    public ?string $postHook ;
}