<?php

declare(strict_types=1);

namespace tests\oihana\certbot\options ;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use oihana\certbot\options\CertbotOptions;

#[CoversClass( CertbotOptions::class )]
final class CertbotOptionsTest extends TestCase
{
    public function testNoOptionGivesAnEmptyString() :void
    {
        $this->assertSame( '' , (string) new CertbotOptions() ) ;
    }

    /**
     * `enabled` says whether the library runs certbot at all: it is never an option of certbot.
     */
    public function testEnabledNeverReachesTheCommand() :void
    {
        $this->assertSame( '' , (string) new CertbotOptions( [ 'enabled' => true ] ) ) ;
    }

    public function testTheOptionsAreWrittenAsCertbotReadsThem() :void
    {
        $options = new CertbotOptions
        ([
            'certName'       => 'example.com' ,
            'domains'        => [ 'example.com' , 'www.example.com' ] ,
            'nonInteractive' => true ,
        ]) ;

        $this->assertSame( '--cert-name "example.com" -d "example.com" -d "www.example.com" -n' , (string) $options ) ;
    }
}
