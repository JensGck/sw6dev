<?php declare(strict_types=1);

namespace Jego\Dev;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Symfony\Component\Process\Process;

class Dev extends Plugin
{
    private const READ  = 'read';
    private const WRITE = 'write';

    private const DEV_INTEGRATION = [
        self::READ  => [
            'writeAccess'     => false,
            'admin'           => true,
            'label'           => self::READ,
            'secretAccessKey' => self::READ,
            'accessKey'       => self::READ
        ],
        self::WRITE => [
            'writeAccess'     => true,
            'admin'           => true,
            'label'           => self::WRITE,
            'secretAccessKey' => self::WRITE,
            'accessKey'       => self::WRITE
        ],
    ];

    public function install(InstallContext $installContext): void
    {
        $context = $installContext->getContext();
        $this->integrations($this->container->get('integration.repository'), $context);
        $this->symLink($installContext->getPlugin()->getBasePath(), $installContext->getCurrentShopwareVersion());
    }

    private function symLink(string $path, string $shopwareVersion): void
    {
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $symlinkPath = substr($path, 0, strrpos($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . 'jego';
        } elseif (!empty($_SERVER['PROJECT_ROOT'])) {
            $symlinkPath = "{$_SERVER['PROJECT_ROOT']}/jego";
        } else {
            return;
        }

        $command = "ln -s {$path}/bin {$symlinkPath}";
        if (version_compare($shopwareVersion, '4.0.0', '>=')) {
            $command = [$command];
        }

        (new Process($command))->run();
    }

    private function integrations(EntityRepositoryInterface $repo, Context $context): void
    {
        if (null === $repo) {
            return;
        }

        $create = [];
        foreach (self::DEV_INTEGRATION as $k => $int) {
            if (!$this->integrationExist($repo, $context, $k)) {
                $create[] = $int;
            }
        }

        if (\count($create)) {
            $repo->create($create, $context);
        }
    }

    private function integrationExist(EntityRepositoryInterface $repo, Context $context, string $label): bool
    {
        return $repo->search((new Criteria())->addFilter(new EqualsFilter('label', $label)), $context)->count() > 0;
    }
}
