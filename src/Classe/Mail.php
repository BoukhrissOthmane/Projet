<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = '4e60bc287132a9e8d510caeb09fd4af5';
    private $api_key_secret = '67ea273e32883c2a2bdeca792ece9ded';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "bkrotm11@gmail.com",
                        'Name' => "Monde De Basket"
                    ],
                    'To' => [
                        [
                    'Email' => $to_email,
                    'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3843707,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}