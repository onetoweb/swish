.. _top:
.. title:: Payout

`Back to index <index.rst>`_

======
Payout
======

.. contents::
    :local:


Create payout
`````````````

.. code-block:: php
    
    // result contains errors or a boolean if the response code is in the 2xx range
    $result = $client->payout->create([
        'payload' => [
            'payoutInstructionUUID' => '',
            'payerPaymentReference' => '',
            'signingCertificateSerialNumber' => '',
            'payerAlias' => '',
            'payeeAlias' => '',
            'payeeSSN' => '',
            'amount' => '',
            'currency' => '',
            'payoutType' => '',
            'instructionDate' => '',
            
            // optional
            'message' => '',
        ],
        'callbackUrl' => '',
        'signature' => ''
    ]);


Get payout
``````````

.. code-block:: php
    
    $id = 'id';
    $result = $client->payout->get($id);


`Back to top <#top>`_