<?php

namespace MartenaSoft\Menu\Event;

use MartenaSoft\Common\Event\CommonEventInterface;
use MartenaSoft\Trash\Entity\InitTrashMethodsTrait;
use MartenaSoft\Trash\Entity\TrashEntityInterface;

class DeleteMenuEvent extends AbstractMenuEvent implements CommonEventInterface, SaveMenuEventInterface, TrashEntityInterface
{
    use InitTrashMethodsTrait;

    public static function getEventName(): string
    {
        return "delete.menu.event";
    }
}