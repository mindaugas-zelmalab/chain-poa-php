<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use ForwardBlock\Protocol\Exception\TxDecodeException;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;

/**
 * Class GenesisTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class GenesisTx extends AbstractPreparedTx
{
    /** @var array */
    public array $chainMasters = [];

    /**
     * @throws TxDecodeException
     */
    protected function decodeCallback(): void
    {
        if ($this->data) {
            $keys = $this->data->raw();
            $chainMasters = str_split($keys, 32);
            if (count($chainMasters) !== 5) {
                throw TxDecodeException::Incomplete($this,
                    sprintf('Genesis tx must have precisely 5 chain masters; Got %d', count($chainMasters))
                );
            }

            $this->chainMasters = $chainMasters;
        }
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $partial = parent::array();
        foreach ($this->chainMasters as $chainMaster) {
            $partial[] = bin2hex($chainMaster);
        }

        return $partial;
    }
}
