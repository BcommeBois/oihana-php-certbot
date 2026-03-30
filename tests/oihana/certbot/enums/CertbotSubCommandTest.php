<?php

namespace tests\oihana\certbot\enums;

use oihana\certbot\enums\CertbotSubCommand;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CertbotSubCommandTest extends TestCase
{
    #[Test]
    public function certbotConstant(): void
    {
        $this->assertSame( 'certbot' , CertbotSubCommand::CERTBOT );
    }

    #[Test]
    public function certOnlyConstant(): void
    {
        $this->assertSame( 'certbot certonly' , CertbotSubCommand::CERT_ONLY );
    }

    #[Test]
    public function enhanceConstant(): void
    {
        $this->assertSame( 'certbot enhance' , CertbotSubCommand::ENHANCE );
    }

    #[Test]
    public function runConstant(): void
    {
        $this->assertSame( 'certbot run' , CertbotSubCommand::RUN );
    }

    #[Test]
    public function certificatesConstant(): void
    {
        $this->assertSame( 'certbot certificates' , CertbotSubCommand::CERTIFICATES );
    }

    #[Test]
    public function deleteConstant(): void
    {
        $this->assertSame( 'certbot delete' , CertbotSubCommand::DELETE );
    }

    #[Test]
    public function renewConstant(): void
    {
        $this->assertSame( 'certbot renew' , CertbotSubCommand::RENEW );
    }

    #[Test]
    public function revokeConstant(): void
    {
        $this->assertSame( 'certbot revoke' , CertbotSubCommand::REVOKE );
    }

    #[Test]
    public function updateSymlinksConstant(): void
    {
        $this->assertSame( 'certbot update_symlinks' , CertbotSubCommand::UPDATE_SYMLINKS );
    }
}
