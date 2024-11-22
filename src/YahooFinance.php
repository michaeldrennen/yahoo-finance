<?php

namespace MichaelDrennen\YahooFinance;

class YahooFinance {

    use BrowserTrait;

    const YAHOO_FINANCE_URL = 'https://finance.yahoo.com/';

    public function __construct( string $chromePath ) {
        $this->_constructBrowser( $chromePath );

    }

    public function __destruct() {
        $this->_destroyBrowser();
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
        $url = self::YAHOO_FINANCE_URL . 'quote/' . $ticker . '/profile';
        $this->page->navigate( $url )->waitForNavigation();
        $html = $this->page->getHtml();

        $dom = new \DOMDocument();
        @$dom->loadHTML( $html );

        $dts = $dom->getElementsByTagName( 'dt' );

        /**
         * @var \DOMElement $dt
         */
        foreach ( $dts as $dt ):
            $dtContents = strtolower( $dt->textContent );

            if ( !str_contains( $dtContents, 'sector' ) ):
                continue;
            endif;

            return trim( $dt->nextElementSibling->nodeValue );
        endforeach;

        throw new \Exception( "Parser did not find a <dt> element with text content of 'sector'" );
    }
}