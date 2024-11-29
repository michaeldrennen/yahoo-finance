<?php

namespace MichaelDrennen\YahooFinance;

class YahooFinance {

    use BrowserTrait;
    use ProfileTrait;

    const YAHOO_FINANCE_URL = 'https://finance.yahoo.com/';

    /**
     * @param string $chromePath
     * @param string $userDataDirPath
     * @param bool   $debug
     *
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function __construct( string $chromePath, string $userDataDirPath = '.', bool $debug = FALSE ) {
        $this->_constructBrowser( $chromePath, $userDataDirPath, $debug );

    }

    public function __destruct() {
        $this->_destroyBrowser();
    }


}