<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Chain\PoA\Transactions\ProtocolTxConstructor;
use ForwardBlock\Protocol\Exception\TxConstructException;
use ForwardBlock\Protocol\Math\UInts;
use ForwardBlock\Protocol\Transactions\Traits\RecipientTrait;
use ForwardBlock\Protocol\Transactions\Traits\TransferObjectsTrait;

/**
 * Class AccountUpgradeTxConstructor
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class AccountUpgradeTxConstructor extends ProtocolTxConstructor
{
    use RecipientTrait;
    use TransferObjectsTrait;

    /** @var int */
    private int $action;

    /**
     * AccountUpgradeTxConstructor constructor.
     * @param ForwardPoA $p
     * @param int $epoch
     * @param int $action
     * @throws \ForwardBlock\Protocol\Exception\TxConstructException
     */
    public function __construct(ForwardPoA $p, int $epoch, int $action)
    {
        parent::__construct($p, 1, $p->txFlags()->getWithName("account_upgrade"), $epoch);
        if (!in_array($action, [AccountUpgradeTx::ACTION_UPGRADE, AccountUpgradeTx::ACTION_DOWNGRADE])) {
            throw TxConstructException::Prop("upgrade.action", "OutOfBounds");
        }

        $this->action = $action;
    }

    /**
     * @return void
     */
    public function beforeSerialize(): void
    {
        $data = new Binary();
        $data->append(UInts::Encode_UInt1LE($this->action));
        $this->data = $data->readOnly(true);
    }
}
