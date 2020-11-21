<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

/**
 * Interface TxFlagsInterface
 * @package ForwardBlock\Chain\PoA\Transactions
 */
interface TxFlagsInterface
{
    /** @var int Genesis/Chain Initializer Transaction */
    public const TX_FLAG_GENESIS = 0x01;
    /** @var int Account Registration Transaction */
    public const TX_FLAG_REGISTER = 0x64;
    /** @var int Account to Account transfer op */
    public const TX_FLAG_TRANSFER = 0xc8;
}
