.. _top:
.. title:: Qr

`Back to index <index.rst>`_

==
Qr
==

.. contents::
    :local:


Create commerce
```````````````

.. code-block:: php
    
    // result contains errors or raw binary data of the qr image
    $result = $client->qr->commerce([
        'token' => 'umP7Eg2HT_OUId8Mc0FHPCxhX3Hkh4qI',
        'format' => 'png', // possible values: jpg, png, svg.
        
        // optional
        'size' => 300,
        'border' => 1,
        'transparent' => false
    ]);
    
    if (!is_array($result)) {
        file_put_contents('/path/to/file.png', $result);
    }


Create prefilled
````````````````

.. code-block:: php
    
    // result contains errors or raw binary data of the qr image
    $result = $client->qr->prefilled([
        'format' => 'png', // possible values: jpg, png, svg.
        
        // either: payee and amount or message is required
        'payee' => [
            'value' => '+31612345678',
            'editable' => false
        ],
        'amount' => [
            'value' => 200,
            'editable' => false
        ],
        'message' => [
            'value' => 'message',
            'editable' => false
        ],
        
        // optional
        'size' => 300,
        'border' => 1,
        'transparent' => false
    ]);
    
    if (!is_array($result)) {
        file_put_contents('/path/to/file.png', $result);
    }
