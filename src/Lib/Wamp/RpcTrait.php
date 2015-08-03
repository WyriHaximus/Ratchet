<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Lib\Wamp;

use Ratchet\ConnectionInterface as Conn;

trait RpcTrait
{
    public function onCall(Conn $conn, $id, $topic, array $params)
    {
        $topicName = self::getTopicName($topic);
        $eventPayload = [
        'connection' => $conn,
        'id' => $id,
        'connectionData' => $this->_connections[$conn->WAMP->sessionId],
        ];

        $event = $this->dispatchEvent(
        'Rachet.WampServer.Rpc',
        $this,
        array_merge($eventPayload, [
        'topicName' => $topicName,
        'topic' => $topic,
        'params' => $params,
        'wampServer' => $this,
        ])
        );

        if ($event->isStopped()) {
            $conn->callError($id, $event->result['stop_reason']['error_uri'], $event->result['stop_reason']['desc'],
            $event->result['stop_reason']['details']);
            $this->outVerbose('Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') was blocked');
            $this->dispatchEvent(
            'Rachet.WampServer.RpcBlocked',
            $this,
            array_merge($eventPayload, [
            'topicName' => $topicName,
            'reason' => $event->result['stop_reason'],
            ])
            );

            return false;
        }

        $start = microtime(true);

        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
        function ($results) use ($conn, $id, $topicName, $start, $eventPayload) {
            $end = microtime(true);

            $conn->callResult($id, $results);
            $this->outVerbose('Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') took <info>' . ($end - $start) . 's</info> and succeeded');
            $this->dispatchEvent(
            'Rachet.WampServer.RpcSuccess',
            $this,
            array_merge($eventPayload, [
            'topicName' => $topicName,
            'results' => $results,
            ])
            );
        },
        function ($errorUri, $desc = '', $details = null) use ($conn, $id, $topicName, $start, $eventPayload) {
            $end = microtime(true);

            $conn->callError($id, $errorUri, $desc, $details);
            $this->outVerbose('Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') took <info>' . ($end - $start) . 's</info> and failed');
            $this->dispatchEvent(
            'Rachet.WampServer.RpcFailed',
            $this,
            array_merge($eventPayload, [
            'topicName' => $topicName,
            'reason' => [
            $errorUri,
            $desc,
            $details,
            ],
            ])
            );
        }
        );

        $this->dispatchEvent(
        'Rachet.WampServer.Rpc.' . $topicName,
        $this,
        array_merge($eventPayload, [
        'promise' => $deferred,
        'topic' => $topic,
        'params' => $params,
        'wampServer' => $this,
        ])
        );
    }
} 