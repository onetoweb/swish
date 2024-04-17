.. title:: Index

===========
Basic Usage
===========

Setup

.. code-block:: php
    
    require 'vendor/autoload.php';
    
    use Onetoweb\Swish\Client;
    
    // params
    $testModus = true;
    
    // params can be ommited in test modus
    $certFile = '/path/to/cert.p12';
    $certPassword = 'swish';
    
    // setup client
    $client = new Client($testModus, $certFile, $certPassword);
    
    // (optional) set handler for running cURL on the system, cURL needs to be build with OpenSSL
    $curlBin = '/path/to/curl';
    
    $client->setHandler(new CurlSystemHandler($curlBin));


===========================================
Compiling cURL with OpenSSL on RHEL systems
===========================================

/path/to/curl is the location where you want to install cURL

.. code-block:: bash
    
    yum install openssl openssl-devel autoconf make libtool
    git clone https://github.com/curl/curl.git curl
    cd curl
    autoreconf -vif
    ./configure --with-openssl --prefix=/path/to/curl --exec-prefix=/path/to/curl
    make
    make install


========
Examples
========

* `Payment <payment.rst>`_
* `Refund <refund.rst>`_
* `Payout <payout.rst>`_
* `Qr <qr.rst>`_
