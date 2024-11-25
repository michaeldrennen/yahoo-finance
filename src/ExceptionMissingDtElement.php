<?php

namespace MichaelDrennen\YahooFinance;


/**
 * When parsing the Yahoo Finance profile page, sometimes the
 * profile will be missing elements.
 * For example, for ticker CFFN (https://finance.yahoo.com/quote/CFFN/profile/)
 * ...they don't have the line for full-time employees.
 * I certainly don't want that to be a showstopping error.
 * So I created this exception to identify this specific problem.
 * Then handle the logic appropriately after that.
 * ( Either ignore the exception, or perhaps report it so I can improve the parser. )
 */
class ExceptionMissingDtElement extends \Exception {


    public string $ticker;

    public string $label;

    public function __construct( $message = "",
        $code = 0,
                                 \Exception $previous = NULL,
                                 string $ticker = NULL,
                                 string $label = NULL ) {
        parent::__construct( $message, $code, $previous );
        $this->ticker = $ticker;
        $this->label  = $label;
    }
}