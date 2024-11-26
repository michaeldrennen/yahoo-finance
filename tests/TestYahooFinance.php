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
     * @group profile
     */
    public function testGetSector() {
        $sector = self::$yahooFinance->getSector( 'LODE' );
        $this->assertEquals( 'Basic Materials', $sector );
    }

    /**
     * @test
     * @group profile
     */
    public function testGetIndustry() {
        $industry = self::$yahooFinance->getIndustry( 'LODE' );
        $this->assertEquals( 'Other Precious Metals & Mining', $industry );
    }

    /**
     * @test
     * @group profile
     */
    public function testGetFullTimeEmployees() {
        $numFullTimeEmployees = self::$yahooFinance->getFullTimeEmployees( 'AAPL' );
        $this->assertEquals( 164000, $numFullTimeEmployees );
    }


    /**
     * @test
     * @group name
     */
    public function testGetCompanyName() {
        $companyName = self::$yahooFinance->getCompanyName( 'LODE' );
        $this->assertEquals( 'Comstock Inc.', $companyName );
    }


    /**
     * @test
     * @group address
     */
    public function testGetCompanyAddress() {
        $companyAddress = self::$yahooFinance->getCompanyAddress( 'LODE' );
        $this->assertEquals( '117 American Flat Road', $companyAddress[ 'street' ] );
        $this->assertEquals( 'Virginia City', $companyAddress[ 'city' ] );
    }


    /**
     * @test
     * @group phone
     */
    public function testGetTelephoneNumber() {
        $phone = self::$yahooFinance->getCompanyTelephoneNumber( 'LODE' );
        $this->assertEquals( '775 847 5272', $phone );
    }


    /**
     * @test
     * @group url
     */
    public function testGetCompanyWebsite() {
        $url = self::$yahooFinance->getCompanyWebsite( 'LODE' );
        $this->assertEquals( 'https://www.comstock.inc', $url );
    }


    /**
     * @test
     * @group desc
     */
    public function testGetCompanyDescription() {
        $desc = self::$yahooFinance->getCompanyDescription( 'LODE' );
        $this->assertTrue( str_contains( $desc, 'Comstock Inc. engages in the systemic decarbonization business' ) );
    }


    /**
     * @test
     * @group execs
     */
    public function testGetKeyExecutives() {
        $keyExecs = self::$yahooFinance->getKeyExecutives( 'LODE' );
        $this->assertNotEmpty( $keyExecs );
        $this->assertEquals( 'Mr. Corrado F. De Gasperis', $keyExecs[ 0 ][ 'name' ] );
    }


    /**
     * @test
     * @group profile
     */
    public function testGetCompleteProfile() {
        $profile = self::$yahooFinance->getCompleteProfile( 'LODE' );
        $this->assertNotEmpty( $profile );
    }


    /**
     * @test
     * @group badticker
     */
    public function testGetTickerNotFoundOnYahoo(){
        $this->expectException(\MichaelDrennen\YahooFinance\ExceptionTickerNotFound::class);
        $tickerThatDoesNotExist = 'DLA';
        self::$yahooFinance->getCompleteProfile( $tickerThatDoesNotExist );

    }
}