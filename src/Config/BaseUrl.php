<?php

namespace Onetoweb\Swish\Config;

enum BaseUrl: string
{
    case LIVE = 'https://cpc.getswish.net/swish-cpcapi';
    case TEST = 'https://mss.cpc.getswish.net/swish-cpcapi';
    case QR = 'https://mpc.getswish.net/qrg-swish';
}
