<?php

declare(strict_types=1);

namespace JTG\Mark\Repository;

class PostRepository extends FileRepository
{
    public function __construct(string $projectPostDir)
    {
        parent::__construct($projectPostDir);
    }
}