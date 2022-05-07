<?php

namespace EzPlatform\ActivitiesLogBundle\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class UIEzPlatformActivitiesLogPolicyProvider extends YamlPolicyProvider
{
    /** @var string $path bundle path */
    protected $path;

    /**
     * UIEzPlatformActivitiesLogPolicyProvider constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return [$this->path . '/Resources/config/policies.yaml'];
    }
}
