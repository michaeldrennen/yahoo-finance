<?php

namespace MichaelDrennen\YahooFinance;


/**
 * First used when missing a telephone number.
 */
class ExceptionMissingElement extends \Exception {


    public string $ticker;


    public function __construct( $message = "",
        $code = 0,
                                 \Exception $previous = NULL,
                                 string $ticker = NULL ) {
        parent::__construct( $message, $code, $previous );
        $this->ticker = $ticker;
    }
}