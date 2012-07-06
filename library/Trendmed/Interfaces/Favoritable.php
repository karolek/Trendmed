<?php
namespace Trendmed\Interfaces;

interface Favoritable
{
    public function getId();
    public function isFavoredByUser(\Trendmed\Entity\User $user);
    public function addFavoredByUser(\Trendmed\Entity\Patient $patient);
}
