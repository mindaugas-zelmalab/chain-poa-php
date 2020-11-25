<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

use ForwardBlock\Chain\PoA\Transactions\Flags\RegisterTx;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\KeyPair\PublicKey;

/**
 * Class TxFactory
 * @package ForwardBlock\Chain\PoA\Transactions
 */
class TxFactory implements TxFlagsInterface
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
        $tx = $this->createTx(self::TX_FLAG_REGISTER, [$this->p, $publicKey]);
        return $tx;
    }

    /**
     * @param int $flag
     * @param array $args
     * @return ProtocolTxConstructor
     */
    private function createTx(int $flag, array $args): ProtocolTxConstructor
    {
        try {
            /** @var ProtocolTxConstructor $pTx */
            $pTx = $this->p->txFlags()->get($flag)->create($args);
        } catch (\Exception $e) {
            throw new \UnexpectedValueException(sprintf('[%s] %s', get_class($e), $e->getMessage()));
        }

        return $pTx;
    }
}
