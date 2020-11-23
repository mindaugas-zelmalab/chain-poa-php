<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

use Comely\DataTypes\Buffer\Binary;
use Comely\Utils\OOP\OOP;
use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Transactions\AbstractTxConstructor;
use ForwardBlock\Protocol\Transactions\AbstractTxFlag;
use ForwardBlock\Protocol\Transactions\AbstractTxReceipt;
use ForwardBlock\Protocol\Transactions\Transaction;

/**
 * Class TxFlag
 * @package ForwardBlock\Chain\PoA\Transactions
 */
class TxFlag extends AbstractTxFlag
{
    /** @var string */
    private string $createClass;

    /**
     * TxFlag constructor.
     * @param AbstractProtocolChain $p
     * @param int $id
     * @param string $name
     * @param bool $status
     */
    public function __construct(AbstractProtocolChain $p, int $id, string $name, bool $status)
    {
        parent::__construct($p, $id, $name, $status);
        $pascalCase = OOP::PascalCase($name);

        // Create TX class
        $this->createClass = sprintf('ForwardBlock\Chain\PoA\Transactions\Flags\%sTx', $pascalCase);
        if (!class_exists($this->createClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx create class');
        }
    }

    /**
     * @param array $args
     * @return AbstractTxConstructor
     */
    public function create(array $args): AbstractTxConstructor
    {
        $createClass = $this->createClass;
        return new $createClass(...$args);
    }

    /**
     * @param Transaction $tx
     * @param int $blockHeightContext
     * @return AbstractTxReceipt
     */
    public function newReceipt(Transaction $tx, int $blockHeightContext): AbstractTxReceipt
    {
        $receiptClass = sprintf(ForwardPoA::CORE_PROTOCOL_NAMESPACE . '\Txs\%sReceipt', OOP::PascalCase($this->name));
        if (!class_exists($receiptClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx receipt class');
        }

        return new $receiptClass($this->p, $tx, $blockHeightContext);
    }

    /**
     * @param Transaction $tx
     * @param Binary $bytes
     * @param int $blockHeightContext
     * @return AbstractTxReceipt
     */
    public function decodeReceipt(Transaction $tx, Binary $bytes, int $blockHeightContext): AbstractTxReceipt
    {
        $receiptClass = sprintf(ForwardPoA::CORE_PROTOCOL_NAMESPACE . '\Txs\%sReceipt', OOP::PascalCase($this->name));
        if (!class_exists($receiptClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx receipt class');
        }

        return call_user_func_array([$receiptClass, "Decode"], [$this->p, $tx, $blockHeightContext, $bytes]);
    }
}
