<?php

namespace oihana\certbot\helpers;

use DateInvalidTimeZoneException;
use DateMalformedStringException;

use oihana\certbot\entities\CertbotCertificate;

use function oihana\core\date\formatDateTime;

/**
 * Parse a string expression and returns the list of the certbot certificates.
 *
 * @param string|null $source     The string expression to parse.
 * @param string|null $timezone   The timezone identifier (e.g., 'Europe/Paris'). Defaults to 'Europe/Paris'.
 * @param string|null $dateFormat The date format string compatible with DateTime::format(). Defaults to 'Y-m-d\TH:i:s'.
 *
 * @return array The list of certificates.
 *
 * ```php
 * use oihana\certbot\helpers\parseCertbotCertificates;
 *
 * $source = <<<CERTS
 * Found the following certs:
 * Certificate Name: example.com
 * Serial Number: 1234567890abcdef
 * Key Type: ECDSA
 * Domains: example.com www.example.com
 * Expiry Date: 2025-10-12 10:15:56+00:00 (VALID: 66 days)
 * Certificate Path: /etc/letsencrypt/live/example.com/fullchain.pem
 * Private Key Path: /etc/letsencrypt/live/example.com/privkey.pem
 * Certificate Name: test.com
 * Serial Number: abcdef1234567890
 * Key Type: RSA
 * Domains: test.com
 * Expiry Date: 2025-11-04 17:38:48+00:00 (VALID: 89 days)
 * Certificate Path: /etc/letsencrypt/live/test.com/fullchain.pem
 * Private Key Path: /etc/letsencrypt/live/test.com/privkey.pem
 * CERTS;
 *
 * $certs = parseCertbotCertificates($source);
 *
 * foreach ( $certs as $cert )
 * {
 *     echo $cert->name . ' expires on ' . $cert->expires . PHP_EOL;
 * }
 * ```
 *
 * @throws DateInvalidTimeZoneException
 * @throws DateMalformedStringException
 */
function parseCertbotCertificates
(
    ?string $source     = null ,
    ?string $timezone   = 'Europe/Paris' ,
    ?string $dateFormat = 'Y-m-d\TH:i:s'
)
: array
{
    if ( $source === null || $source === '' )
    {
        return [] ;
    }

    $certificates = [] ;

    $lines = explode(PHP_EOL , $source ) ;

    $current = [] ;

    foreach ( $lines as $line )
    {
        $line = trim( $line );

        if ( str_starts_with( $line , 'Certificate Name:' ) )
        {
            if ( !empty( $current ) )
            {
                $certificates[] = new CertbotCertificate( $current ) ;
                $current        = [] ;
            }
            $current[ CertbotCertificate::NAME ] = trim( substr( $line , strlen('Certificate Name:' ) ) ) ;
        }
        elseif ( str_starts_with( $line , 'Serial Number:' ) )
        {
            $current[ CertbotCertificate::IDENTIFIER ] = trim( substr( $line, strlen('Serial Number:' ) ) ) ;
        }
        elseif ( str_starts_with( $line , 'Key Type:' ) )
        {
            $current[ CertbotCertificate::KEY_TYPE ] = trim(substr( $line , strlen('Key Type:' ) ) ) ;
        }
        else if ( str_starts_with( $line , 'Domains:' ) )
        {
            $current[ CertbotCertificate::DOMAINS ] = preg_split('/\s+/', trim(substr($line, strlen('Domains:'))));
        }
        else if ( str_starts_with( $line , 'Expiry Date:' ) )
        {
            if( preg_match('/Expiry Date:\s+([^(]+)\s+\(([^)]+)\)/' , $line, $matches) )
            {
                $current[ CertbotCertificate::EXPIRES ] = formatDateTime( trim( $matches[ 1 ] ) , $timezone , $dateFormat ) ;

                if ( preg_match('/VALID:\s*(\d+)\s*days/i', $matches[ 2 ], $validDaysMatches ) )
                {
                    $current[ CertbotCertificate::VALID_DAYS ] = (int) $validDaysMatches[ 1 ] ;
                }
            }
        }
        else if ( str_starts_with( $line , 'Certificate Path:' ) )
        {
            $current[ CertbotCertificate::CERTIFICATE_KEY_PATH ] = trim( substr( $line, strlen('Certificate Path:' ) ) ) ;
        }
        else if ( str_starts_with( $line , 'Private Key Path:' ) )
        {
            $current[ CertbotCertificate::PRIVATE_KEY_PATH ] = trim(substr( $line, strlen('Private Key Path:') ) ) ;
        }
    }

    if ( !empty( $current ) )
    {
        $certificates[] = new CertbotCertificate( $current ) ;
    }

    return $certificates ;
}