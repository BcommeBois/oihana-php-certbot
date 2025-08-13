<?php

namespace oihana\certbot\options;

use oihana\enums\Char;

use oihana\options\Option;

use function oihana\core\strings\hyphenate;

/**
 * The enumeration of the certbot options properties.
 */
class CertbotOption extends Option
{
    // ------ Extras

    public const string ENABLED = 'enabled' ;

    // ------ Common

    public const string CERT_NAME            = 'certName' ;
    public const string CONFIG               = 'config' ;
    public const string DEBUG_CHALLENGES     = 'debugChallenges' ;
    public const string DOMAINS              = 'domains' ;
    public const string DRY_RUN              = 'dryRun'  ;
    public const string EAB_KID              = 'eabKid'  ;
    public const string EAB_HMAC_KEY         = 'eabHmacKey'  ;
    public const string FORCE_INTERACTIVE    = 'forceInteractive' ;
    public const string HELP                 = 'help' ;
    public const string ISSUANCE_TIMEOUT     = 'issuanceTimeout' ;
    public const string MAX_LOG_BACKUPS      = 'maxLogBackups' ;
    public const string NON_INTERACTIVE      = 'nonInteractive' ;
    public const string PREFERRED_CHAIN      = 'preferredChain' ;
    public const string PREFERRED_CHALLENGES = 'preferredChallenges' ;
    public const string USER_AGENT           = 'userAgent' ;
    public const string USER_AGENT_COMMENT   = 'userAgentComment' ;
    public const string VERBOSE              = 'verbose' ;

    // ------ Automation

    public const string AGREE_TOS              = 'agreeTos' ;
    public const string ALLOW_SUBSET_OF_NAMES  = 'allowSubsetOfNames' ;
    public const string DUPLICATE              = 'duplicate' ;
    public const string EXPAND                 = 'expand' ;
    public const string FORCE_RENEWAL          = 'forceRenewal' ;
    public const string KEEP                   = 'keep' ;
    public const string KEEP_UNTIL_EXPIRING    = 'keepUntilExpiring' ;
    public const string NEW_KEY                = 'newKey' ;
    public const string NO_REUSE_KEY           = 'noReuseKey' ;
    public const string QUIET                  = 'quiet' ;
    public const string REINSTALL              = 'reinstall' ;
    public const string REUSE_KEY              = 'reuseKey' ;
    public const string RENEW_BY_DEFAULT       = 'renewsByDefault' ;
    public const string RENEW_WITH_NEW_DOMAINS = 'renewWithNewDomains' ;
    public const string VERSION                = 'version' ;

    // ------ Certonly

    public const string CSR = 'csr' ;

    // ------ Paths

    public const string CERT_PATH      = 'certPath' ;
    public const string CHAIN_PATH     = 'chainPath' ;
    public const string CONFIG_PATH    = 'configPath' ;
    public const string FULLCHAIN_PATH = 'fullchainPath' ;
    public const string KEY_PATH       = 'keyPath' ;
    public const string LOGS_DIR       = 'logsDir' ;
    public const string SERVER         = 'server' ;
    public const string WORK_DIR       = 'workDir' ;

    // ------ Register

    public const string EMAIL                           = 'email' ;
    public const string EFF_EMAIL                       = 'effEmail'  ;
    public const string NO_EFF_EMAIL                    = 'noEffEmail' ;
    public const string REGISTER_UNSAFELY_WITHOUT_EMAIL = 'registerUnsafelyWithoutEmail' ;

    // ------ Renew

    public const string DEPLOY_HOOK             = 'deployHook' ;
    public const string DISABLE_HOOK_VALIDATION = 'disableHookValidation' ;
    public const string DISABLE_RENEWS_UPDATES  = 'disableRenewsUpdates' ;
    public const string NO_AUTORENEW            = 'noAutorenew' ;
    public const string NO_DIRECTORY_HOOKS      = 'noDirectoryHooks' ;
    public const string PRE_HOOK                = 'preHook' ;
    public const string POST_HOOK               = 'postHook' ;

    // ------ Revoke

    public const string DELETE_AFTER_REVOKE    = 'deleteAfterRevoke' ;
    public const string NO_DELETE_AFTER_REVOKE = 'noDeleteAfterRevoke' ;
    public const string REASON                 = 'reason' ;

    // ------ Rollback

    public const string CHECK_POINTS = 'checkpoints' ;

    // ------ Plugins

