.. _top:
.. title:: Refund

`Back to index <index.rst>`_

======
Refund
======

.. contents::
    :local:


Create refund
`````````````

.. code-block:: php
    
    // generate your own uuid
    $instructionUuid = \Onetoweb\Swish\Utils::Uuid4Hex();
    
    // result contains errors or a boolean if the response code is in the 2xx range
    $result = $client->refund->create($instructionUuid, [
        'originalPaymentReference' => '652ED6A2BCDE4BA8AD11D7334E9567B7',
        'callbackUrl' => 'https://example.com/api/swishcb/paymentrequests',
        'payerAlias' => '1234679304',
        'amount' => '100',
        'currency' => 'SEK',
        'message' => 'Refund for Kingston USB Flash Drive 8 GB'
    ]);


`Back to top <#top>`_