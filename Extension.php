<?php

namespace Bolt\Extension\Bolt\NginxCacheHelper;

use Bolt\StorageEvents;

class Extension extends \Bolt\BaseExtension
{

    public function getName()
    {
        return "NginXCacheHelper";
    }

    public function initialize() {
        $this->app['dispatcher']->addListener(StorageEvents::POST_SAVE, array($this, 'hookPostSave'));
    }

    public function hookPostSave($input) {

        // Get the content
        $content = $input->getContent();
        $host = $this->app['paths']['hosturl'];

        // Purge it
        $this->callPurgeFastCGIUrl($host . $this->config['purge']);
        $this->callPurgeFastCGIUrl($host . $content->link() . $this->config['purge']);
    }

    /**
     *
     * @param string $url
     */
    private function callPurgeFastCGIUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Default config
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return array(
            'nginx_purge_uri' => 'purge'
        );
    }
}
