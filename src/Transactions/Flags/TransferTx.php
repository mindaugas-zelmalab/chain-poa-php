<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;

/**
 * Class TransferTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class TransferTx extends AbstractPreparedTx
{
    protected function decodeCallback(): void
    {
    }
}
