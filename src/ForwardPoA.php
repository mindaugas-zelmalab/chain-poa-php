<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA;

use ForwardBlock\Chain\PoA\Transactions\TxFactory;
use ForwardBlock\Chain\PoA\Transactions\TxFlag;
use ForwardBlock\Chain\PoA\Transactions\TxFlagsInterface;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Transactions\TxFlags;

/**
 * Class ForwardPoA
 * @package ForwardBlock\Chain\PoA
 */
class ForwardPoA extends AbstractProtocolChain implements TxFlagsInterface
{
    /** @var TxFactory */
    private TxFactory $txF;

    /**
     * ForwardPoA constructor.
     * @param array $config
     * @throws \ForwardBlock\Protocol\Exception\ProtocolConfigException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->txF = new TxFactory($this);
    }

    /**
     * @param TxFlags $flags
     */
    protected function registerTxFlags(TxFlags $flags): void
    {
        // TX: REGISTER
        $flags->append($this->createTxFlag(self::TX_FLAG_REGISTER, "REGISTER", true));
    }

    /**
     * @return TxFactory
     */
    public function txFactory(): TxFactory
    {
        return $this->txF;
    }

    /**
     * @param int $dec
     * @param string $name
     * @param bool $enabled
     * @return TxFlag
     */
    private function createTxFlag(int $dec, string $name, bool $enabled): TxFlag
    {
        return new TxFlag($this, $dec, $name, $enabled);
    }
}
