<?php

namespace Zelenin\SmsRu\Response;

use Zelenin\SmsRu\Entity\StoplistPhone;

class StoplistGetResponse extends AbstractResponse
{

    /**
     * @var StoplistPhone[]
     */
    public $phones = [];

    /**
     * @var array
     */
    protected $availableDescriptions = [
        '100' => '«апрос обработан. Ќа последующих строчках будут идти номера телефонов, указанных в стоплисте в формате номер;примечание.',
    ];
}
