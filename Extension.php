<?php

namespace Bolt\Extension\Bolt\NginxCacheHelper;

use Bolt\Events\StorageEvent;
use Bolt\Events\StorageEvents;

class Extension extends \Bolt\BaseExtension
{

    public function getName()
    {
        return "NginxCacheHelper";
    }

    public function initialize()
    {
        /*
         * Backend
         */
        if ($this->app['config']->getWhichEnd() == 'backend') {
            $this->app['dispatcher']->addListener(StorageEvents::POST_SAVE, array($this, 'hookPostSave'));
        }
    }

    /**
     * Post save hook
     *
     * @param StorageEvent $input
     */
    public function hookPostSave(StorageEvent $input)
    {
        // Get the content
        $content = $input->getContent();
        $host = $this->app['paths']['hosturl'];

        // Purge it
        $this->callPurgeFastCGIUrl($host . $this->config['nginx_purge_uri']);
        $this->callPurgeFastCGIUrl($host . $content->link() . '/' . $this->config['nginx_purge_uri']);
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
