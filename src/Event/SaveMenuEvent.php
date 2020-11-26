<?php

namespace MartenaSoft\Menu\Event;

use MartenaSoft\Common\Event\CommonEventInterface;

class SaveMenuEvent extends AbstractMenuEvent implements CommonEventInterface, SaveMenuEventInterface
{
    public static function getEventName(): string
    {
        return "save.menu.event";
    }
}

