<?php


use MichaelDrennen\YahooFinance\YahooFinance;

class TestYahooFinance extends \PHPUnit\Framework\TestCase {

    protected static \MichaelDrennen\YahooFinance\YahooFinance $yahooFinance;

    public static function setUpBeforeClass(): void {
        self::$yahooFinance = new YahooFinance( $_ENV[ 'CHROME_PATH' ] );
    }


    public static function tearDownAfterClass(): void {

    }


    /**
     * @test
     * @group sector
     */
    public function testGetSector() {
        $sector = self::$yahooFinance->getSector( 'LODE' );
        $this->assertEquals( 'Basic Materials', $sector );
    }
}