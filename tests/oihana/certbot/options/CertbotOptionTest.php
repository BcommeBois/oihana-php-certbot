<?php

namespace tests\oihana\certbot\options;

use oihana\certbot\options\CertbotOption;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CertbotOptionTest extends TestCase
{
    // ------- getCommandOption

    #[Test]
    #[DataProvider( 'shortOptionProvider' )]
    public function getCommandOptionReturnsShortForm( string $option , string $expected ): void
    {
        $this->assertSame( $expected , CertbotOption::getCommandOption( $option ) );
    }

    public static function shortOptionProvider(): array
    {
        return [
            'authenticator' => [ CertbotOption::AUTHENTICATOR   , 'a' ] ,
            'config'        => [ CertbotOption::CONFIG           , 'c' ] ,
            'domains'       => [ CertbotOption::DOMAINS          , 'd' ] ,
            'email'         => [ CertbotOption::EMAIL            , 'm' ] ,
            'help'          => [ CertbotOption::HELP             , 'h' ] ,
            'installer'     => [ CertbotOption::INSTALLER        , 'i' ] ,
            'nonInteractive'=> [ CertbotOption::NON_INTERACTIVE  , 'n' ] ,
            'quiet'         => [ CertbotOption::QUIET            , 'q' ] ,
            'verbose'       => [ CertbotOption::VERBOSE          , 'v' ] ,
            'webrootPath'   => [ CertbotOption::WEBROOT_PATH     , 'w' ] ,
        ];
    }

    #[Test]
    public function getCommandOptionReturnsHyphenatedFormForNonShortOptions(): void
    {
        $result = CertbotOption::getCommandOption( CertbotOption::AGREE_TOS );
        $this->assertIsString( $result );
        $this->assertNotEmpty( $result );
    }

    // ------- getCommandPrefix

    #[Test]
    #[DataProvider( 'singleHyphenPrefixProvider' )]
    public function getCommandPrefixReturnsSingleHyphenForShortOptions( string $option ): void
    {
        $prefix = CertbotOption::getCommandPrefix( $option );
        $this->assertSame( '-' , $prefix );
    }

    public static function singleHyphenPrefixProvider(): array
    {
        return [
            'authenticator'  => [ CertbotOption::AUTHENTICATOR ] ,
            'config'         => [ CertbotOption::CONFIG ] ,
            'domains'        => [ CertbotOption::DOMAINS ] ,
            'email'          => [ CertbotOption::EMAIL ] ,
            'help'           => [ CertbotOption::HELP ] ,
            'installer'      => [ CertbotOption::INSTALLER ] ,
            'nonInteractive' => [ CertbotOption::NON_INTERACTIVE ] ,
            'quiet'          => [ CertbotOption::QUIET ] ,
            'verbose'        => [ CertbotOption::VERBOSE ] ,
            'webrootPath'    => [ CertbotOption::WEBROOT_PATH ] ,
        ];
    }

    #[Test]
    public function getCommandPrefixReturnsDoubleHyphenForLongOptions(): void
    {
        $this->assertSame( '--' , CertbotOption::getCommandPrefix( CertbotOption::AGREE_TOS ) );
        $this->assertSame( '--' , CertbotOption::getCommandPrefix( CertbotOption::CERT_NAME ) );
        $this->assertSame( '--' , CertbotOption::getCommandPrefix( CertbotOption::RSA_KEY_SIZE ) );
        $this->assertSame( '--' , CertbotOption::getCommandPrefix( CertbotOption::WEBROOT ) );
        $this->assertSame( '--' , CertbotOption::getCommandPrefix( CertbotOption::FORCE_RENEWAL ) );
    }
}
