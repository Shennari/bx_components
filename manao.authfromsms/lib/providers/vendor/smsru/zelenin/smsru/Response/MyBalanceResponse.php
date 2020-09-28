<?php

namespace Zelenin\SmsRu\Response;

class MyBalanceResponse extends AbstractResponse
{

    /**
     * @var float
     */
    public $balance;

    /**
     * @var array
     */
    protected $availableDescriptions = [
        '100' => '������ ��������. �� ������ ������� �� ������� ���� ������� ��������� �������.',
        '200' => '������������ api_id.',
        '210' => '������������ GET, ��� ���������� ������������ POST.',
        '211' => '����� �� ������.',
        '220' => '������ �������� ����������, ���������� ���� �����.',
        '300' => '������������ token (�������� ����� ���� ��������, ���� ��� IP ���������).',
        '301' => '������������ ������, ���� ������������ �� ������.',
        '302' => '������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���).',
    ];
}
