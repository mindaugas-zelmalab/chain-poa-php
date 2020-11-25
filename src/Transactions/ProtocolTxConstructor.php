<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Transactions\AbstractTxConstructor;
use ForwardBlock\Protocol\Transactions\AbstractTxFlag;

/**
 * Class ProtocolTxConstructor
 * @package ForwardBlock\Chain\PoA\Transactions
 */
abstract class ProtocolTxConstructor extends AbstractTxConstructor
{
    /** @var AbstractProtocolChain|ForwardPoA $p */
    protected AbstractProtocolChain $p;

    /**
     * ProtocolTxConstructor constructor.
     * @param ForwardPoA $p
     * @param int $ver
     * @param AbstractTxFlag $flag
     * @param int $epoch
     * @throws \ForwardBlock\Protocol\Exception\TxConstructException
     */
    public function __construct(ForwardPoA $p, int $ver, AbstractTxFlag $flag, int $epoch)
    {
        parent::__construct($p, $ver, $flag, $epoch);
    }
}
