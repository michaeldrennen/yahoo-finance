<?php

namespace MichaelDrennen\YahooFinance;


use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isNull;

trait ProfileTrait {
    use BrowserTrait;


    /**
     * @var \DOMDocument[]
     */
    protected array $domsByTicker = [];


    /**
     * @param string $ticker
     *
     * @return \DOMDocument
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \MichaelDrennen\YahooFinance\ExceptionTickerNotFound
     */
    protected function _getDom( string $ticker ): \DOMDocument {
        if ( isset( $this->domsByTicker[ $ticker ] ) ):
            return $this->domsByTicker[ $ticker ];
        endif;

        $url = YahooFinance::YAHOO_FINANCE_URL . 'quote/' . $ticker . '/profile';
        $this->page->navigate( $url )->waitForNavigation();
        $html = $this->page->getHtml();

        if ( str_contains( $html, 'Symbols similar to' ) ):
            throw new ExceptionTickerNotFound( "The ticker " . $ticker . " was not found on Yahoo.", 0, NULL, $ticker );
        endif;

        $dom = new \DOMDocument();
        @$dom->loadHTML( $html );
        $this->domsByTicker[ $ticker ] = $dom;

        return $this->domsByTicker[ $ticker ];
    }


    /**
     * @param string $ticker Ex: AAPL
     * @param string $label  Ex: 'sector' 'industry' ...etc
     *
     * @return mixed
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \MichaelDrennen\YahooFinance\ExceptionMissingDtElement
     */
    protected function _getValueAfterLabel( string $ticker, string $label ): mixed {
        $dom = $this->_getDom( $ticker );

        $dts = $dom->getElementsByTagName( 'dt' );

        /**
         * @var \DOMElement $dt
         */
        foreach ( $dts as $dt ):
            $dtContents = strtolower( $dt->textContent );

            if ( !str_contains( $dtContents, strtolower( $label ) ) ):
                continue;
            endif;

            return trim( $dt->nextElementSibling->nodeValue );
        endforeach;

        throw new ExceptionMissingDtElement( "Parser did not find a <dt> element with text content of '" . strtolower( $label ) . "' for ticker " . $ticker,
                                             0,
                                             NULL,
                                             $ticker,
                                             $label );
    }


    /**
     * @param string $ticker
     *
     * @return array
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getCompleteProfile( string $ticker ): array {
        return [
            'sector'               => $this->getSector( $ticker ),
            'industry'             => $this->getIndustry( $ticker ),
            'numFullTimeEmployees' => $this->getFullTimeEmployees( $ticker ),
            'name'                 => $this->getCompanyName( $ticker ),
            'address'              => $this->getCompanyAddress( $ticker ),
            'telephone'            => $this->getCompanyTelephoneNumber( $ticker ),
            'website'              => $this->getCompanyWebsite( $ticker ),
            'description'          => $this->getCompanyDescription( $ticker ),
            'keyExecutives'        => $this->getKeyExecutives( $ticker ),
        ];
    }


    /**
     * @param string $ticker
     *
     * @return string
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \Exception
     */
    public function getSector( string $ticker ): string {
        try {
            return $this->_getValueAfterLabel( $ticker, 'sector' );
        } catch ( ExceptionMissingDtElement $e ) {
            // Sometimes the profile is missing a piece of data.
            return '';
        }
    }


    /**
     * @param string $ticker
     *
     * @return string
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getIndustry( string $ticker ): string {
        try {
            return $this->_getValueAfterLabel( $ticker, 'industry' );
        } catch ( ExceptionMissingDtElement $e ) {
            // Sometimes the profile is missing a piece of data.
            return '';
        }
    }


    /**
     * @param string $ticker
     *
     * @return int
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \Exception
     */
    public function getFullTimeEmployees( string $ticker ): ?int {
        try {
            $stringNumber = $this->_getValueAfterLabel( $ticker, 'Full Time Employees' );
            return (int)str_replace( ',', '', $stringNumber );
        } catch ( ExceptionMissingDtElement $e ) {
            // Sometimes the profile is missing a piece of data.
            return NULL;
        }
    }


