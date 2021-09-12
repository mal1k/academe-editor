<?php
/**
 * Get lesson slide templates for render
 */
function get_lesson_slide_templates() {

    $templates = [
        'template1' => [
            'template1_text1' => [
                'type' => 'text',
                'label' => 'Text design',
                'fields' => [
                    'template1_text1_font' => [
                        'type' => 'select',
                        'label' => 'Font',
                        'values' => [
                            'arial' => 'Arial',
                            'courier_new' => 'Courier New',
                            'montserrat' => 'Montserrat',
                            'times_new_roman' => 'Times New Roman',
                        ]
                    ],
                    'template1_text1_font_weight' => [
                        'type' => 'select',
                        'label' => 'Font weight',
                        'values' => [
                            '200' => 'Light',
                            '400' => 'Regular',
                            '600' => 'Bold',
                        ]
                    ],
                    'template1_text1_font_size' => [
                        'type' => 'select',
                        'label' => 'Font size',
                        'values' => [
                            "10px" => '10',
                            '11px' => '11',
                            '12px' => '12',
                            '14px' => '14',
                            '16px' => '16',
                            '18px' => '18',
                            '20px' => '20',
                            '24px' => '24',
                            '28px' => '28',
                            '30px' => '30',
                            '32px' => '32',
                            '40px' => '40',
                            '48px' => '48'
                        ]
                    ],
                    'template1_text1_text_align' => [
                        'type' => 'alignment',
                        'label' => 'Align',
                        'values' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right',
                        ]
                    ],
                    'template1_text1_text_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Text Color',
                    ],
                    'template1_text1_fill_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Fill Color',
                    ],
//                    'template1_text1_text_opacity' => [  //No need because of transparency presented in colorpicker
//                        'type' => 'range',
//                        'label' => 'Opacity',
//                        'from' => 0,
//                        'to' => 100,
//                        'step' => 1,
//                    ],
                    'template1_text1_text' => [
                        'type' => 'textarea',
                        'label' => 'Text',
                    ],
                ]
            ],
            'template1_text2' => [
                'type' => 'text',
                'label' => 'Text design',
                'fields' => [
                    'template1_text2_font' => [
                        'type' => 'select',
                        'label' => 'Font',
                        'values' => [
                            'arial' => 'Arial',
                            'courier_new' => 'Courier New',
                            'montserrat' => 'Montserrat',
                            'times_new_roman' => 'Times New Roman',
                        ]
                    ],
                    'template1_text2_font_weight' => [
                        'type' => 'select',
                        'label' => 'Font weight',

                        'values' => [
                            '200' => 'Light',
                            '400' => 'Regular',
                            '600' => 'Bold',
                        ]
                    ],
                    'template1_text2_font_size' => [
                        'type' => 'select',
                        'label' => 'Font size',
                        'values' => [
                            "10px" => '10',
                            '11px' => '11',
                            '12px' => '12',
                            '14px' => '14',
                            '16px' => '16',
                            '18px' => '18',
                            '20px' => '20',
                            '24px' => '24',
                            '28px' => '28',
                            '30px' => '30',
                            '32px' => '32',
                            '40px' => '40',
                            '48px' => '48'
                        ]
                    ],
                    'template1_text2_text_align' => [
                        'type' => 'alignment',
                        'label' => 'Align',
                        'values' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right',
                        ]
                    ],
                    'template1_text2_text_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Text Color',
                    ],
                    'template1_text2_fill_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Fill Color',
                    ],
