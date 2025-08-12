<?php

namespace oihana\certbot\options\traits;

/**
 * The common Certbot options.
 */
trait CertbotCommonOptions
{
    /**
     * Certificate name to apply. This name is used by Certbot for housekeeping and in file paths; it doesn't affect the content of the
     * certificate itself. To see certificate names, run 'certbot certificates'. When creating a new certificate, specifies the new
     * certificate's name. (default: the first provided domain or the name of an existing certificate on your system for the same domains)
     * @var string|null
     */
    public string|null $certName = null ;

    /**
     * Path to config file (default: /etc/letsencrypt/cli.ini and ~/.config/letsencrypt/cli.ini)
     * @var string|null
     */
    public string|null $config = null ;

    /**
     * After setting up challenges, wait for user input before submitting to CA.
     * When used in combination with the `-v` option, the challenge URLs or FQDNs
     * and their expected return values are shown. (default: False)
     * @var bool|null
     */
    public ?bool $debugChallenges = null ;

    /**
     * Domain names to apply.
     *
     * For multiple domains you can use multiple -d flags or enter a comma separated list of domains as a parameter.
     *
     * The first domain provided will be the subject CN of the certificate,
     * and all domains will be Subject Alternative Names on the certificate.
     *
     * The first domain will also be used in some software user interfaces and as the file paths for the certificate
     * and related material unless otherwise specified or you already have a certificate with the same name.
     *
     * In the case of a name collision it will append a number like 0001 to the file path name. (default: Ask)
     * @var array|string|null
     */
    public array|string|null $domains = null ;

    /**
     * Perform a test run of the client, obtaining test (invalid) certificates but not saving them to disk.
     *
     * This can currently only be used with the 'certonly' and 'renew' subcommands.
     *
     * Note: Although --dry-run tries to avoid making any persistent changes on a system,
     * it is not completely side-effect free: if used with webserver authenticator plugins like apache and nginx,
     * it makes and then reverts temporary config changes in order to obtain test certificates, and reloads webservers
     * to deploy and then roll back those changes.
     *
     * It also calls --pre-hook and --post-hook commands if they are defined
     * because they may be necessary to accurately simulate renewal.
     *
     * --deploy-hook commands are not called. (default: False)
     * @var bool|null
     */
    public ?bool $dryRun = null ;

    /**
     * Key Identifier for External Account Binding (default: None)
     * @var string|null
     */
    public ?string $eabKid = null ;

    /**
     * HMAC key for External Account Binding (default: None)
     * @var string|null
     */
    public ?string $eabHmacKey = null ;

    /**
     * Extra property to indicates if the certbot command are enabled.
     * @var bool|null
     */
    public ?bool $enabled = true ;

    /**
     * Force Certbot to be interactive even if it detects it's not being run in a terminal.
     * This flag cannot be used with the 'renew' subcommand. (default: False)
     * @var bool|null
     */
    public ?bool $forceInteractive = null ;

    /**
     * Show the help message and exit.
     * @var bool|null
     */
    public ?bool $help = null ;

    /**
     * This option specifies how long (in seconds) Certbot will wait for the server to issue a certificate. (default: 90)
     * @var int|null
     */
    public ?int $issuanceTimeout = null ;

    /**
     * Specifies the maximum number of backup logs that should be kept by Certbot's built in log rotation.
     * Setting this flag to 0 disables log rotation entirely, causing Certbot to always append to the same log file.
     * (default: 1000)
     * @var int|null
     */
    public ?int $maxLogBackups ;

    /**
     * Run without ever asking for user input.
     * This may require additional command line flags; the client will try to explain which ones are required
     * if it finds one missing (default: False)
     * @var bool|null
     */
    public ?bool $nonInteractive = null ;

    /**
     * Set the preferred certificate chain.
     *
     * If the CA offers multiple certificate chains, prefer the chain whose topmost certificate was issued
     * from this Subject Common Name. If no match, the default offered chain will be used. (default: None)
     *
     * @var bool|null
     */
    public ?bool $preferredChain = null ;

    /**
     * A sorted, comma delimited list of the preferred challenge to use during authorization with
     * the most preferred challenge listed first (Eg, "dns" or "http,dns").
     *
     * Not all plugins support all challenges. See https://certbot.eff.org/docs/using.html#plugins for details.
     *
     * ACME Challenges are versioned, but if you pick "http" rather than "http-01",
     * Certbot will select the latest version automatically. (default: [])
     *
     * @var bool|null
     */
    public ?bool $preferredChallenges = null ;

    /**
     * Set a custom user agent string for the client.
     *
     * User agent strings allow the CA to collect high level statistics about success rates by OS, plugin and use case, and to know when to deprecate support for past Python versions and flags.
     *
     * If you wish to hide this information from the Let's Encrypt server, set this to "".
     *
     * The flags encoded in the user agent are:
     *   --duplicate,
     *   --force-renew,
     *   --allow-subset-of-names,
     *   -n, and whether any hooks are set.
     *
     * @var string|null
     */
    public ?string $userAgent = null ;

    /**
     * Add a comment to the default user agent string.
     *
     * May be used when repackaging Certbot or calling it from another tool to allow additional statistical
     * data to be collected. Ignored if --user-agent is set. (Example: Foo-Wrapper/1.0) (default: None)
     *
     * @var string|null
     */
    public ?string $userAgentComment = null ;

    /**
     * This flag can be used multiple times to incrementally increase the verbosity of output, e.g. -vvv. (default: 0)
     * @var bool|null
     */
    public ?bool $verbose = null ;
}