    /**
     * @param string $ticker
     *
     * @return string
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getCompanyName( string $ticker ): string {
        $dom = $this->_getDom( $ticker );

        $titles      = $dom->getElementsByTagName( 'title' );
        $title       = $titles->item( 0 );
        $titleParts  = explode( '(', $title->textContent );
        $companyName = trim( $titleParts[ 0 ] );

        return $companyName;
    }


    /**
     * @param string $ticker
     *
     * @return array
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getCompanyAddress( string $ticker ): array {
        $address = [];
        $dom     = $this->_getDom( $ticker );

        $xpath      = new \DOMXPath( $dom );
        $expression = "//div[contains(@class,'company-details')]/div[contains(@class,'company-info')]/div[contains(@class,'address')]/div";
        $nodes      = $xpath->query( $expression );


        $addressParts = [];
        foreach ( $nodes as $node ):
            if ( is_null( $node ) ):
                continue;
            endif;
            $addressParts[] = trim( $node->textContent );
        endforeach;

        $address = self::getAddressFromAddressLines( $addressParts, $ticker );


        return $address;
    }


    /**
     * @param string $ticker
     *
     * @return string|null
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getCompanyTelephoneNumber( string $ticker ): ?string {
        $dom = $this->_getDom( $ticker );

        $xpath      = new \DOMXPath( $dom );
        $expression = "//div[contains(@class,'company-details')]/div/a[contains(@href,'tel')]";
        $nodes      = $xpath->query( $expression );
        $firstNode  = $nodes->item( 0 );
        if ( is_null( $firstNode ) ):
            //throw new ExceptionMissingElement( "Unable to find the telephone number.", 0, NULL, $ticker );
            return NULL;
        endif;
        $href      = $firstNode->getAttribute( 'href' );
        $telephone = str_replace( 'tel:', '', $href );
        return $telephone;
    }


    /**
     * @param string $ticker
     *
     * @return string
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getCompanyWebsite( string $ticker ): ?string {
        $dom = $this->_getDom( $ticker );

        $xpath      = new \DOMXPath( $dom );
        $expression = "//div[contains(@class,'company-details')]/div/a[contains(@data-ylk,'business-url')]";
        $nodes      = $xpath->query( $expression );
        $firstNode  = $nodes->item( 0 );
        if ( is_null( $firstNode ) ):
            return NULL;
        endif;
        $href = $firstNode->getAttribute( 'href' );
        return $href;
    }


    /**
     * @param string $ticker
     *
     * @return string|null
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function getCompanyDescription( string $ticker ): ?string {
        $dom = $this->_getDom( $ticker );

        $xpath      = new \DOMXPath( $dom );
        $expression = "//section[@data-testid='description']/p";
        $nodes      = $xpath->query( $expression );
        $firstNode  = $nodes->item( 0 );
        if ( is_null( $firstNode ) ):
            return NULL;
        endif;
        $desc = trim( $nodes->item( 0 )->textContent );
        return $desc;
    }


    public function getKeyExecutives( string $ticker ): array {
        $execs = [];
        $dom   = $this->_getDom( $ticker );

        $xpath      = new \DOMXPath( $dom );
        $expression = "//section[@data-testid='key-executives']/div/table/tbody/tr";
        $trs        = $xpath->query( $expression );
        foreach ( $trs as $tr ):
            $tds       = $tr->getElementsByTagName( 'td' );
            $name      = trim( $tds->item( 0 )->textContent );
            $title     = trim( $tds->item( 1 )->textContent );
            $pay       = trim( $tds->item( 2 )->textContent );
            $exercised = trim( $tds->item( 3 )->textContent );
            $birthYear = trim( $tds->item( 4 )->textContent );
            $execs[]   = [
                'name'      => str_replace( '  ', ' ', $name ),
                'title'     => $title,
                'pay'       => '--' == $pay ? NULL : $pay,
                'exercised' => '--' == $exercised ? NULL : $exercised,
                'birthYear' => '--' == $birthYear ? NULL : $birthYear,
            ];
        endforeach;

        return $execs;
    }


    /**
     * Below are examples of what will be held in $addressParts:
     *
     * [0] => 117 American Flat Road
     * [1] => Virginia City, NV 89440
     * [2] => United States
     *
     * 0 => "616 N. North Court"
     * 1 => "Suite 120"
     * 2 => "Palatine, IL 60067"
     * 3 => "United States"
     *
     * 0 => "China Yuangu Hanggang Technology Building"
     * 1 => "509 Qianjiang Road Shangcheng District"
     * 2 => "Hangzhou, 310000"
     * 3 => "China"
     *
     * 0 => "A.S. Cooper Building"
     * 1 => "Suite 601 26 Reid Street"
     * 2 => "Hamilton, HM 11"
     * 3 => "Bermuda"
     *
     */

    /**
     * @param array  $addressLines
     * @param string $ticker
     *
     * @return array
     * @throws \MichaelDrennen\YahooFinance\ExceptionUnparsedAddress
     */
    public static function getAddressFromAddressLines( array $addressLines, string $ticker ): array {

        $STREET     = NULL;
        $UNIT       = NULL;
        $CITY       = NULL;
        $STATE      = NULL;
        $POSTALCODE = NULL;
        $COUNTRY    = NULL;

        try {
            // The country will always be the last line... I think.
            $COUNTRY = array_pop( $addressLines );

            $lineWithPostalCode = array_pop( $addressLines );

            $regexPostalCode = '/\d+$/';
            preg_match( $regexPostalCode, $lineWithPostalCode, $matches );
            $POSTALCODE            = $matches[ 0 ];
            $lineWithoutPostalCode = preg_replace( $regexPostalCode,
                                                   '',
                                                   $lineWithPostalCode );

            $remainingPartsOfPostalCodeLine = explode( ',', $lineWithoutPostalCode );
            $remainingPartsOfPostalCodeLine = array_map( 'trim', $remainingPartsOfPostalCodeLine );


            $CITY  = array_shift( $remainingPartsOfPostalCodeLine );
            $STATE = array_shift( $remainingPartsOfPostalCodeLine );

            // Only a street address. No building.
            if ( 1 == count( $addressLines ) ):
                $STREET = array_pop( $addressLines );
            elseif ( 2 == count( $addressLines ) ):
                $UNIT   = array_pop( $addressLines );
                $STREET = array_pop( $addressLines );
            else:
                throw new \Exception( "What the hell kinda address was this?" );
            endif;

            return [
                'street'  => $STREET,
                'unit'    => $UNIT,
                'city'    => $CITY,
                'state'   => $STATE,
                'zip'     => $POSTALCODE,
                'country' => $COUNTRY,
            ];
        } catch ( \Exception $e ) {
            throw new ExceptionUnparsedAddress( "Address for $ticker was unable to be parsed.",
                                                $e->getCode(),
                                                $e,
                                                $ticker,
                                                $addressLines );
        }
    }

    public function flushDomsByTickerCache(): void {
        $this->domsByTicker = [];
    }


}