<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Blocks;

use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Protocol\Blocks\AbstractBlockForge;

/**
 * Class BlockConstructor
 * @package ForwardBlock\Chain\PoA\Blocks
 */
class BlockConstructor extends AbstractBlockForge
{
    /**
     * BlockConstructor constructor.
     * @param ForwardPoA $p
     * @param string $prevBlock
     * @param int $ver
     * @param int $epoch
     * @throws \ForwardBlock\Protocol\Exception\BlockForgeException
     */
    public function __construct(ForwardPoA $p, string $prevBlock, int $ver, int $epoch)
    {
        parent::__construct($p, $prevBlock, $ver, $epoch);
    }

    public function onConstructCallback(): void
    {
    }

    protected function generateFinalReceipts(): void
    {
    }
}
