<?php
namespace Alive;
class AfterActionReplay {
    private $sides = [
        'EAST'    => 0,
        'WEST'    => 1,
        'GUER'    => 2,
        'CIV'     => 3,
        'UNKNOWN' => 3
    ];
    private $map = [
        'type' => [
            'positions_infantry' => [
                'playerId' => 'Player',
                'type' => 'type',
                'missionTime' => 'missionTime',
                'realTime' => 'realTime',
                'value' => [
                    'unit' => 'AAR_unit',
                    'id'   => 'AAR_playerUID',
                    'pos'  => 'AAR_pos',
                    'dir'  => 'AAR_dir',
                    'ico'  => 'AAR_ico',
                    'fac'  => [
                        'options' => [
                            'key' => 'AAR_side',
                            'map' => 'sides'
                        ]
                    ],
                    'grp'  => 'AAR_groupid',
                    'ldr'  => 'AAR_isLeader'
                ]
            ],
            'positions_vehicles' => [
                'playerId' => 'Player',
                'type' => 'type',
                'missionTime' => 'missionTime',
                'realTime' => 'realTime',
                'value' => [
                    'unit' => 'AAR_unit',
                    'id'   => 'AAR_playerUID',
                    'pos'  => 'AAR_pos',
                    'dir'  => 'AAR_dir',
                    'cls'  => 'AAR_config',
                    'ico'  => 'AAR_ico',
                    'icp'  => '',
                    'fac'  => [
                        'options' => [
                            'key' => 'AAR_side',
                            'map' => 'sides'
                        ]
                    ],
                    'grp'  => 'AAR_groupid',
                    'crw'  => 'AAR_crew',
                    'cgo'  => 'AAR_cargo'
                ]
            ],
        ],
        'Event' => [
            'GetIn' => [
                'playerId' => 'Player',
                'type' => [
                    'options' => [
                        'key' => 'Event',
                        'sub' => 'get_in'
                    ]
                ],
                'missionTime' => 'missionTime',
                'realTime' => 'realTime',
                'value' => [
                    'unit' => 'unit',
                    'id'   => 'Player'
                ]
            ],
            'GetOut' => [
                'playerId' => 'Player',
                'type' => [
                    'options' => [
                        'key' => 'Event',
                        'sub' => 'get_out'
                    ]
                ],
                'missionTime' => 'missionTime',
                'realTime' => 'realTime',
                'value' => [
                    'unit' => 'unit',
                    'id'   => 'Player'
                ]
            ],
            'Kill' => [
                'playerId' => 'Player',
                'type' => [
                    'options' => [
                        'key' => 'Event',
                        'sub' => 'unit_killed'
                    ]
                ],
                'missionTime' => 'missionTime',
                'realTime' => 'realTime',
                'value' => [
                    'victim' => [
                        'unit'    => 'Killed',
                        'id'      => [
                            'options' => [
                                'key' => 'Player',
                                'conditions' => [
                                    'isset' => 'Death'
                                ]
                            ]
                        ],
                        'pos'     => 'KilledGeoPos',
                        'type'    => 'KilledType',
                        'faction' => [
                            'options' => [
                                'key' => 'KilledSide',
                                'map' => 'sides'
                            ]
                        ]
                    ],
                    'attacker' => [
                        'unit'     => 'Killer',
                        'id'       => [
                            'options' => [
                                'key' => 'Player',
                                'conditions' => [
                                    'notisset' => 'Death'
                                ]
                            ]
                        ],
                        'pos'      => 'KillerGeoPos',
                        'type'     => 'KillerType',
                        'faction' => [
                            'options' => [
                                'key' => 'KillerSide',
                                'map' => 'sides'
                            ]
                        ],
                        'weapon'   => 'Weapon',
                        'distance' => 'Distance'
                    ]
                ]
            ]
        ]
    ];

    public function convert($data) {
        $result = [];

        foreach ($data['response']['rows'] as $event) {
            $mapping = $this->getMapping($event['value']);

            if (!empty($mapping)) {
                $value = $this->getValues($event['value'], $mapping);

                if (isset($value['value'])) {
                    // Encode value attribute because R3 expects it
                    $value['value'] = json_encode($value['value']);
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    private function getMapping($array) {
        $keys = array_keys($this->map);

        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $keys2 = array_keys($this->map[$key]);

                foreach ($keys2 as $key2) {
                    if ($key2 === $array[$key]) {
                        return $this->map[$key][$key2];
                    }
                }
            }
        }

        return [];
    }

    private function getValues($array, $mapping) {
        $result = [];

        foreach ($mapping as $k => $v) {
            if (is_array($v) && isset($array[$k])) {
                $keys = array_keys($array[$k]);

                // Array is associative
                if (array_keys($keys) !== $keys) {
                    $result[$k] = $this->getValues($array[$k], $v);
                } else {
                    foreach ($array[$k] as $item) {
                        $result[$k][] = $this->getValues($item, $mapping[$k]);
                    }
                }
            } else if (is_array($v) && !isset($v['options'])) {
                $result[$k] = $this->getValues($array, $v);
            } else {
                if (is_array($v)) {
                    $key = $array[$v['options']['key']];
                    $value = $array[$v['options']['key']];

                    // 'evaluate' conditions
                    if (isset($v['options']['conditions'])) {
                        foreach ($v['options']['conditions'] as $ck => $cv) {
                            switch ($ck) {
                                // if not set, clear $value
                                case 'isset':
                                    if (!isset($array[$cv])) {
                                        $value = '';
                                    }
                                    break;
                                // if set, clear $value
                                case 'notisset':
                                    if (isset($array[$cv])) {
                                        $value = '';
                                    }
                                    break;
                            }
                        }
                    }

                    if (isset($v['options']['map'])) {
                        // get map from class property
                        $value = $this->{$v['options']['map']}[$value];
                    } else if (isset($v['options']['sub'])) {
                        $value = $v['options']['sub'];
                    }

                    $result[$k] = $value;
                } else if (isset($array[$v])) {
                    $result[$k] = $array[$v];
                } else {
                    $result[$k] = '';
                }
            }
        }

        return $result;
    }
}














