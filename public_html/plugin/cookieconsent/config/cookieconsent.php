<?php

return [
    'title' => 'Gestisci la tua privacy',
    'description' => 'Utilizziamo i cookie per personalizzare l\'esperienza e misurare le performance. Scegli le categorie che preferisci mantenendo il controllo completo dei tuoi dati.',
    'policy_url' => '/privacy-policy.html',
    'categories' => [
        'necessary' => [
            'label' => 'Necessari',
            'description' => 'Abilitano le funzioni di base del sito e non possono essere disabilitati.',
            'readonly' => true,
        ],
        'preferences' => [
            'label' => 'Preferenze',
            'description' => 'Ricordano le tue scelte e migliorano l\'esperienza personalizzata.',
            'readonly' => false,
        ],
        'analytics' => [
            'label' => 'Statistiche',
            'description' => 'Aiutano a capire come il sito viene utilizzato per migliorarne i servizi.',
            'readonly' => false,
        ],
        'marketing' => [
            'label' => 'Marketing',
            'description' => 'Consentono annunci pertinenti e campagne ottimizzate.',
            'readonly' => false,
        ],
    ],
    'buttons' => [
        'accept_all' => 'Accetta tutto',
        'reject_all' => 'Rifiuta non necessari',
        'save' => 'Salva preferenze',
        'manage' => 'Personalizza',
    ],
];
