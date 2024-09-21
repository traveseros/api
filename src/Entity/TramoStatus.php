<?php

namespace App\Entity;

enum TramoStatus: string
{
    case NOT_STARTED = 'no_empezado';
    case IN_TRANSIT = 'en_transito';
    case DELAYED = 'con_retraso';
    case LOST = 'perdidos';
    case FINISH = 'terminado';
}