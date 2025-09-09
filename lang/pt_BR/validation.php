<?php

return [
    'required'   => 'O campo :attribute é obrigatório.',
    'email'      => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'min'        => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'max'        => [
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
    ],
    'confirmed'  => 'A confirmação do campo :attribute não confere.',
    'unique'     => 'Este :attribute já está em uso.',

    // Tradução dos nomes dos atributos
    'attributes' => [
        'email'    => 'E-mail',
        'name'     => 'Nome',
        'lastname' => 'Sobrenome',
        'phone'    => 'Telefone',
        'password' => 'Senha',
    ],
];