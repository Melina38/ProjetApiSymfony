<?php
//src/Model/QueryDTO.php
namespace App\Model;

use Symfony\Component\Validator\Constraints\NotBlank;

class QueryDTO
{
    public function __construct(
        #[NotBlank]
        public string $message = "Hey this is my default value!",
    ) {}
}
