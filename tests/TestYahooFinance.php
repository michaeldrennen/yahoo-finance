<?php


use MichaelDrennen\YahooFinance\YahooFinance;

class TestYahooFinance extends \PHPUnit\Framework\TestCase {

    protected static \MichaelDrennen\YahooFinance\YahooFinance $yahooFinance;

    public static function setUpBeforeClass(): void {
        self::$yahooFinance = new YahooFinance( $_ENV[ 'CHROME_PATH' ],
                                                $_ENV[ 'CHROME_PATH' ],
                                                FALSE );
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
        $this->assertEquals( 166000, $numFullTimeEmployees );
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
        $this->assertTrue( str_contains( $desc, 'Comstock Inc. commercializes technologies, systems, and supply chains that extract, process, and convert under-utilized waste and natural resources into clean energy and clean energy supporting products in the United States. It operates through the Fuels, Metals, Mining, and Strategic Investments segments. The company develops and commercializes technology that extracts and converts wasted and unused lignocellulosic biomass into intermediates for refining into advanced renewable fuels. It also owns 100% in the Lucerne Project located in the Storey County, Nevada; and the Spring Valley Project situated in the Lyon County, Nevada. In addition, the company offers engineering and construction services; and invests in non-mining real estate, water rights, and securities investments. Comstock Inc. company was incorporated in 1999 and is based in Virginia City, Nevada.' ) );
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
    public function testGetTickerNotFoundOnYahoo() {
        $this->expectException( \MichaelDrennen\YahooFinance\ExceptionTickerNotFound::class );
        $tickerThatDoesNotExist = 'DLA';
        self::$yahooFinance->getCompleteProfile( $tickerThatDoesNotExist );

    }
}