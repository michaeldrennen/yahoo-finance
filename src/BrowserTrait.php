<?php

namespace MichaelDrennen\YahooFinance;

use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Cookies\CookiesCollection;
use HeadlessChromium\Page;

trait BrowserTrait {
    protected CookiesCollection $cookies;
    protected string            $chromePath;
    public ProcessAwareBrowser  $browser;

    public Page $page;
    const NETWORK_IDLE_MS_TO_WAIT    = 4000;
    const BROWSER_WINDOW_SIZE_WIDTH  = 1000;
    const BROWSER_WINDOW_SIZE_HEIGHT = 1000;
    const BROWSER_ENABLE_IMAGES      = FALSE;
    const USER_AGENT_STRING          = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.83 Safari/537.36';


    /**
     * Add some delay between each instruction sent to Chrome. More human-like?
     */
    const BROWSER_CONNECTION_DELAY = 1;


    /**
     * @param string $chromePath
     * @param string $userDataDirPath
     * @param bool   $debug
     *
     * @return void
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    protected function _constructBrowser( string $chromePath, string $userDataDirPath = '.', bool $debug = FALSE ): void {
        $this->cookies    = new CookiesCollection();
        $this->chromePath = $chromePath;

        $browserFactory = new BrowserFactory( $this->chromePath );

        $options = [ 'headless'        => TRUE,         // disable headless mode
                     'userDataDir'     => $userDataDirPath,
                     'connectionDelay' => self::BROWSER_CONNECTION_DELAY,
                     'windowSize'      => [ self::BROWSER_WINDOW_SIZE_WIDTH,
                                            self::BROWSER_WINDOW_SIZE_HEIGHT ],
                     'enableImages'    => self::BROWSER_ENABLE_IMAGES,
                     'customFlags'     => [ '--disable-web-security' ] ];

        if ( $debug ):
            $options[ 'debugLogger' ] = 'php://stdout'; // will enable verbose mode
        endif;

        // starts headless chrome
        $this->browser = $browserFactory->createBrowser( $options );

        $this->createPage();
    }


    /**
     * @return void
     */
    protected function _destroyBrowser(): void {
        try {
            // This is absolutely critical to avoid having zombie chrome processes.
            $this->browser->close();
        } catch ( \Exception $exception ) {
            // I believe the only Exception thrown from here is \HeadlessChromium\Exception\OperationTimedOut
            // I am going to suppress this exception in __destruct.
            // If I let the exception get thrown from __destruct() it will cause a fatal error.
        }
    }


    /**
     * @return void
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    private function createPage(): void {
        $this->page = $this->browser->createPage();
        $this->page->setUserAgent( self::USER_AGENT_STRING );
        $this->page->setCookies( $this->cookies );
    }


    /**
     * @return void
     */
    public function reloadCookies(): void {
        $this->page->setCookies( $this->cookies );
    }
}