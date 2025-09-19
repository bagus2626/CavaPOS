<?php
// resources/lang/id/validation.php
return [
    'required' => ':Attribute wajib diisi.',
    'exists'   => ':Attribute yang dipilih tidak valid.',
    'image'    => ':Attribute harus berupa gambar.',
    'mimes'    => ':Attribute harus bertipe: :values.',
    'max'      => [
        'file' => ':Attribute tidak boleh lebih dari :max kilobyte.',
        'array' => ':Attribute tidak boleh memiliki lebih dari :max item.',
        'string' => 'Panjang :attribute tidak boleh lebih dari :max karakter.',
    ],
    'array'    => ':Attribute harus berupa array.',
    'numeric'  => ':Attribute harus berupa angka.',

    'custom' => [
        'name' => [
            'required' => 'Nama produk wajib diisi.',
        ],
    ],

    'attributes' => [],
];
