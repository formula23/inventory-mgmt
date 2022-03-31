<?php

return [
    'date_format' => 'm/d/Y',
    'date_time_format' => 'm/d/Y H:i:s',
    'excise_tax_rate' => 0.27,
    'transpo_tax_rate' => 0.01,
//    'cultivation_tax_2019' => [
//        'flower' => [
//            'ounce' => 9.25,
//            'pound' => (9.25 * 16), //154.4
//            'gram' => (9.25 / 28.3495),
//        ],
//        'trim' => [
//            'ounce' => 2.75,
//            'pound' => (2.75 * 16), //45.92
//            'gram' => (2.75 / 28.3495),
//        ],
//        '1_8' => 1.16,
//        '1_4' => 2.31,
//        '1_2' => 4.63,
//    ],
//    'cultivation_tax_2020' => [
//        'flower' => [
//            'ounce' => 9.65,
//            'oz' => 9.65,
//            'pound' => (9.65 * 16), //154.4
//            'lb' => (9.65 * 16), //154.4
//            'gram' => (9.65 / 28.3495),
//            'g' => (9.65 / 28.3495),
//        ],
//        'trim' => [
//            'ounce' => 2.87,
//            'oz' => 2.87,
//            'pound' => (2.87 * 16), //45.92
//            'lb' => (2.87 * 16), //45.92
//            'gram' => (2.87 / 28.3495),
//            'g' => (2.87 / 28.3495),
//        ],
//        '1_8' => 1.16,
//        '1_4' => 2.31,
//        '1_2' => 4.63,
//    ],
//    'cultivation_tax_2021' => [
//        'flower' => [
//            'ounce' => 9.65,
//            'oz' => 9.65,
//            'pound' => (9.65 * 16), //154.4
//            'lb' => (9.65 * 16), //154.4
//            'gram' => (9.65 / 28.3495),
//            'g' => (9.65 / 28.3495),
//        ],
//        'trim' => [
//            'ounce' => 2.87,
//            'oz' => 2.87,
//            'pound' => (2.87 * 16), //45.92
//            'lb' => (2.87 * 16), //45.92
//            'gram' => (2.87 / 28.3495),
//            'g' => (2.87 / 28.3495),
//        ],
//        '1_8' => 1.16,
//        '1_4' => 2.31,
//        '1_2' => 4.63,
//    ],
    'cultivation_tax' => [
        'flower' => [
            'ounce' => 10.08,
            'oz' => 10.08,
            'pound' => (10.08 * 16), //161.28
            'lb' => (10.08 * 16), //161.28
            'gram' => (10.08 / 28.3495),
            'g' => (10.08 / 28.3495),
        ],
        'trim' => [
            'ounce' => 3,
            'oz' => 3,
            'pound' => (3 * 16), //48
            'lb' => (3 * 16), //48
            'gram' => (3 / 28.3495),
            'g' => (3 / 28.3495),
        ],
        '1_8' => 1.16,
        '1_4' => 2.31,
        '1_2' => 4.63,
    ],
    'sales_commission_start_date' => '2018-09-01',
    'payment_terms' => [
        '0'=>'Due On Receipt',
        '7'=>'Net 7',
        '15'=>'Net 15',
        '21'=>'Net 21',
        '30'=>'Net 30',
        '45'=>'Net 45',
        '60'=>'Net 60',
    ],
    'metrc_tag' => [
        '2' => '1A406030000592E', //distrubtion
        '3' => '1A4060300003B1A', //mfg
    ],
//    'uom' => [
//        'lb', 'g', 'unit', '1/8 oz', '1/4 oz', '1/2 oz', '1oz', '0.6g', '1.2g',
//    ],
    'uom' => [
        'lb'=>453.5924,
        'g'=>1,
        'kg'=>1000,
        'Unit'=>1,
        'Ea'=>1,
        'oz'=>0.0352739907,
//        'Case of 16'=>8,
//        'Case of 16 (0.5g)'=>8,
//        'Case of 16 (0.3g)'=>4.8,
//        'Case of 12'=>6,
//        'Case of 12 (0.5g)' => 6,
//        'Case 20 x 3g' => 60,
//        'Case 10 x 1g' => 10,
//        'Case 20 x 1g' => 20,
//        'Case 25 x 1g' => 25,
//        'Case 50 x 1g' => 50,
//        '1/8 oz'=>(28.3495/8),
//        '1/8 oz' => 3.54,
//        '1/4 oz'=>(28.3495/4),
//        '1/2 oz'=>(28.3495/2),
//        '1oz'=>28.3495,
//        '1/2g 7-Pack'=>3.5,
//        '0.3g'=>0.3,
//        '0.5g'=>0.5,
//        '1.2g'=>1.2,
//        '0.5g Joint'=>0.5,
//        '0.6g Joint'=>0.6,
//        '1g Mylar'=>1,
//        '2g Mylar'=>2,
//        '1g Joint'=>1,
//        '1.1g Joint'=>1.1,
//        '1.2g Joint'=>1.2,
//        '3-Pack 3.0g'=>3,
//        '7-Pack 3.5g'=>3.5,
//        '6 x 0.5g'=>3,
//        '16 x 1g'=>16,
//        '25 x 3g'=>75,
//        '32 1/8oz'=>  (3.54 * 32), // 113.28
//        '28 1/8oz'=> (3.54 * 28), // 99.12
//        '21 1/8oz'=>(3.54 * 21), // 74.34g

    ],
    'sell_price' => [
        1 => [ //Glass House
            '1/8 oz' => [
                'NorCal' => 14,
                'SoCal' => 14,
            ],
            '1g Joint' => [
                'NorCal' => '4.00',
                'SoCal' => '4.00'
            ]
        ],
        2 => [ //FadeCo
            '1/8 oz' => [
                'NorCal' => 20,
                'SoCal' => 20,
            ],
            '1g Joint' => [
                'NorCal' => '5.00',
                'SoCal' => '5.00'
            ],
            '1g Mylar' => [
                'NorCal' => '7.00',
                'SoCal' => '7.00'
            ],
            'g' => [
                'NorCal' => 20,
                'SoCal' => 20
            ],
            'Case 50 x 1g' => [
                'NorCal' => 300,
                'SoCal' => 300,
            ],
            '32 1/8oz' => [
                'NorCal' => 640,
                'SoCal' => 640,
            ],
        ],
        4 => [ //FLWR
            '1/8 oz' => [
                'NorCal' => '17.5',
                'SoCal' => '17.5',
            ],
            '1g Mylar' => [
                'NorCal' => '4.50',
                'SoCal' => '4.50'
            ],
            '6 x 0.5g' => [
                'NorCal' => '17.50',
                'SoCal' => '17.50'
            ],
            '21 1/8oz' => [
                'NorCal' => '367.50',
                'SoCal' => '367.50'
            ]
        ],
        5 => [ //Standard
            '1/8 oz' => [
                'NorCal' => '12.5',
                'SoCal' => '12.5',
            ],
            '1g Mylar' => [
                'NorCal' => '4.50',
                'SoCal' => '4.50'
            ]
        ],
        8 => [ //Roam
            '0.5g' => [
                'NorCal' => '19.5',
                'SoCal' => '19.5',
            ],
            'Case of 12 (0.5g)' => [
                'NorCal' => '234',
                'SoCal' => '234',
            ],
        ],
        10 => [ //ZaxWax
            'g' => [
                'NorCal' => '15',
                'SoCal' => '15',
            ],
            '16 x 1g' => [
                'NorCal' => '240',
                'SoCal' => '240',
            ],
            '25 x 1g' => [
                'NorCal' => '375',
                'SoCal' => '375',
            ],
        ],
        11 => [ //ZaxPax
            '3-Pack 3.0g' => [
                'NorCal' => '18',
                'SoCal' => '18',
            ],
            '7-Pack 3.5g' => [
                'NorCal' => '16',
                'SoCal' => '16',
            ],
        ],
        12 => [ //Pottery
            '1/8 oz' => [
                'NorCal' => 12.5,
                'SoCal' => 12.5,
            ],
            '1/4 oz' => [
                'NorCal' => 25,
                'SoCal' => 25,
            ]
        ],
        13 => [ //Cannabiotix
            '1/8 oz' => [
                'NorCal' => 20,
                'SoCal' => 20,
            ],
            '32 1/8oz' => [
                'NorCal' => 640,
                'SoCal' => 640,
            ],
        ],
        14 => [ //Cannary
            '1g Joint' => [
                'NorCal' => 5,
                'SoCal' => 5,
            ],
            '2g Mylar' => [
                'NorCal' => 10,
                'SoCal' => 10,
            ],
        ],
        15 => [ //FadeCo Exculsive
            '1/8 oz' => [
                'NorCal' => 25,
                'SoCal' => 25,
            ],
            '28 1/8oz' => [
                'NorCal' => 700,
                'SoCal' => 700,
            ],
            'Case 50 x 1g' => [
                'NorCal' => 250,
                'SoCal' => 250,
            ]
        ]
    ],
    'sale_types' => [
        'packaged',
        'bulk',
//        'demo',
        'sample',
//        'co-pack',
//        'promotional',
//        'transfer',
    ],
    'flower_character' => [
        'Indoor', 'Outdoor', 'Deps', 'Strong Nose', 'Light Nose', 'Dry', 'Wet', 'Big Buds', 'Small Buds', 'Lots of Shake', 'Little Shake',
    ],
    'batch_statuses' => [
        'Received','Lab','Inventory','Rejected','Destroyed','Failed','Transferred','Processing'
    ],
    'testing_statuses' => [
        'Pending','In-Testing','R&D','Passed','Failed'
    ],
    'po_statuses' => [
        'open', 'closed',
    ],
//    'po_statuses' => [
//        'Unpaid','Partially Paid','Paid',
//    ],
    'order_statuses' => [
        'open', 'ready for delivery', 'in-transit', 'delivered'
//        ,'returned', 'rejected',
    ],
    'conversions' => [
        'grams_per_ounce' => 28.35,
        'grams_per_pound' => 453.5924,
        'oz_per_g' => 0.035274,
        'oz_per_lb' => 16,
    ],
    'license_name' => 'Owls, Inc.',
    'license_name_DBA' => '',
    'license_number_adult' => 'C11-0000347-LIC',
    'license_number_med' => 'C11-0000347-LIC',
    'license' => [
        'legal_name' => 'Owls, Inc.',
        'address' => '101 Test Dr.',
        'address2' => 'Los Angeles, CA 90064',
        'adult' => 'C11-0000347-LIC',
        'med' => 'C11-0000347-LIC',
    ],
    'vault_log_access'=>[
        13, //Dan
        14, //Nick
        15, //Chris
        164, //Ash
        452, //Tera
        582, //Trey
    ],
    'vault_log_sms_ids'=>[
        13, //Dan
        14, //Nick
        164, //Ash
        452, //Tera
        582, //Trey
    ]
];

