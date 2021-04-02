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
    public const PROTOCOL_VERSION = "0.1.1";

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
        $ledgerFlags->append(LedgerEntryFlags::TX_RECEIPT_DEBIT_FEE, false);

        // TX: GENESIS
        $flags->append($this->createTxFlag(ProtocolConstants::GENESIS_TX_FLAG, "GENESIS"));

        // TX: REGISTER
        $flags->append($this->createTxFlag(self::TX_FLAG_REGISTER, "REGISTER"));
        $ledgerFlags->append(LedgerEntryFlags::TX_RECEIPT_REGISTER, false);
        $ledgerFlags->append(LedgerEntryFlags::TX_RECEIPT_REGISTER_REF_COM, true);

        // TX: ACCOUNT_UPGRADE
        $flags->append($this->createTxFlag(self::TX_FLAG_ACCOUNT_UPGRADE, "ACCOUNT_UPGRADE"));
    }

    /**
     * @param AbstractTxFlag $f
     * @param int $blockHeightContext
     * @return bool
     */
    public function isEnabledTxFlag(AbstractTxFlag $f, int $blockHeightContext): bool
    {
        switch ($f->id()) {
            case self::GENESIS_TX_FLAG: // GenesisTx available only in block height 0 context
                return $blockHeightContext === 0;
        }

        return true;
    }

    /**
     * @param int $blockHeightContext
     * @return int
     */
    public function getForkId(int $blockHeightContext): int
    {
        switch ($blockHeightContext) {
            default:
                return $this->config->forkId;
        }
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
