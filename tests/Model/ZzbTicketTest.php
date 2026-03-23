<?php

namespace ZzbSdkTests\Model;

use PHPUnit\Framework\TestCase;
use ZzbSdk\Model\ZzbTicket;

class ZzbTicketTest extends TestCase
{
    public function testFromArray()
    {
        $data = [
            'numberByDay' => 1,
            'parentChannelCode' => '00000000',
            'childChannelCode' => '00000000',
            'ticketNo' => '130904010Go0102',
            'cinemaCode' => '13090401',
            'screenCode' => '0000000000000001',
            'seatCode' => '88888888010010011101',
            'filmCode' => '000000252022',
            'sessionCode' => 'SE00001234567890',
            'sessionDatetime' => '2023-11-09 16:31:35',
            'ticketPrice' => 45.00,
            'screenServiceFee' => 3.36,
            'netServiceFee' => 1.47,
            'saleChannelCode' => '98151235',
            'operation' => 1,
            'operationDatetime' => '2023-11-09 16:42:17',
        ];

        $ticket = ZzbTicket::fromArray($data);

        $this->assertEquals(1, $ticket->numberByDay);
        $this->assertEquals('130904010Go0102', $ticket->ticketNo);
        $this->assertEquals(45.00, $ticket->ticketPrice);
    }

    public function testToArray()
    {
        $ticket = new ZzbTicket();
        $ticket->numberByDay = 1;
        $ticket->ticketNo = '123456';
        $ticket->ticketPrice = 50.00;

        $array = $ticket->toArray();

        $this->assertArrayHasKey('numberByDay', $array);
        $this->assertEquals(1, $array['numberByDay']);
        $this->assertEquals('123456', $array['ticketNo']);
        $this->assertEquals(50.00, $array['ticketPrice']);
    }
}
