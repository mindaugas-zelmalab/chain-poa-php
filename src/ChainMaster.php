<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA;

use Comely\DataTypes\Buffer\Base16;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Accounts\ChainAccountInterface;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use FurqanSiddiqui\BIP32\Exception\PublicKeyException;

/**
 * Class ChainMaster
 * @package ForwardBlock\Chain\PoA
 */
class ChainMaster extends PublicKey implements ChainAccountInterface
{
    /** @var string ChainMaster Account Public Key */
    public const PUBLIC_KEY = "02b62ac0d6870a7f1984de6e9997724178f33897ad0eb210a87185fce1e3bfe172";
    /** @var string ChainMaster Signatory # 1 */
    public const MULTI_SIG_KEY_1 = "02b62ac0d6870a7f1984de6e9997724178f33897ad0eb210a87185fce1e3bfe172";
    /** @var string ChainMaster Signatory # 2 */
    public const MULTI_SIG_KEY_2 = "02723a0478600bfcf861a51339d21fdd19c063e8598c85da6dfcec5dc690b1679d";
    /** @var string ChainMaster Signatory # 3 */
    public const MULTI_SIG_KEY_3 = "024096c2184bc02efdf47d475991810b3adc91c1096542f42c15b3779c24d8ac86";
    /** @var string ChainMaster Signatory # 4 */
    public const MULTI_SIG_KEY_4 = "039b9a061c2f9fe2a42cc9c9d4b45dd5a166151baa18d7494d0aad757b0595a922";
    /** @var string ChainMaster Signatory # 5 */
    public const MULTI_SIG_KEY_5 = "02b5fca74e80bd92a2a170d49fed09125f38c29635db29c1cc628a91e94bdf2549";
    /** @var int Initial Supply 100,000,000 */
    public const INITIAL_SUPPLY = 10000000000000000;

    /** @var array */
    private array $multiSigPubs = [];

    /**
     * ChainMaster constructor.
     * @param AbstractProtocolChain $p
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function __construct(AbstractProtocolChain $p)
    {
        parent::__construct($p, null, $p->secp256k1(), new Base16(static::PUBLIC_KEY), true);
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return $this;
    }

    /**
     * @return int
     */
    public function initialSupply(): int
    {
        return self::INITIAL_SUPPLY;
    }

    /**
     * @return bool
     */
    public function canForgeBlocks(): bool
    {
        return true;
    }

    /**
     * @return array
     * @throws PublicKeyException
     */
    public function getAllPublicKeys(): array
    {
        $pubKeys = [];
        for ($i = 1; $i <= 5; $i++) {
            $pubKeys[] = $this->getSignatory($i);
        }

        return $pubKeys;
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig1(): PublicKey
    {
        return $this->getSignatory(1);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig2(): PublicKey
    {
        return $this->getSignatory(2);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig3(): PublicKey
    {
        return $this->getSignatory(3);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig4(): PublicKey
    {
        return $this->getSignatory(4);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig5(): PublicKey
    {
        return $this->getSignatory(5);
    }

    /**
     * @param int $num
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    private function getSignatory(int $num): PublicKey
    {
        if (!isset($this->multiSigPubs[$num])) {
            $this->multiSigPubs[$num] = new PublicKey($this->protocol, null, $this->protocol->secp256k1(), new Base16(constant("static::MULTI_SIG_KEY_" . $num)));
        }

        return $this->multiSigPubs[$num];
    }
}
