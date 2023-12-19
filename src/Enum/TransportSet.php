<?php

namespace Dmoldovanu\Cargo\Enum;

interface TransportSet
{
    const One = [
        [
            'count' => 27,
            'sizes' => [
                'width' => 78,
                'height' => 79,
                'length' => 93,
            ],
        ],
    ];

    const Two = [
        [
            'count' => 24,
            'sizes' => [
                'width' => 30,
                'height' => 60,
                'length' => 90,
            ],
        ],
        [
            'count' => 33,
            'sizes' => [
                'width' => 75,
                'height' => 100,
                'length' => 200,
            ],
        ],
    ];

    const Three = [
        [
            'count' => 10,
            'sizes' => [
                'width' => 80,
                'height' => 100,
                'length' => 200,
            ],
        ],
        [
            'count' => 25,
            'sizes' => [
                'width' => 60,
                'height' => 80,
                'length' => 150,
            ],
        ],
    ];

    const Four = [
        [
            'count' => 10,
            'sizes' => [
                'width' => 200,
                'height' => 100,
                'length' => 80,
            ],
        ],
        [
            'count' => 25,
            'sizes' => [
                'width' => 150,
                'height' => 80,
                'length' => 60,
            ],
        ],
    ];
}
