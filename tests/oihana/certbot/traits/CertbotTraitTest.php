<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

use oihana\certbot\traits\CertbotTrait;

#[CoversTrait( CertbotTrait::class )]
final class CertbotTraitTest extends TestCase
{
    public function testEveryCommandIsInitialized() :void
    {
        $certbot = new RecordingCertbot() ;

        $this->assertSame( $certbot , $certbot->initialize
        ([
            'certbot' =>
            [
                'config'       => '/shared.ini' ,
                'certificates' => [ 'config' => '/certificates.ini' ] ,
                'certOnly'     => [ 'config' => '/certonly.ini'     ] ,
                'delete'       => [ 'config' => '/delete.ini'       ] ,
                'renew'        => [ 'config' => '/renew.ini'        ] ,
                'revoke'       => [ 'config' => '/revoke.ini'       ] ,
            ] ,
        ])) ;

        $this->assertSame( '/certificates.ini' , $certbot->certbotCertificatesOptions->config ) ;
        $this->assertSame( '/certonly.ini'     , $certbot->certbotCertOnlyOptions->config     ) ;
        $this->assertSame( '/delete.ini'       , $certbot->certbotDeleteOptions->config       ) ;
        $this->assertSame( '/renew.ini'        , $certbot->certbotRenewOptions->config        ) ;
        $this->assertSame( '/revoke.ini'       , $certbot->certbotRevokeOptions->config       ) ;
    }

    public function testNothingGivenLeavesEveryCommandWithItsDefaults() :void
    {
        $certbot = ( new RecordingCertbot() )->initialize() ;

        $this->assertTrue( $certbot->certbotCertificatesOptions->enabled ) ;
        $this->assertTrue( $certbot->certbotCertOnlyOptions->enabled     ) ;
        $this->assertTrue( $certbot->certbotDeleteOptions->enabled       ) ;
        $this->assertTrue( $certbot->certbotRenewOptions->enabled        ) ;
        $this->assertTrue( $certbot->certbotRevokeOptions->enabled       ) ;
    }
}