//                    'template1_text2_text_opacity' => [  //No need because of transparency presented in colorpicker
//                        'type' => 'range',
//                        'label' => 'Opacity',
//                        'from' => 0,
//                        'to' => 100,
//                        'step' => 1,
//                    ],
                    'template1_text2_text' => [
                        'type' => 'textarea',
                        'label' => 'Text',
                    ],
                ]
            ],
            'template1_text3' => [
                'type' => 'text',
                'label' => 'Text design',
                'fields' => [
                    'template1_text3_font' => [
                        'type' => 'select',
                        'label' => 'Font',
                        'values' => [
                            'arial' => 'Arial',
                            'courier_new' => 'Courier New',
                            'montserrat' => 'Montserrat',
                            'times_new_roman' => 'Times New Roman',
                        ]
                    ],
                    'template1_text3_font_weight' => [
                        'type' => 'select',
                        'label' => 'Font weight',

                        'values' => [
                            '200' => 'Light',
                            '400' => 'Regular',
                            '600' => 'Bold',
                        ]
                    ],
                    'template1_text3_font_size' => [
                        'type' => 'select',
                        'label' => 'Font size',
                        'values' => [
                            "10px" => '10',
                            '11px' => '11',
                            '12px' => '12',
                            '14px' => '14',
                            '16px' => '16',
                            '18px' => '18',
                            '20px' => '20',
                            '24px' => '24',
                            '28px' => '28',
                            '30px' => '30',
                            '32px' => '32',
                            '40px' => '40',
                            '48px' => '48'
                        ]
                    ],
                    'template1_text3_text_align' => [
                        'type' => 'alignment',
                        'label' => 'Align',
                        'values' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right',
                        ]
                    ],
                    'template1_text3_text_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Text Color',
                    ],
                    'template1_text3_fill_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Fill Color',
                    ],
                    'template1_text3_text' => [
                        'type' => 'textarea',
                        'label' => 'Text',
                    ],
                ]
            ],
            'template1_text4' => [
                'type' => 'text',
                'label' => 'Text design',
                'fields' => [
                    'template1_text4_font' => [
                        'type' => 'select',
                        'label' => 'Font',
                        'values' => [
                            'arial' => 'Arial',
                            'courier_new' => 'Courier New',
                            'montserrat' => 'Montserrat',
                            'times_new_roman' => 'Times New Roman',
                        ]
                    ],
                    'template1_text4_font_weight' => [
                        'type' => 'select',
                        'label' => 'Font weight',

                        'values' => [
                            '200' => 'Light',
                            '400' => 'Regular',
                            '600' => 'Bold',
                        ]
                    ],
                    'template1_text4_font_size' => [
                        'type' => 'select',
                        'label' => 'Font size',
                        'values' => [
                            "10px" => '10',
                            '11px' => '11',
                            '12px' => '12',
                            '14px' => '14',
                            '16px' => '16',
                            '18px' => '18',
                            '20px' => '20',
                            '24px' => '24',
                            '28px' => '28',
                            '30px' => '30',
                            '32px' => '32',
                            '40px' => '40',
                            '48px' => '48'
                        ]
                    ],
                    'template1_text4_text_align' => [
                        'type' => 'alignment',
                        'label' => 'Align',
                        'values' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right',
                        ]
                    ],
                    'template1_text4_text_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Text Color',
                    ],
                    'template1_text4_fill_color' => [
                        'type' => 'colorpicker',
                        'label' => 'Fill Color',
                    ],
                    'template1_text4_text' => [
                        'type' => 'textarea',
                        'label' => 'Text',
                    ],
                ]
            ],
            'template1_media1' => [
                'type' => 'media',
                'label' => 'Media',
                'fields' => [
                    'template1_media1_image' => [
                        'type' => 'image',
                        'label' => 'Image',
                    ],
//                    'template1_media1_video' => [  // Video is not necessary at rhis stage ( issue: how to click on slide block to open meta if video already loaded? )
//                        'type' => 'text',
//                        'label' => 'Video',
//                    ],
                ]
            ],
            'template1_media2' => [
                'type' => 'media',
                'label' => 'Media',
                'fields' => [
                    'template1_media2_image' => [
                        'type' => 'image',
                        'label' => 'Image',
                    ],
                ]
            ],
            'template1_media3' => [
                'type' => 'media',
                'label' => 'Media',
                'fields' => [
                    'template1_media3_image' => [
                        'type' => 'image',
                        'label' => 'Image',
                    ],
                ]
            ],
            'template1_media4' => [
                'type' => 'media',
                'label' => 'Media',
                'fields' => [
                    'template1_media4_image' => [
                        'type' => 'image',
                        'label' => 'Image',
                    ],
                ]
            ],
        ],
    ];

    return $templates;
}