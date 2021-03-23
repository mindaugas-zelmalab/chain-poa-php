<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Chain\PoA\Transactions\ProtocolTxConstructor;
use ForwardBlock\Protocol\Exception\TxConstructException;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use ForwardBlock\Protocol\Math\UInts;

/**
 * Class GenesisTxConstructor
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class GenesisTxConstructor extends ProtocolTxConstructor
{
    /**
     * GenesisTx constructor.
     * @param ForwardPoA $p
     * @param int $epoch
     * @throws TxConstructException
     */
    public function __construct(ForwardPoA $p, int $epoch)
    {
        parent::__construct($p, 1, $p->txFlags()->getWithName("genesis"), $epoch);
    }

    /**
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    protected function beforeSerialize(): void
    {
        $data = new Binary();

        $chainMaster = $this->p->chainMaster();

        // Append Chain Master's Public Key
        $data->append($chainMaster->compressed()->binary());

        // Append all 5 chain master multiSig keys
        /** @var PublicKey $publicKey */
        foreach ($chainMaster->getAllPublicKeys() as $publicKey) {
            $data->append($publicKey->compressed()->binary());
        }

        // Append initial supply
        $data->append(UInts::Encode_UInt8LE($chainMaster->initialSupply()));

        $this->data = $data->readOnly(true);
    }
}
