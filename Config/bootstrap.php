<?php

App::uses('CakeEventManager', 'Event');

App::uses('RatchetCallUrlListener', 'Ratchet.Event');
App::uses('RatchetConnectionStatisticsListener', 'Ratchet.Event');

CakeEventManager::instance()->attach(new RatchetCallUrlListener());
CakeEventManager::instance()->attach(new RatchetConnectionStatisticsListener());