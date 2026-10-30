<?php

return [

    /*
    | The shop contract language is Italian. Add a locale here when the
    | storefront actually supports another language; the admin editor
    | lists every entry.
    */
    'locales' => [
        'it' => 'Italiano',
    ],

    'fallback' => 'it',

    'groups' => [
        [
            'label' => 'Page de confirmation',
            'hint' => 'Textes fixes de la page affichée après la commande. Le récapitulatif (produits, montants, numéro) reste calculé sur la commande.',
            'keys' => [
                'confirmation.document_title',
                'confirmation.page_title',
                'confirmation.thanks',
                'confirmation.summary_title',
                'confirmation.product',
                'confirmation.line_total',
                'confirmation.subtotal',
                'confirmation.total',
                'confirmation.payment_method',
                'confirmation.order_number',
                'confirmation.date',
            ],
        ],
        [
            'label' => 'Bonifico anticipato',
            'hint' => 'Le titre et les trois phrases restent toujours sur la page de confirmation. La case ci-dessous ajoute seulement le nom, l’IBAN, le BIC et le type de virement. Ces coordonnées restent affichées sur l’e-mail, le suivi et la page Méthodes de paiement.',
            'keys' => [
                'bank.show_on_confirmation',
                'bank.title',
                'bank.intro',
                'bank.holder_label',
                'bank.holder',
                'bank.iban_label',
                'bank.iban',
                'bank.bic_label',
                'bank.bic',
                'bank.transfer_type_label',
                'bank.transfer_type',
                'bank.whatsapp',
                'bank.receipt',
                'bank.email',
            ],
        ],
        [
            'label' => 'Caisse',
            'hint' => 'Texte de l’option de paiement, avant que les coordonnées ne soient affichées.',
            'keys' => [
                'checkout.section_title',
                'checkout.option_title',
                'checkout.option_description',
            ],
        ],
        [
            'label' => 'Page Méthodes de paiement',
            'hint' => 'Phrase d’introduction de la page. Le bloc des coordonnées bancaires est celui du bonifico, plus haut.',
            'keys' => [
                'payment.exclusive',
            ],
        ],
    ],

    'fields' => [
        'confirmation.document_title' => ['label' => 'Titre de l’onglet', 'type' => 'text'],
        'confirmation.page_title' => ['label' => 'Titre de page', 'type' => 'text'],
        'confirmation.thanks' => ['label' => 'Message de remerciement', 'type' => 'textarea'],
        'confirmation.summary_title' => ['label' => 'Titre du récapitulatif', 'type' => 'text'],
        'confirmation.product' => ['label' => 'Colonne produit', 'type' => 'text'],
        'confirmation.line_total' => ['label' => 'Colonne total', 'type' => 'text'],
        'confirmation.subtotal' => ['label' => 'Libellé sous-total', 'type' => 'text'],
        'confirmation.total' => ['label' => 'Libellé total', 'type' => 'text'],
        'confirmation.payment_method' => ['label' => 'Libellé moyen de paiement', 'type' => 'text'],
        'confirmation.order_number' => ['label' => 'Libellé numéro de commande', 'type' => 'text'],
        'confirmation.date' => ['label' => 'Libellé date', 'type' => 'text'],

        'bank.show_on_confirmation' => [
            'label' => 'Afficher le nom, l’IBAN, le BIC et le type de virement sur la page de confirmation',
            'type' => 'checkbox',
            'hint' => 'Décochez pour garder uniquement le titre Bonifico anticipato et les trois phrases, comme maintenant.',
        ],
        'bank.title' => ['label' => 'Titre du bonifico', 'type' => 'text'],
        'bank.intro' => ['label' => 'Introduction', 'type' => 'textarea'],
        'bank.holder_label' => ['label' => 'Libellé du titulaire', 'type' => 'text'],
        'bank.holder' => ['label' => 'Nom du titulaire', 'type' => 'text'],
        'bank.iban_label' => ['label' => 'Libellé IBAN', 'type' => 'text'],
        'bank.iban' => ['label' => 'IBAN', 'type' => 'text'],
        'bank.bic_label' => ['label' => 'Libellé BIC', 'type' => 'text'],
        'bank.bic' => ['label' => 'BIC', 'type' => 'text'],
        'bank.transfer_type_label' => ['label' => 'Libellé du type de virement', 'type' => 'text'],
        'bank.transfer_type' => ['label' => 'Type de virement', 'type' => 'text'],
        'bank.whatsapp' => [
            'label' => 'Phrase WhatsApp',
            'type' => 'textarea',
            'hint' => 'Le mot WhatsApp devient un lien vers le numéro WhatsApp du site.',
        ],
        'bank.receipt' => [
            'label' => 'Phrase d’envoi de la copie',
            'type' => 'textarea',
            'hint' => 'L’adresse e-mail ci-dessous est ajoutée à la fin de la phrase.',
        ],
        'bank.email' => ['label' => 'E-mail de réception de la copie', 'type' => 'email'],

        'checkout.section_title' => ['label' => 'Titre de la section', 'type' => 'text'],
        'checkout.option_title' => ['label' => 'Nom de l’option', 'type' => 'text'],
        'checkout.option_description' => ['label' => 'Description', 'type' => 'textarea'],

        'payment.exclusive' => ['label' => 'Phrase d’introduction', 'type' => 'textarea'],
    ],

    'defaults' => [
        'confirmation.document_title' => 'Ordine confermato',
        'confirmation.page_title' => 'Cassa',
        'confirmation.thanks' => 'Grazie. Il tuo ordine è stato ricevuto.',
        'confirmation.summary_title' => 'Riepilogo ordine',
        'confirmation.product' => 'Prodotto',
        'confirmation.line_total' => 'Totale',
        'confirmation.subtotal' => 'Subtotale:',
        'confirmation.total' => 'Totale:',
        'confirmation.payment_method' => 'Metodo di pagamento:',
        'confirmation.order_number' => 'Numero ordine:',
        'confirmation.date' => 'Data:',

        'bank.show_on_confirmation' => '0',
        'bank.title' => 'Bonifico anticipato',
        'bank.intro' => 'Paga direttamente sul nostro conto bancario. L\'ordine verrà spedito solo dopo la ricezione del pagamento.',
        'bank.holder_label' => 'Nome',
        'bank.holder' => 'Licari Alessia',
        'bank.iban_label' => 'IBAN',
        'bank.iban' => 'IT09Q3608105138217896017932',
        'bank.bic_label' => 'BIC',
        'bank.bic' => 'PPAYITR1XXX',
        'bank.transfer_type_label' => 'Tipo di trasferimento',
        'bank.transfer_type' => 'istantaneo',
        'bank.whatsapp' => 'Dopo aver effettuato il bonifico, puoi inviarci la ricevuta via WhatsApp per velocizzare la spedizione.',
        'bank.receipt' => 'Ti preghiamo di inviarci una copia del bonifico a',
        'bank.email' => 'contatto@rizzochristian.com',

        'checkout.section_title' => 'Opzioni di pagamento',
        'checkout.option_title' => 'Bonifico anticipato',
        'checkout.option_description' => 'Paga direttamente sul nostro conto bancario con bonifico istantaneo. L’ordine verrà spedito solo dopo la ricezione del pagamento. Le coordinate bancarie vengono mostrate subito dopo l’ordine.',

        'payment.exclusive' => 'Il pagamento sullo shop avviene esclusivamente tramite bonifico anticipato. Nessun altro metodo è disponibile al momento dell’ordine.',
    ],

];
