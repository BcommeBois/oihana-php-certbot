<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use oihana\commands\options\CommandOptions;
use oihana\commands\Process;

use oihana\certbot\traits\CertbotTrait;

/**
 * Uses CertbotTrait with the two methods that run a command replaced by recorders.
 *
 * `system()` and `proc()` run nothing: they note what they were asked. `system()` answers with the
 * next code of `$codes`, `proc()` with the next `[ status , output ]` of `$outputs` (success and no
 * output when the list is empty). So the traits can be tested without certbot and without sudo.
 */
final class RecordingCertbot
{
    use CertbotTrait ;

    /**
     * What the traits asked for, in order: `[ 'system' , command , args , silent , sudo , dryRun ]`
     * or `[ 'proc' , command , args , sudo , dryRun ]`.
     * @var list<array>
     */
    public array $calls = [] ;

    /**
     * The codes the next `system()` calls answer with, first in first out.
     * @var list<int>
     */
    public array $codes = [] ;

    /**
     * The answers of the next `proc()` calls, as `[ status , output ]`, first in first out.
     * @var list<array{int, string}>
     */
    public array $outputs = [] ;

    /**
     * Reaches the protected `initializeCertbotOptions()` of CertbotTrait.
     */
    public function initialize( array $init = [] ) :static
    {
        return $this->initializeCertbotOptions( $init ) ;
    }

    public function proc
    (
        null|array|string $command ,
        null|array|string $args     = null  ,
        ?CommandOptions   $options  = null  ,
        bool              $verbose  = false ,
        ?string           $previous = null  ,
        ?string           $post     = null  ,
        bool              $sudo     = false ,
        bool              $dryRun   = false ,
    )
    :Process
    {
        $this->calls[] = [ 'proc' , $command , $args , $sudo , $dryRun ] ;

        [ $status , $output ] = array_shift( $this->outputs ) ?? [ 0 , '' ] ;

        return new Process
        ([
            Process::OUTPUT => $output ,
            Process::ERROR  => '' ,
            Process::STATUS => $status ,
        ]) ;
    }

    public function system
    (
        null|array|string         $command ,
        null|array|string         $args     = null  ,
        null|array|CommandOptions $options  = null  ,
        bool                      $silent   = false ,
        bool                      $verbose  = false ,
       ?string                    $previous = null  ,
       ?string                    $post     = null  ,
        bool                      $sudo     = false ,
        bool                      $dryRun   = false ,
    )
    :int
    {
        $this->calls[] = [ 'system' , $command , $args , $silent , $sudo , $dryRun ] ;

        return array_shift( $this->codes ) ?? 0 ;
    }
}
