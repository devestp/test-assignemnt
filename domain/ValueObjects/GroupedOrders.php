<?php

namespace Domain\ValueObjects;

use Illuminate\Support\Collection;

readonly class GroupedOrders
{
    /**
     * @param  Collection<GroupedOrder>  $groups
     */
    public function __construct(
        private Collection $groups,
    ) {}

    /**
     * @return Collection<GroupedOrder>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }
}
