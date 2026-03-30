<?php

namespace tests\oihana\certbot\helpers;

use InvalidArgumentException;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function oihana\certbot\helpers\getCertbotDomains;

class GetCertbotDomainsTest extends TestCase
{
    #[Test]
    public function returnsDomainOnlyWhenNoSubdomain(): void
    {
        $domains = getCertbotDomains( 'example.com' , null );
        $this->assertSame( [ 'example.com' ] , $domains );
    }

    #[Test]
    public function returnsDomainOnlyWhenSubdomainIsEmpty(): void
    {
        $domains = getCertbotDomains( 'example.com' , '' );
        $this->assertSame( [ 'example.com' ] , $domains );
    }

    #[Test]
    public function returnsDomainAndWwwSubdomain(): void
    {
        $domains = getCertbotDomains( 'example.com' , 'www' );
        $this->assertSame( [ 'example.com' , 'www.example.com' ] , $domains );
    }

    #[Test]
    public function returnsCustomSubdomainOnly(): void
    {
        $domains = getCertbotDomains( 'example.com' , 'api' );
        $this->assertSame( [ 'api.example.com' ] , $domains );
    }

    #[Test]
    public function normalizesToLowerCase(): void
    {
        $domains = getCertbotDomains( 'EXAMPLE.COM' , 'WWW' );
        $this->assertSame( [ 'example.com' , 'www.example.com' ] , $domains );
    }

    #[Test]
    public function trimsWhitespace(): void
    {
        $domains = getCertbotDomains( '  example.com  ' , '  www  ' );
        $this->assertSame( [ 'example.com' , 'www.example.com' ] , $domains );
    }

    #[Test]
    public function throwsOnInvalidDomainByDefault(): void
    {
        $this->expectException( InvalidArgumentException::class );
        getCertbotDomains( '' , null );
    }

    #[Test]
    public function returnsNullOnInvalidDomainWhenThrowIsFalse(): void
    {
        $result = getCertbotDomains( '' , null , false );
        $this->assertNull( $result );
    }

    #[Test]
    public function throwsOnNullDomainByDefault(): void
    {
        $this->expectException( InvalidArgumentException::class );
        getCertbotDomains( null , null );
    }

    #[Test]
    public function returnsNullOnNullDomainWhenThrowIsFalse(): void
    {
        $result = getCertbotDomains( null , null , false );
        $this->assertNull( $result );
    }
}