    public const string APACHE              = 'apache'          ;
    public const string MANUAL              = 'manual'          ;
    public const string NGINX               = 'nginx'           ;
    public const string STANDALONE          = 'standalone'      ;
    public const string WEBROOT             = 'webroot'         ;
    public const string DNS_CLOUDFLARE      = 'dnsCloudflare'   ;
    public const string DNS_DIGITALOCEAN    = 'dnsDigitalocean' ;
    public const string DNS_DNSIMPLE        = 'dnsDnsimple'     ;
    public const string DNS_DNS_MADE_EASY   = 'dnsDnsmadeeasy'  ;
    public const string DNS_GEHIRN          = 'dnsGehirn'       ;
    public const string DNS_GOOGLE          = 'dnsGoogle'       ;
    public const string DNS_LINODE          = 'dnsLinode'       ;
    public const string DNS_LUA_DNS         = 'dnsLuadns'       ;
    public const string DNS_NS_ONE          = 'dnsNsone'        ;
    public const string DNS_OVH             = 'dnsOvh'          ;
    public const string DNS_RFC2136         = 'dnsRfc2136'      ;
    public const string DNS_ROUTE53         = 'dnsRoute53'      ;
    public const string DNS_SAKURA_CLOUD    = 'dnsSakuracloud'  ;

    public const string AUTHENTICATOR  = 'authenticator' ;
    public const string AUTHENTICATORS = 'authenticators' ;
    public const string CONFIGURATOR   = 'configurator' ;
    public const string INIT           = 'init' ;
    public const string INSTALLER      = 'installer' ;
    public const string PREPARE        = 'prepare' ;

    // ------ Security

    public const string AUTO_HSTS          = 'autoHsts' ;
    public const string ELLIPTIC_CURVE     = 'ellipticCurve' ;
    public const string HSTS               = 'hsts' ;
    public const string KEY_TYPE           = 'keyType' ;
    public const string MUST_STAPLE        = 'mustStaple' ;
    public const string NO_REDIRECT        = 'noRedirect' ;
    public const string REDIRECT           = 'redirect' ;
    public const string RSA_KEY_SIZE       = 'rsaKeySize' ;
    public const string STAPLE_OCSP        = 'stapleOcsp' ;
    public const string STRICT_PERMISSIONS = 'strictPermissions' ;
    public const string UIR                = 'uir' ;

    // ------ Testing

    public const string BREAK_MY_CERTS  = 'breakMyCerts'  ;
    public const string DEBUG           = 'debug'         ;
    public const string HTTP_01_ADDRESS = 'http01Address' ;
    public const string HTTP_01_PORT    = 'http01Port'    ;
    public const string HTTPS_PORT      = 'httpsPort'     ;
    public const string NO_VERIFY_SSL   = 'noVerifySsl'   ;
    public const string TEST_CERT       = 'testCert'      ;
    public const string STAGING         = 'staging'       ;

    // ------ Unregister

    public const string ACCOUNT = 'account' ;

    // ------ Webroot

    public const string WEBROOT_MAP  = 'webrootMap'    ;
    public const string WEBROOT_PATH = 'webrootPath'    ;

    /**
     * Returns the command line option expression from a specific option.
     * @param string $option
     * @return string
     */
    public static function getCommandOption( string $option ):string
    {
        return match( $option )
        {
            self::AUTHENTICATOR   => 'a' ,
            self::CONFIG          => 'c' ,
            self::DOMAINS         => 'd' ,
            self::EMAIL           => 'm' ,
            self::HELP            => 'h' ,
            self::INSTALLER       => 'i' ,
            self::NON_INTERACTIVE => 'n' ,
            self::QUIET           => 'q' ,
            self::VERBOSE         => 'v' ,
            self::WEBROOT_PATH    => 'w' ,
            default               =>  hyphenate( $option )
        };
    }


    /**
     * Returns the prefix from a specific option.
     * @param string $option The name of the option.
     * @return ?string The prefix of the given option.
     */
    public static function getCommandPrefix( string $option ):?string
    {
        return match( $option )
        {
            self::AUTHENTICATOR    ,
            self::CONFIG           ,
            self::DOMAINS          ,
            self::EMAIL            ,
            self::HELP             ,
            self::INSTALLER        ,
            self::NON_INTERACTIVE  ,
            self::QUIET            ,
            self::VERBOSE          ,
            self::WEBROOT_PATH     => Char::HYPHEN ,
            default                => Char::DOUBLE_HYPHEN
        };
    }
}
