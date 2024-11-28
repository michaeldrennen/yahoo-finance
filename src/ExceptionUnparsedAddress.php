<?php

namespace MichaelDrennen\YahooFinance;

/**
 * When parsing the address from the profile page on Yahoo Finance, sometimes
 * an unusually formatted address will be encountered.
 * This exception gets thrown, so the developer can modify the parser
 * to accommodate the new address format.
 */
class ExceptionUnparsedAddress extends \Exception {


    public string $ticker;

    public array $addressLines;

    public function __construct( $message = "", $code = 0, \Exception $previous = NULL,
                                 string $ticker = NULL,
                                 array $addressLines = [] ) {
        parent::__construct( $message, $code, $previous );
        $this->ticker       = $ticker;
        $this->addressLines = $addressLines;
    }
}