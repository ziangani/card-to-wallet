<?php

namespace App\Common;

class ErrorCodes
{
    const ERROR_CODE_SUCCESS = 0;
    const ERROR_MESSAGE_SUCCESS = 'Operation completed successfully';

    const ERROR_CODE_INTERNAL_SERVER_ERROR = 1;
    const ERROR_MESSAGE_INTERNAL_SERVER_ERROR = 'Internal server error';

    const ERROR_CODE_INCOMPLETE_REQUEST = 2;
    const ERROR_MESSAGE_INCOMPLETE_REQUEST = 'Incomplete request';
    const ERROR_CODE_INVALID_NETWORK = 3;
    const ERROR_MESSAGE_INVALID_NETWORK = 'The provided number\'s network is not supported';

    const ERROR_CODE_PROVIDER_ERROR = 4;
    const ERROR_MESSAGE_PROVIDER_ERROR = 'Third party payment provider could not honor the request';
    const ERROR_CODE_DUPLICATE_REFERENCE = 5;
    const ERROR_MESSAGE_DUPLICATE_REFERENCE = 'Duplicate transaction reference';
    const ERROR_CODE_INVALID_MERCHANT_ID = 6;
    const ERROR_MESSAGE_INVALID_MERCHANT_ID = 'Invalid merchant id';
    const ERROR_CODE_AUTHENTICATION_FAILED = 7;
    const ERROR_MESSAGE_AUTHENTICATION_FAILED = 'Authentication failed';
    const ERROR_CODE_TRANSACTION_NOT_FOUND = 8;
    const ERROR_MESSAGE_TRANSACTION_NOT_FOUND = 'Transaction not found';
    const ERROR_CODE_TRANSACTION_PENDING = 9;
    const ERROR_CODE_TRANSACTION_FAILED = 10;
    const ERROR_CODE_TRANSACTION_STATUS_UNKNOWN = 11;
    const ERROR_CODE_INVALID_WALLET = 12;
    const ERROR_MESSAGE_INVALID_WALLET = 'Invalid wallet';

}
