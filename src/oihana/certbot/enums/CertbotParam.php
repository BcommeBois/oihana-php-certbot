<?php

namespace oihana\certbot\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * The parameters of the 'certbot' cli.
 */
class CertbotParam
{
    use ConstantsTrait ;

    public const string CERTBOT      = 'certbot'      ;
    public const string CERTIFICATES = 'certificates' ;
    public const string CERT_ONLY    = 'certOnly'     ;
    public const string DELETE       = 'delete'       ;
    public const string RENEW        = 'renew'        ;
    public const string REVOKE       = 'revoke'       ;

}