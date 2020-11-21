<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Exception\TxConstructException;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use ForwardBlock\Protocol\Transactions\AbstractTxConstructor;

/**
 * Class GenesisTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class GenesisTx extends AbstractTxConstructor
{
    /** @var PublicKey */
    private PublicKey $chainMaster;
    /** @var array */
    private array $multiSig = [];

    /**
     * GenesisTx constructor.
     * @param AbstractProtocolChain $protocol
     * @param PublicKey $chainMaster
     * @param array $multiSigKeys
     * @throws TxConstructException
     */
    public function __construct(AbstractProtocolChain $protocol, PublicKey $chainMaster, array $multiSigKeys)
    {
        parent::__construct($protocol, 1, $protocol->txFlags()->getWithName("genesis"));
        $this->chainMaster = $chainMaster;

        if (count($multiSigKeys) !== 5) {
            throw new TxConstructException('5 multiSig keys are required for genesis transaction');
        }

        $multiSigCount = 0;
        $includedKeys = [];
        foreach ($multiSigKeys as $multiSigKey) {
            $multiSigCount++;
            if (!$multiSigKey instanceof PublicKey) {
                throw new TxConstructException(sprintf('Invalid chain master multiSig key # %d', $multiSigCount));
            }

            if (in_array(strtolower($multiSigKey->compressed()->hexits(false)), $includedKeys)) {
                throw new TxConstructException(sprintf('Duplicate chain master multiSig key # %d', $multiSigCount));
            }

            $this->multiSig[] = $multiSigKey;
            $includedKeys[] = strtolower($multiSigKey->compressed()->hexits(false));
        }

        unset($includedKeys);
    }

    protected function beforeSerialize(): void
    {

    }
}
