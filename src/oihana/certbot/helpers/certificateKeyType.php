<?php

namespace oihana\certbot\helpers;

/**
 * Reads the type of key a certificate was issued for: `ECDSA` or `RSA`.
 *
 * Read from the **public** certificate — the file every visitor of the site receives — and never
 * from the private key. In PHP, through the OpenSSL extension, rather than by running `openssl`:
 * a report over many certificates costs no process.
 *
 * The answer uses the words of the `Key Type:` line of `certbot certificates`, which
 * {@see parseCertbotCertificates()} reads into `CertbotCertificate::$keyType`, so the two can be
 * compared as they are. certbot's own `--key-type` option takes them in lower case (`rsa`,
 * `ecdsa`). certbot issues ECDSA keys by default since its version 2.0, RSA before.
 *
 * @param string $pem A certificate, PEM encoded. In a chain, such as certbot's `fullchain.pem`, the
 *                    first certificate is the one read: the site's own.
 *
 * @return string|null `ECDSA`, `RSA`, or null when the text holds no certificate, or one issued
 *                     for another type of key (Ed25519, DSA, …).
 *
 * @example
 * ```php
 * use function oihana\certbot\helpers\certificateKeyType;
 *
 * certificateKeyType( file_get_contents( '/etc/letsencrypt/live/example.com/fullchain.pem' ) ) ;
 * // 'ECDSA'
 *
 * certificateKeyType( 'not a certificate' ) ; // null
 * ```
 *
 * @package oihana\certbot\helpers
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.0
 */
function certificateKeyType( string $pem ) :?string
{
    $key = @openssl_pkey_get_public( $pem ) ;

    if ( $key === false )
    {
        return null ;
    }

    $details = openssl_pkey_get_details( $key ) ;

    return match ( $details[ 'type' ] ?? null )
    {
        OPENSSL_KEYTYPE_EC  => 'ECDSA' ,
        OPENSSL_KEYTYPE_RSA => 'RSA' ,
        default             => null ,
    } ;
}
