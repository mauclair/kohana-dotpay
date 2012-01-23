<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'id'                => 12345,
    'PIN'               => '0123456789abcdef',
    // Others
    'returnAction'      => 'welcome/zaplacone',
    'currency'          => 'PLN', // PLN, EUR, USD, GBP, JPY, CZK, SEK
    'lang'              => 'pl', // pl, en, de, it, fr, es, cz, ru, bg
    'selectChannel'     => 0,
    'channels'          => array(
//        0   => 'Karta VISA, MasterCard, JCB, Diners Club',
        1   => 'mTransfer (mBank)',
        2   => 'Płacę z Inteligo (konto Inteligo)',
        3   => 'MultiTransfer (MultiBank)',
        6   => 'Przelew24 (BZWBK)',
        7   => 'ING Bank Śląski',
        8   => 'SEZAM (Bank BPH SA)',
        9   => 'Pekao24 (Bank Pekao S.A.)',
        10  => 'Millennium - klienci korporacyjni',
        11  => 'Przekaz/Przelew bankowy',
        13  => 'Deutsche Bank PBC S.A.',
        14  => 'Kredyt Bank S.A. (KB24)',
        15  => 'iPKO (Bank PKO BP)',
        16  => 'Credit Agricole Bank Polska',
        17  => 'Płać z Nordea',
        18  => 'Przelew z BPH',
        19  => 'Citi Handlowy',
        21  => 'Moje Rachunki',
        24  => 'mPay - płatność telefonem komórkowym',
        26  => 'Bank Ochrony Środowiska',
        27  => 'Bank Gospodarki Żywnościowej',
        31  => 'Zapłać w Żabce i we Freshmarkecie',
        32  => 'BNP Paribas',
        33  => 'Volkswagen Bank Polska',
    ),
    'hashSalt'          => '', // Unikalny ciąg znaków, używany do szyfrowania haseł i kodów przychodzących
);
