<?php

namespace MichaelDrennen\YahooFinance;


/**
 * Sometimes a ticker can't be found on Yahoo.
 */
class ExceptionTickerNotFound extends \Exception {


    public string $ticker;

    public string $html;

    /**
     * @param                 $message
     * @param                 $code
     * @param \Exception|NULL $previous
     * @param string|NULL     $ticker
     * @param string          $html
     */
    public function __construct( $message = "", $code = 0, \Exception $previous = NULL,
                                 string $ticker = NULL,
                                 string $html = '' ) {
        parent::__construct( $message, $code, $previous );
        $this->ticker = $ticker;
        $this->html   = $html;
    }


    /**
     * @return string
     */
    public function getHtml() {
        return $this->html;
    }
}