<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Base16;
use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Protocol\Exception\TxDecodeException;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use ForwardBlock\Protocol\Math\UInts;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;

/**
 * Class GenesisTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class GenesisTx extends AbstractPreparedTx
{
    /** @var PublicKey */
    protected PublicKey $chainMasterPubKey;
    /** @var array */
    protected array $signers = [];
    /** @var int */
    protected int $initialSupply;

    /**
     * @throws TxDecodeException
     * @throws \ForwardBlock\Protocol\Exception\KeyPairException
     */
    protected function decodeCallback(): void
    {
        if ($this->data) {
            $dataReader = (new Binary($this->data->raw()))->read();
            $dataReader->throwUnderflowEx();

            // ChainMaster Identifier
            $this->chainMasterPubKey = $this->p->keyPair()->publicKeyFromEntropy(new Base16(bin2hex($dataReader->next(33))));

            // Signers
            for ($i = 0; $i < 5; $i++) {
                $this->signers[] = $this->p->keyPair()->publicKeyFromEntropy(new Base16(bin2hex($dataReader->next(33))));
            }

            // Initial Supply
            $this->initialSupply = UInts::Decode_UInt8LE($dataReader->next(8));

            // Extra bytes?
            if ($dataReader->remaining()) {
                throw TxDecodeException::Incomplete($this, 'Data contains unnecessary additional bytes');
            }
        }
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $partial = parent::array();
        $data = [];

        if (isset($this->chainMasterPubKey)) {
            $data["chainMasterPubKey"] = $this->chainMasterPubKey;
        }

        $data["signers"] = $this->signers;
        if(isset($this->initialSupply)) {
            $data["initialSupply"] = $this->initialSupply;
        }

        $partial["txClass"] = get_called_class();
        $partial["txData"] = $data;
        return $partial;
    }
}
