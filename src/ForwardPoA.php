<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA;

use ForwardBlock\Chain\PoA\Transactions\LedgerEntryFlags;
use ForwardBlock\Chain\PoA\Transactions\TxFactory;
use ForwardBlock\Chain\PoA\Transactions\TxFlag;
use ForwardBlock\Chain\PoA\Transactions\TxFlagsInterface;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\ProtocolConstants;
use ForwardBlock\Protocol\Transactions\AbstractTxFlag;
use ForwardBlock\Protocol\Transactions\TxFlags;

/**
 * Class ForwardPoA
 * @package ForwardBlock\Chain\PoA
 */
class ForwardPoA extends AbstractProtocolChain implements TxFlagsInterface, LedgerEntryFlags
{
    /** @var string */
    public const CORE_PROTOCOL_NAMESPACE = 'ForwardBlock\Blockchain\Shared\Protocol';

    /** @var TxFactory */
    private TxFactory $txF;
    /** @var ChainMaster|null */
    private ?ChainMaster $chainMaster = null;

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
     * @return ChainMaster
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function chainMaster(): ChainMaster
    {
        if (!$this->chainMaster) {
            $this->chainMaster = new ChainMaster($this);
        }

        return $this->chainMaster;
    }

    /**
     * @param TxFlags $flags
     */
    protected function registerTxFlags(TxFlags $flags): void
    {
        $ledgerFlags = $flags->ledgerFlags();
        $ledgerFlags->append(LedgerEntryFlags::TX_RECEIPT_G_INIT_SUPPLY, true);
        $ledgerFlags->append(LedgerEntryFlags::TX_RECEIPT_MINT, true);
        $ledgerFlags->append(LedgerEntryFlags::TX_RECEIPT_DEBIT_FEE, false, true);

        // TX: GENESIS
        $flags->append($this->createTxFlag(ProtocolConstants::GENESIS_TX_FLAG, "GENESIS"));

        // TX: REGISTER
        $flags->append($this->createTxFlag(self::TX_FLAG_REGISTER, "REGISTER"));
    }

    /**
     * @param AbstractTxFlag $f
     * @param int $blockHeightContext
     * @return bool
     */
    public function isEnabledTxFlag(AbstractTxFlag $f, int $blockHeightContext): bool
    {
        // Check tx flag in block height's context
        return true;
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
     * @return TxFlag
     */
    private function createTxFlag(int $dec, string $name): TxFlag
    {
        return new TxFlag($this, $dec, $name);
    }
}
