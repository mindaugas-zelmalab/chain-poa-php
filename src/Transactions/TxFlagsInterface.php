<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

/**
 * Interface TxFlagsInterface
 * @package ForwardBlock\Chain\PoA\Transactions
 */
interface TxFlagsInterface
{
    /** @var int */
    public const TX_FLAG_REGISTER = 0x64;
    /** @var int */
    public const TX_FLAG_TRANSFER = 0xc8;
}
