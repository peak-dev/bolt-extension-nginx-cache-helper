<?php

namespace Bolt\Extension\Bolt\NginxCacheHelper;

use Bolt\Events\StorageEvent;
use Bolt\Events\StorageEvents;
use Bolt\Extension\SimpleExtension;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Nginx Cache Helper extension loader.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class NginxCacheHelperExtension extends SimpleExtension
{
    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return 'Nginx Cache Helper';
    }

    /**
     * {@inheritdoc}
     */
    protected function subscribe(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(StorageEvents::POST_SAVE, array($this, 'hookPostSave'));
    }

    /**
     * Post save hook
     *
     * @param StorageEvent $event
     */
    public function hookPostSave(StorageEvent $event)
    {
        $app = $this->getContainer();
        $config = $this->getConfig();
        $content = $event->getContent();
        $rootUrl = $app['resources']->getUrl('rooturl');

        $url = sprintf('%s%s', $rootUrl, $config['nginx_purge_uri']);
        try {
            $app['guzzle.client']->get($url);
        } catch (ClientException $e) {
            $app['logger.flash']->error(sprintf('Nginx cache purge failed for route %s with error: %s', $url, $e->getMessage()));
        }

        $url = sprintf('%s%s/%s', $rootUrl, $content->link(), $config['nginx_purge_uri']);
        try {
            $app['guzzle.client']->get($url);
        } catch (ClientException $e) {
            $app['logger.flash']->error(sprintf('Nginx cache purge failed for route %s with error: %s', $url, $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig()
    {
        return array(
            'nginx_purge_uri' => 'purge'
        );
    }
}
