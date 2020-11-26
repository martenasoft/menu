<?php

namespace MartenaSoft\Menu\EventListener;

use MartenaSoft\Menu\Event\SaveMenuEvent;

class BeforeSaveEventListener
{
    public function onBeforeSave(SaveMenuEvent $event)
    {
        dump($event);
        die;
    }
}
