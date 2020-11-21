<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Chain\PoA\Transactions\Flags\RegisterTx;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\KeyPair\PublicKey;

/**
 * Class TxFactory
 * @package ForwardBlock\Chain\PoA\Transactions
 */
class TxFactory
{
    /** @var AbstractProtocolChain */
    private AbstractProtocolChain $p;

    /**
     * TxFactory constructor.
     * @param AbstractProtocolChain $p
     */
    public function __construct(AbstractProtocolChain $p)
    {
        $this->p = $p;
    }

    /**
     * @param PublicKey $publicKey
     * @return RegisterTx
     */
    public function registerTx(PublicKey $publicKey): RegisterTx
    {
        /** @var RegisterTx $tx */
        $tx = $this->p->txFlags()->get(ForwardPoA::TX_FLAG_REGISTER)->create([$this->p, $publicKey]);
        return $tx;
    }
}
