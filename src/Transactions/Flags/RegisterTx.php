<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Base16;
use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Protocol\Exception\KeyPairException;
use ForwardBlock\Protocol\Exception\TxDecodeException;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use ForwardBlock\Protocol\Math\UInts;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;

/**
 * Class RegisterTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class RegisterTx extends AbstractPreparedTx
{
    /** @var PublicKey */
    private PublicKey $pubKey;
    /** @var string */
    private string $referrerHash160;
    /** @var array */
    private array $multiSig = [];

    /**
     * @throws TxDecodeException
     */
    public function decodeCallback(): void
    {
        if (!$this->data) {
            throw TxDecodeException::Incomplete($this, 'Registration data not present');
        }

        $dataReader = (new Binary($this->data->raw()))->read();
        $dataReader->throwUnderflowEx();

        // Get the public key
        $pubKeyBytes = $dataReader->first(33);
        try {
            $this->p->keyPair()->publicKeyFromEntropy(new Base16(bin2hex($pubKeyBytes)));
        } catch (KeyPairException $e) {
            $this->p->debugError($e);
            throw TxDecodeException::Incomplete($this, 'Registrant public key decode error');
        }

        // Get the referrer's Id
        /** @var string $referrerId */
        $referrerId = $dataReader->next(20);
        $this->referrerHash160 = $referrerId;

        // MultiSig?
        $multiSigCount = UInts::Decode_UInt1LE($dataReader->next(1));
        if ($multiSigCount > 0) {
            if ($multiSigCount > 5) {
                throw TxDecodeException::Incomplete($this, 'MultiSig cannot have more than 5 public key');
            }

            $multiSigIndex = [];
            for ($i = 0; $i < $multiSigCount; $i++) {
                $msKBytes = $dataReader->next(33);

                try {
                    $msKPub = $this->p->keyPair()->publicKeyFromEntropy(new Base16(bin2hex($msKBytes)));
                } catch (KeyPairException $e) {
                    $this->p->debugError($e);
                    throw TxDecodeException::Incomplete($this, sprintf('Register multiSig key %d decode error', $i));
                }

                $mskPubI = strtolower($msKPub->compressed()->hexits(false));
                if (in_array($mskPubI, $multiSigIndex)) {
                    throw TxDecodeException::Incomplete($this, sprintf('Repeating multiSig key at index %d', $i));
                }

                $this->multiSig[] = $msKPub;
                $multiSigIndex[] = $mskPubI;
            }
        }

        // Extra bytes?
        if ($dataReader->remaining()) {
            throw TxDecodeException::Incomplete($this, 'Data contains unnecessary additional bytes');
        }
    }

    /**
     * @return array
     */
    public function multiSig(): array
    {
        return $this->multiSig;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $partial = parent::array();
        $partial["txData"] = [];

        if (isset($this->pubKey)) {
            $partial["txData"]["publicKey"] = $this->pubKey->compressed()->hexits(true);
        }

        if (isset($this->referrerHash160)) {
            $partial["txData"]["referrer"] = "0x" . bin2hex($this->referrerHash160);
        }

        /** @var PublicKey $msPub */
        foreach ($this->multiSig as $msPub) {
            $partial["txData"]["multiSig"][] = $msPub->compressed()->hexits(true);
        }

        return $partial;
    }
}
