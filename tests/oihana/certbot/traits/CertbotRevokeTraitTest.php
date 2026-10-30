<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotRevokeOptions;
use oihana\certbot\traits\CertbotRevokeTrait;

#[CoversTrait( CertbotRevokeTrait::class )]
final class CertbotRevokeTraitTest extends TestCase
{
    private RecordingCertbot $certbot ;

    protected function setUp() :void
    {
        $this->certbot = new RecordingCertbot() ;
    }

    public function testTheOptionsAreReadFlat() :void
    {
        $this->certbot->initializeCertbotRevokeOptions( [ 'reason' => 'superseded' ] ) ;

        $this->assertInstanceOf( CertbotRevokeOptions::class , $this->certbot->certbotRevokeOptions ) ;
        $this->assertSame( 'superseded' , $this->certbot->certbotRevokeOptions->reason ) ;
    }

    public function testTheOptionsAreReadUnderCertbot() :void
    {
        $this->certbot->initializeCertbotRevokeOptions( [ 'certbot' => [ 'reason' => 'superseded' ] ] ) ;

        $this->assertSame( 'superseded' , $this->certbot->certbotRevokeOptions->reason ) ;
    }

    public function testTheOptionsOfTheCommandWinOverTheSharedOnes() :void
    {
        $this->certbot->initializeCertbotRevokeOptions
        ([
            'certbot' => [ 'reason' => 'superseded' , 'revoke' => [ 'reason' => 'keycompromise' ] ] ,
        ]) ;

        $this->assertSame( 'keycompromise' , $this->certbot->certbotRevokeOptions->reason ) ;
    }

    public function testTheInitializationChains() :void
    {
        $this->assertSame( $this->certbot , $this->certbot->initializeCertbotRevokeOptions() ) ;
    }

    public function testTheCertificateIsRevokedByItsName() :void
    {
        $this->assertSame( 0 , $this->certbot->certbotRevoke( 'example.com' ) ) ;

        $this->assertSame
        (
            [ [ 'system' , CertbotSubCommand::REVOKE , '--cert-name "example.com"' , false , true , false ] ] ,
            $this->certbot->calls
        ) ;
    }

    public function testOnlyTheFirstNameOfAListIsKept() :void
    {
        $this->certbot->certbotRevoke( [ 'example.com' , 'www.example.com' ] ) ;

        $this->assertSame( '--cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testNoNameSendsNoCertName() :void
    {
        $this->certbot->certbotRevoke( '' ) ;
        $this->certbot->certbotRevoke( [] ) ;
        $this->certbot->certbotRevoke() ;

        $this->assertSame( [ '' , '' , '' ] , array_column( $this->certbot->calls , 2 ) ) ;
    }

    public function testDeleteAfterRevoking() :void
    {
        $this->certbot->certbotRevoke( 'example.com' , deleteAfter : true ) ;

        $this->assertSame( '--delete-after-revoke --cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testKeepAfterRevoking() :void
    {
        $this->certbot->certbotRevoke( 'example.com' , deleteAfter : false ) ;

        $this->assertSame( '--no-delete-after-revoke --cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testTheDefaultAndPassedOptionsMeet() :void
    {
        $this->certbot->initializeCertbotRevokeOptions( [ 'reason' => 'superseded' ] ) ;

        $this->certbot->certbotRevoke( 'example.com' , options : [ 'certPath' => '/live/fullchain.pem' ] ) ;

        $this->assertSame
        (
            '--cert-path "/live/fullchain.pem" --reason "superseded" --cert-name "example.com"' ,
            $this->certbot->calls[ 0 ][ 2 ]
        ) ;
    }

    public function testTheFlagsReachTheCommand() :void
    {
        $this->certbot->certbotRevoke( 'example.com' , silent : true , dryRun : true ) ;

        $this->assertSame
        (
            [ 'system' , CertbotSubCommand::REVOKE , '--cert-name "example.com"' , true , true , true ] ,
            $this->certbot->calls[ 0 ]
        ) ;
    }

    public function testTheCodeOfTheCommandIsReturned() :void
    {
        $this->certbot->codes = [ 1 ] ;

        $this->assertSame( 1 , $this->certbot->certbotRevoke( 'example.com' ) ) ;
    }
}
