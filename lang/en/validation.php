<?php
// resources/lang/en/validation.php
return [
    'required' => 'The :attribute field is required.',
    'exists'   => 'The selected :attribute is invalid.',
    'image'    => 'The :attribute must be an image.',
    'mimes'    => 'The :attribute must be a file of type: :values.',
    'max'      => [
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'array' => 'The :attribute may not have more than :max items.',
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'array'    => 'The :attribute must be an array.',
    'numeric'  => 'The :attribute must be a number.',

    // custom messages per field (opsional)
    'custom' => [
        'name' => [
            'required' => 'Product name is required.',
        ],
    ],

    // attribute nice names (opsional)
    'attributes' => [],
];
