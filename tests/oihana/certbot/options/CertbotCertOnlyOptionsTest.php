<?php

declare(strict_types=1);

namespace tests\oihana\certbot\options ;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use oihana\certbot\options\CertbotCertOnlyOptions;

#[CoversClass( CertbotCertOnlyOptions::class )]
final class CertbotCertOnlyOptionsTest extends TestCase
{
    public function testNoOptionGivesAnEmptyString() :void
    {
        $this->assertSame( '' , (string) new CertbotCertOnlyOptions() ) ;
    }

    public function testEnabledNeverReachesTheCommand() :void
    {
        $this->assertSame( '' , (string) new CertbotCertOnlyOptions( [ 'enabled' => true ] ) ) ;
    }

    /**
     * The options without a set place come first; then the plugin, the domains, the agreement, the
     * key size, the email, and last the dry run and quiet flags.
     */
    public function testTheOptionsAreWrittenInTheirOrder() :void
    {
        $options = new CertbotCertOnlyOptions
        ([
            'quiet'       => true ,
            'dryRun'      => true ,
            'email'       => 'contact@example.com' ,
            'rsaKeySize'  => 4096 ,
            'agreeTos'    => true ,
            'domains'     => [ 'example.com' , 'www.example.com' ] ,
            'webrootPath' => '/var/www/acme' ,
            'webroot'     => true ,
            'keyType'     => 'ecdsa' ,
            'certName'    => 'example.com' ,
        ]) ;

        $this->assertSame
        (
            '--cert-name "example.com" --key-type "ecdsa" --webroot -w "/var/www/acme" -d "example.com" -d "www.example.com" '
            . '--agree-tos --rsa-key-size 4096 -m "contact@example.com" --dry-run -q' ,
            (string) $options
        ) ;
    }
}
