.. _top:
.. title:: Payment

`Back to index <index.rst>`_

=======
Payment
=======

.. contents::
    :local:


Create payment
``````````````

.. code-block:: php
    
    // generate your own uuid
    $instructionUuid = \Onetoweb\Swish\Utils::Uuid4Hex();
    
    // result contains errors or a boolean if the response code is in the 2xx range
    $result = $client->payment->create($instructionUuid, [
        'payeePaymentReference' => '0123456789',
        'callbackUrl' => 'https://example.com/api/swishcb/paymentrequests',
        'payerAlias' => '4671234768',
        'payeeAlias' => '1234679304',
        'amount' => '100',
        'currency' => 'SEK',
        'message' => 'Kingston USB Flash Drive 8 GB'
    ]);


Get payment
```````````

.. code-block:: php
    
    $id = 'id';
    $result = $client->payment->get($id);


Cancel payment
``````````````

.. code-block:: php
    
    $id = 'id';
    $result = $client->payment->cancel($id);


`Back to top <#top>`_