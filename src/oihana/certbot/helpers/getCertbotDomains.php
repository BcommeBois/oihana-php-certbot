<?php

namespace oihana\certbot\helpers;

use InvalidArgumentException;

use function oihana\commands\helpers\assertDomain;

/**
 * Returns the list of domains to be passed to Certbot for a Let's Encrypt certificate.
 *
 * @param string|null $domain     The main domain (e.g. "example.com").
 * @param string|null $subdomain  The subdomain (e.g. "www", "test").
 * @param bool        $throw      Whether to throw an exception on invalid domain.
 *
 * @return array<string>|null List of domain names (["example.com", "www.example.com"]) or null if not assertable.
 *
 * @throws InvalidArgumentException if the domain is missing or invalid and $assertable is true.
 */
function getCertbotDomains( ?string $domain, ?string $subdomain, bool $throw = true): ?array
{
    $domain    = strtolower( trim( ( string ) $domain ) );
    $subdomain = $subdomain !== null ? strtolower( trim( $subdomain ) ) : '' ;

    $isValidDomain = assertDomain( $domain , $throw );

    if( !$isValidDomain )
    {
        return null ;
    }

    $domains = [] ;

    if ( $subdomain === 'www' )
    {
        $domains[] = $domain ;
        $domains[] = $subdomain . '.' .$domain ;
    }
    elseif ( $subdomain !== '' )
    {
        $domains[] = $subdomain . '.' .$domain ;
    }
    else
    {
        $domains[] = $domain;
    }

    return $domains;
}