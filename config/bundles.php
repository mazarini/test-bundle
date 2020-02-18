<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    Mazarini\ToolsBundle\MazariniToolsBundle::class => ['all' => true],
    Mazarini\TestBundle\MazariniTestBundle::class => ['dev' => true, 'test' => true],
    Mazarini\PackageBundle\MazariniPackageBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['test' => true],
    Mazarini\BootstrapBundle\MazariniBootstrapBundle::class => ['dev' => true, 'test' => true],
];
