<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

use Comely\DataTypes\Buffer\Binary;
use Comely\Utils\OOP\OOP;
use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;
use ForwardBlock\Protocol\Transactions\AbstractTxFlag;
use ForwardBlock\Protocol\Transactions\AbstractTxReceipt;

/**
 * Class TxFlag
 * @package ForwardBlock\Chain\PoA\Transactions
 */
class TxFlag extends AbstractTxFlag
{
    /** @var string */
    private string $createClass;
    /** @var string */
    private string $decodeClass;

    /**
     * TxFlag constructor.
     * @param AbstractProtocolChain $p
     * @param int $id
     * @param string $name
     */
    public function __construct(AbstractProtocolChain $p, int $id, string $name)
    {
        parent::__construct($p, $id, $name);
        $pascalCase = OOP::PascalCase($name);

        // Create TX class
        $this->createClass = sprintf('ForwardBlock\Chain\PoA\Transactions\Flags\%sTxConstructor', $pascalCase);
        if (!class_exists($this->createClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx create class');
        }
        $this->decodeClass = sprintf('ForwardBlock\Chain\PoA\Transactions\Flags\%sTx', $pascalCase);
        if (!class_exists($this->decodeClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx decode class');
        }
    }

    /**
     * @param array $args
     * @return ProtocolTxConstructor
     */
    public function create(array $args): ProtocolTxConstructor
    {
        $createClass = $this->createClass;
        return new $createClass(...$args);
    }

    /**
     * @param Binary $encoded
     * @return AbstractPreparedTx
     */
    public function decode(Binary $encoded): AbstractPreparedTx
    {
        $decodeClass = $this->decodeClass;
        return call_user_func_array([$decodeClass, "Decode"], [$this->p, $encoded]);
    }

    /**
     * @param AbstractPreparedTx $tx
     * @param int $blockHeightContext
     * @return AbstractTxReceipt
     */
    public function newReceipt(AbstractPreparedTx $tx, int $blockHeightContext): AbstractTxReceipt
    {
        $receiptClass = sprintf(ForwardPoA::CORE_PROTOCOL_NAMESPACE . '\Receipts\%sReceipt', OOP::PascalCase($this->name));
        if (!class_exists($receiptClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx receipt class');
        }

        return new $receiptClass($this->p, $tx, $blockHeightContext);
    }

    /**
     * @param AbstractPreparedTx $tx
     * @param Binary $bytes
     * @param int $blockHeightContext
     * @return AbstractTxReceipt
     */
    public function decodeReceipt(AbstractPreparedTx $tx, Binary $bytes, int $blockHeightContext): AbstractTxReceipt
    {
        $receiptClass = sprintf(ForwardPoA::CORE_PROTOCOL_NAMESPACE . '\Receipts\%sReceipt', OOP::PascalCase($this->name));
        if (!class_exists($receiptClass)) {
            throw new \UnexpectedValueException('Cannot find "%s" tx receipt class');
        }

        return call_user_func_array([$receiptClass, "Decode"], [$this->p, $tx, $blockHeightContext, $bytes]);
    }
}
