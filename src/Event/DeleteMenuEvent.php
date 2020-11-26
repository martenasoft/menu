<?php

namespace MartenaSoft\Menu\Event;

use MartenaSoft\Common\Event\CommonEventInterface;

class DeleteMenuEvent extends AbstractMenuEvent implements CommonEventInterface, SaveMenuEventInterface
{
    public static function getEventName(): string
    {
        return "delete.menu.event";
    }
}