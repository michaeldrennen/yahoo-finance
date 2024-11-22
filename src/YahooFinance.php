<?php

namespace MichaelDrennen\YahooFinance;

class YahooFinance {

    use BrowserTrait;
    use ProfileTrait;

    const YAHOO_FINANCE_URL = 'https://finance.yahoo.com/';

    public function __construct( string $chromePath ) {
        $this->_constructBrowser( $chromePath );

    }

    public function __destruct() {
        $this->_destroyBrowser();
    }



}