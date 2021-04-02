<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use ForwardBlock\Protocol\Exception\TxDecodeException;
use ForwardBlock\Protocol\Math\UInts;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;

/**
 * Class AccountUpgradeTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class AccountUpgradeTx extends AbstractPreparedTx
{
    public const ACTION_UPGRADE = 0x00;
    public const ACTION_DOWNGRADE = 0x01;

    /** @var int */
    private int $action;

    /**
     * @throws TxDecodeException
     */
    public function decodeCallback(): void
    {
        if (!$this->data) {
            throw TxDecodeException::Incomplete($this, 'Transaction data not present');
        }

        $action = $this->data->raw();
        if (strlen($action) !== 1) {
            throw TxDecodeException::Incomplete($this, 'Invalid accountUpgrade tx data');
        }

        $this->action = UInts::Decode_UInt1LE($action);
    }

    /**
     * @return int
     */
    public function action(): int
    {
        return $this->action;
    }
}
