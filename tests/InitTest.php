<?php
namespace tests;
use Ably\AblyRest;
use \Exception;

require_once __DIR__ . '/factories/TestApp.php';

class InitTest extends \PHPUnit_Framework_TestCase {

    protected static $testApp;

    public static function setUpBeforeClass() {
        self::$testApp = new TestApp();
    }

    public static function tearDownAfterClass() {
      self::$testApp->release();
    }

    /**
     * Init library with a key only
     */
    public function testInitLibWithKeyOnly() {
        try {
            $key = self::$testApp->getAppKeyDefault();
            new AblyRest( $key->string );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Init library with a key in options
     */
    public function testInitLibWithKeyOption() {
        try {
            $key = self::$testApp->getAppKeyDefault();
            new AblyRest( array('key' => $key->string) );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Init library with appId
     */
    public function testInitLibWithAppId() {
        try {
            $key = self::$testApp->getAppKeyDefault();
            new AblyRest( array('appId' => self::$testApp->getAppId() ) );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Verify library fails to init when both appId and key are missing
     */
    public function testFailInitOnMissingAppIdAndKey() {
        try {
            new AblyRest( array() );
            $this->fail('Unexpected success instantiating library');
        } catch (Exception $e) {
            # do nothing
        }
    }

    /**
     * Init library with specified host
     */
    public function testInitLibWithSpecifiedHost() {
        try {
            $opts = array(
                'appId' => self::$testApp->getAppId(),
                'host'  => 'some.other.host',
            );
            $ably = new AblyRest( $opts );
            $this->assertEquals( $opts['host'], $ably->get_setting('host'), 'Unexpected host mismatch' );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Init library with specified port
     */
    public function testInitLibWithSpecifiedPort() {
        try {
            $opts = array(
                'appId' => self::$testApp->getAppId(),
                'port'  => 9999,
            );
            $ably = new AblyRest( $opts );
            $this->assertEquals( $opts['port'], $ably->get_setting('port'), 'Unexpected port mismatch' );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Verify encrypted defaults to true
     */
    public function testEncryptedDefaultIsTrue() {
        try {
            $opts = array(
                'appId' => self::$testApp->getAppId(),
            );
            $ably = new AblyRest( $opts );
            $this->assertEquals( 'https', $ably->get_setting('scheme'), 'Unexpected scheme mismatch' );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Verify encrypted can be set to false
     */
    public function testEncryptedCanBeFalse() {
        try {
            $opts = array(
                'appId' => self::$testApp->getAppId(),
                'encrypted' => false,
            );
            $ably = new AblyRest( $opts );
            $this->assertEquals( 'http', $ably->get_setting('scheme'), 'Unexpected scheme mismatch' );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Init with log handler; check called
     */
    protected $init8_logCalled = false;
    public function testLoggerIsCalledWithDebugTrue() {
        try {
            $opts = array(
                'appId' => self::$testApp->getAppId(),
                'debug' => function( $output ) {
                    $this->init8_logCalled = true;
                    return $output;
                },
            );
            new AblyRest( $opts );
            $this->assertTrue( $this->init8_logCalled, 'Log handler not called' );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }

    /**
     * Init with log handler; check not called if logLevel == NONE
     */
    public function testLoggerNotCalledWithDebugFalse() {
        try {
            $opts = array(
                'appId' => self::$testApp->getAppId(),
                'debug' => false,
            );
            $ably = new AblyRest( $opts );
            # There is no logLevel in the PHP library so we'll simply assert log_action returns false
            $this->assertFalse( $ably->log_action('test()', 'called'), 'Log handler incorrectly called' );
        } catch (Exception $e) {
            $this->fail('Unexpected exception instantiating library');
        }
    }
}