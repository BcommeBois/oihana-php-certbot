<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

use oihana\exceptions\ValidationException;

use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotRenewOptions;
use oihana\certbot\traits\CertbotRenewTrait;

#[CoversTrait( CertbotRenewTrait::class )]
final class CertbotRenewTraitTest extends TestCase
{
    private const string OUTPUT = <<<OUTPUT
    Found the following certs:
      Certificate Name: example.com
        Key Type: ECDSA
        Domains: example.com www.example.com
        Expiry Date: 2025-10-12 10:15:56+00:00 (VALID: 66 days)
    OUTPUT;

    private RecordingCertbot $certbot ;

    protected function setUp() :void
    {
        $this->certbot = new RecordingCertbot() ;
    }

    public function testTheOptionsAreReadFlat() :void
    {
        $this->certbot->initializeCertbotRenewOptions( [ 'forceRenewal' => true ] ) ;

        $this->assertInstanceOf( CertbotRenewOptions::class , $this->certbot->certbotRenewOptions ) ;
        $this->assertTrue( $this->certbot->certbotRenewOptions->forceRenewal ) ;
    }

    public function testTheOptionsAreReadUnderCertbot() :void
    {
        $this->certbot->initializeCertbotRenewOptions( [ 'certbot' => [ 'preHook' => 'shared' ] ] ) ;

        $this->assertSame( 'shared' , $this->certbot->certbotRenewOptions->preHook ) ;
    }

    public function testTheOptionsOfTheCommandWinOverTheSharedOnes() :void
    {
        $this->certbot->initializeCertbotRenewOptions
        ([
            'certbot' => [ 'preHook' => 'shared' , 'renew' => [ 'preHook' => 'own' ] ] ,
        ]) ;

        $this->assertSame( 'own' , $this->certbot->certbotRenewOptions->preHook ) ;
    }

    public function testTheInitializationChains() :void
    {
        $this->assertSame( $this->certbot , $this->certbot->initializeCertbotRenewOptions() ) ;
    }

    /**
     * Without a name, certbot renews every certificate close to its expiry.
     */
    public function testNoNameRenewsEveryCertificate() :void
    {
        $this->assertSame( 0 , $this->certbot->certbotRenew() ) ;

        $this->assertSame
        (
            [ [ 'system' , CertbotSubCommand::RENEW , '' , false , true , false ] ] ,
            $this->certbot->calls
        ) ;
    }

    /**
     * Asked to renew one certificate, certbot is told which one — and renews no other.
     */
    public function testTheCertificateIsRenewedByItsName() :void
    {
        $this->certbot->certbotRenew( 'example.com' ) ;

        $this->assertSame( '--cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testOnlyTheFirstNameOfAListIsKept() :void
    {
        $this->certbot->certbotRenew( [ 'example.com' , 'www.example.com' ] ) ;

        $this->assertSame( '--cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testAnEmptyNameSendsNoCertName() :void
    {
        $this->certbot->certbotRenew( '' ) ;
        $this->certbot->certbotRenew( [] ) ;

        $this->assertSame( [ '' , '' ] , array_column( $this->certbot->calls , 2 ) ) ;
    }

    public function testTheDefaultAndPassedOptionsMeet() :void
    {
        $this->certbot->initializeCertbotRenewOptions( [ 'forceRenewal' => true ] ) ;

        $this->certbot->certbotRenew( 'example.com' , [ 'config' => '/c.ini' ] ) ;

        $this->assertSame( '--force-renewal --cert-name "example.com" -c "/c.ini"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testTheFlagsReachTheCommand() :void
    {
        $this->certbot->certbotRenew( 'example.com' , silent : true , dryRun : true ) ;

        $this->assertSame
        (
            [ 'system' , CertbotSubCommand::RENEW , '--cert-name "example.com"' , true , true , true ] ,
            $this->certbot->calls[ 0 ]
        ) ;
    }

    public function testTheCodeOfTheCommandIsReturned() :void
    {
        $this->certbot->codes = [ 1 ] ;

        $this->assertSame( 1 , $this->certbot->certbotRenew() ) ;
    }

    public function testAnAssertedCertificateThatExistsIsRenewed() :void
    {
        $this->certbot->outputs = [ [ 0 , self::OUTPUT ] ] ;

        $this->certbot->certbotRenew( 'www.example.com' , assertable : true ) ;

        $this->assertSame( [ 'proc' , 'system' ] , array_column( $this->certbot->calls , 0 ) ) ;
        $this->assertSame( '--cert-name "www.example.com"' , $this->certbot->calls[ 1 ][ 2 ] ) ;
    }

    public function testAnAssertedCertificateThatDoesNotExistIsRefused() :void
    {
        $this->certbot->outputs = [ [ 0 , self::OUTPUT ] ] ;

        try
        {
            $this->certbot->certbotRenew( 'unknown.org' , assertable : true ) ;
            $refused = false ;
        }
        catch ( ValidationException $exception )
        {
            $refused = true ;
            $this->assertSame( 'The certificate unknown.org does not exist.' , $exception->getMessage() ) ;
        }

        $this->assertTrue( $refused , 'An unknown certificate is refused.' ) ;
        $this->assertSame( [ 'proc' ] , array_column( $this->certbot->calls , 0 ) , 'Nothing is renewed.' ) ;
    }

    public function testNoNameIsNeverAsserted() :void
    {
        $this->certbot->certbotRenew( assertable : true ) ;
        $this->certbot->certbotRenew( '' , assertable : true ) ;

        $this->assertSame( [ 'system' , 'system' ] , array_column( $this->certbot->calls , 0 ) ) ;
    }

    public function testWithoutAssertionNothingIsLookedUp() :void
    {
        $this->certbot->certbotRenew( 'unknown.org' ) ;

        $this->assertSame( [ 'system' ] , array_column( $this->certbot->calls , 0 ) ) ;
    }
}
