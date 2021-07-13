<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use ForwardBlock\Chain\PoA\Transactions\ProtocolTxConstructor;
use ForwardBlock\Protocol\Transactions\Traits\CustomDataTrait;
use ForwardBlock\Protocol\Transactions\Traits\TransferObjectsTrait;

/**
 * Class TransferTxConstructor
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class TransferTxConstructor extends ProtocolTxConstructor
{
    use TransferObjectsTrait;
    use CustomDataTrait;

    protected function beforeSerialize(): void
    {
    }
}
