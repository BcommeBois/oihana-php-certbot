<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotDeleteOptions;
use oihana\certbot\traits\CertbotDeleteTrait;

#[CoversTrait( CertbotDeleteTrait::class )]
final class CertbotDeleteTraitTest extends TestCase
{
    private RecordingCertbot $certbot ;

    protected function setUp() :void
    {
        $this->certbot = new RecordingCertbot() ;
    }

    public function testTheOptionsAreReadFlat() :void
    {
        $this->certbot->initializeCertbotDeleteOptions( [ 'config' => '/flat.ini' ] ) ;

        $this->assertInstanceOf( CertbotDeleteOptions::class , $this->certbot->certbotDeleteOptions ) ;
        $this->assertSame( '/flat.ini' , $this->certbot->certbotDeleteOptions->config ) ;
    }

    public function testTheOptionsAreReadUnderCertbot() :void
    {
        $this->certbot->initializeCertbotDeleteOptions( [ 'certbot' => [ 'config' => '/shared.ini' ] ] ) ;

        $this->assertSame( '/shared.ini' , $this->certbot->certbotDeleteOptions->config ) ;
    }

    public function testTheOptionsOfTheCommandWinOverTheSharedOnes() :void
    {
        $this->certbot->initializeCertbotDeleteOptions
        ([
            'certbot' => [ 'config' => '/shared.ini' , 'delete' => [ 'config' => '/own.ini' ] ] ,
        ]) ;

        $this->assertSame( '/own.ini' , $this->certbot->certbotDeleteOptions->config ) ;
    }

    public function testTheInitializationChains() :void
    {
        $this->assertSame( $this->certbot , $this->certbot->initializeCertbotDeleteOptions() ) ;
    }

    public function testTheCertificateIsDeletedByItsName() :void
    {
        $this->assertSame( 0 , $this->certbot->certbotDelete( 'example.com' ) ) ;

        $this->assertSame
        (
            [ [ 'system' , CertbotSubCommand::DELETE , '--cert-name "example.com"' , false , true , false ] ] ,
            $this->certbot->calls
        ) ;
    }

    public function testOnlyTheFirstNameOfAListIsKept() :void
    {
        $this->certbot->certbotDelete( [ 'example.com' , 'www.example.com' ] ) ;

        $this->assertSame( '--cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testNoNameSendsNoCertName() :void
    {
        $this->certbot->certbotDelete( '' ) ;
        $this->certbot->certbotDelete( [] ) ;
        $this->certbot->certbotDelete() ;

        $this->assertSame( [ '' , '' , '' ] , array_column( $this->certbot->calls , 2 ) ) ;
    }

    public function testTheNamePassedWinsOverTheOptions() :void
    {
        $this->certbot->initializeCertbotDeleteOptions( [ 'certName' => 'default.com' ] ) ;

        $this->certbot->certbotDelete( 'example.com' , [ 'config' => '/c.ini' ] ) ;

        $this->assertSame( '--cert-name "example.com" -c "/c.ini"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testTheFlagsReachTheCommand() :void
    {
        $this->certbot->certbotDelete( 'example.com' , silent : true , dryRun : true ) ;

        $this->assertSame
        (
            [ 'system' , CertbotSubCommand::DELETE , '--cert-name "example.com"' , true , true , true ] ,
            $this->certbot->calls[ 0 ]
        ) ;
    }

    public function testTheCodeOfTheCommandIsReturned() :void
    {
        $this->certbot->codes = [ 1 ] ;

        $this->assertSame( 1 , $this->certbot->certbotDelete( 'example.com' ) ) ;
    }
}
