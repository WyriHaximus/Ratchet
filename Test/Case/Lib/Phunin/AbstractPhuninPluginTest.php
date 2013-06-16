<?php

abstract class AbstractPhuninPluginTest extends PhuninNode\Tests\Plugins\AbstractPluginTest {
    
    /**
     * {@inheritdoc}
     */
    public function setUp() {
        parent::setUp();
        
        $this->_pluginPath = App::pluginPath('Ratchet');
        App::build(array(
            'Plugin' => array($this->_pluginPath . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS )
        ));
        CakePlugin::load('TestRatchet');
        
        Configure::write('Ratchet.Queue', array(
            'transporter' => 'TestRatchet.DummyTransport',
            'configuration' => array(
                'server' => 'tcp://127.0.0.1:13001',
            ),
        ));
        
        $this->TransportProxy = TransportProxy::instance();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        unset($this->TransportProxy);
        
        CakePlugin::unload('TestRatchet');
        
        parent::tearDown();
    }
    
}