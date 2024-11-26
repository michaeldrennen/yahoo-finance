<?php

namespace MichaelDrennen\YahooFinance;


/**
 * Sometimes a ticker can't be found on Yahoo.
 */
class ExceptionTickerNotFound extends \Exception {


    public string $ticker;

    public function __construct( $message = "",
        $code = 0,
                                 \Exception $previous = NULL,
                                 string $ticker = NULL ) {
        parent::__construct( $message, $code, $previous );
        $this->ticker = $ticker;
    }
}