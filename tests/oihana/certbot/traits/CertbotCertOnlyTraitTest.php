<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotCertOnlyOptions;
use oihana\certbot\traits\CertbotCertOnlyTrait;

#[CoversTrait( CertbotCertOnlyTrait::class )]
final class CertbotCertOnlyTraitTest extends TestCase
{
    private RecordingCertbot $certbot ;

    protected function setUp() :void
    {
        $this->certbot = new RecordingCertbot() ;
    }

    public function testTheOptionsAreReadFlat() :void
    {
        $this->certbot->initializeCertbotCertOnlyOptions( [ 'email' => 'flat@example.com' ] ) ;

        $this->assertInstanceOf( CertbotCertOnlyOptions::class , $this->certbot->certbotCertOnlyOptions ) ;
        $this->assertSame( 'flat@example.com' , $this->certbot->certbotCertOnlyOptions->email ) ;
    }

    public function testTheOptionsAreReadUnderCertbot() :void
    {
        $this->certbot->initializeCertbotCertOnlyOptions( [ 'certbot' => [ 'email' => 'shared@example.com' ] ] ) ;

        $this->assertSame( 'shared@example.com' , $this->certbot->certbotCertOnlyOptions->email ) ;
    }

    public function testTheOptionsOfTheCommandWinOverTheSharedOnes() :void
    {
        $this->certbot->initializeCertbotCertOnlyOptions
        ([
            'certbot' => [ 'email' => 'shared@example.com' , 'certOnly' => [ 'email' => 'own@example.com' ] ] ,
        ]) ;

        $this->assertSame( 'own@example.com' , $this->certbot->certbotCertOnlyOptions->email ) ;
    }

    public function testTheInitializationChains() :void
    {
        $this->assertSame( $this->certbot , $this->certbot->initializeCertbotCertOnlyOptions() ) ;
    }

    public function testTheCommandRunsWithSudo() :void
    {
        $this->assertSame( 0 , $this->certbot->certbotCertOnly( [ 'certName' => 'example.com' ] ) ) ;

        $this->assertSame
        (
            [ [ 'system' , CertbotSubCommand::CERT_ONLY , '--cert-name "example.com"' , false , true , false ] ] ,
            $this->certbot->calls
        ) ;
    }

    /**
     * The default options and the passed ones meet in one command; a passed option wins.
     */
    public function testTheDefaultAndPassedOptionsMeet() :void
    {
        $this->certbot->initializeCertbotCertOnlyOptions
        ([
            'webroot'     => true ,
            'webrootPath' => '/var/www/acme' ,
            'email'       => 'default@example.com' ,
            'agreeTos'    => true ,
        ]) ;

        $this->certbot->certbotCertOnly
        ([
            'certName' => 'example.com' ,
            'domains'  => [ 'example.com' , 'www.example.com' ] ,
            'email'    => 'contact@example.com' ,
        ]) ;

        $this->assertSame
        (
            '--cert-name "example.com" --webroot -w "/var/www/acme" -d "example.com" -d "www.example.com" --agree-tos -m "contact@example.com"' ,
            $this->certbot->calls[ 0 ][ 2 ]
        ) ;
    }

    public function testTheFlagsReachTheCommand() :void
    {
        $this->certbot->certbotCertOnly( silent : true , dryRun : true ) ;

        $this->assertSame( [ 'system' , CertbotSubCommand::CERT_ONLY , '' , true , true , true ] , $this->certbot->calls[ 0 ] ) ;
    }

    public function testTheCodeOfTheCommandIsReturned() :void
    {
        $this->certbot->codes = [ 3 ] ;

        $this->assertSame( 3 , $this->certbot->certbotCertOnly() ) ;
    }
}
