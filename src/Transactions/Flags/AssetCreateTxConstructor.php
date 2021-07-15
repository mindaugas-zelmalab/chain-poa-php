<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Chain\PoA\Transactions\ProtocolTxConstructor;
use ForwardBlock\Protocol\Exception\TxConstructException;
use ForwardBlock\Protocol\Exception\TxEncodeException;
use ForwardBlock\Protocol\Math\UInts;
use ForwardBlock\Protocol\Validator;

/**
 * Class AssetCreateTxConstructor
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class AssetCreateTxConstructor extends ProtocolTxConstructor
{
    /** @var string|null */
    private ?string $id = null;
    /** @var string */
    private string $ticker;
    /** @var string */
    private string $name;
    /** @var int */
    private int $scale;
    /** @var bool */
    private bool $isFixedSupply;
    /** @var int */
    private int $mintAmount;

    /**
     * @param string $name
     * @param string $ticker
     * @param int $scale
     * @return $this
     * @throws TxConstructException
     */
    public function asset(string $name, string $ticker, int $scale): self
    {
        if (!Validator::isValidAssetTicker($ticker)) {
            throw TxConstructException::Prop("asset.ticker", "Invalid asset ticker");
        }

        if (!Validator::isValidAssetName($name)) {
            throw TxConstructException::Prop("asset.name", "Invalid asset name");
        }

        if ($scale < 0 || $scale > 18) {
            throw TxConstructException::Prop("asset.scale", "Invalid asset scale");
        }

        $this->name = $name;
        $this->ticker = $ticker;
        $this->scale = $scale;
        return $this;
    }

    /**
     * @param int $totalSupply
     * @return $this
     * @throws TxConstructException
     */
    public function fixedSupply(int $totalSupply): self
    {
        $this->setMintAmount($totalSupply);
        $this->isFixedSupply = true;
        return $this;
    }

    /**
     * @param int $initialSupply
     * @return $this
     * @throws TxConstructException
     */
    public function liquidSupply(int $initialSupply): self
    {
        $this->setMintAmount($initialSupply);
        $this->isFixedSupply = false;
        return $this;
    }

    /**
     * @param int $mintAmount
     * @throws TxConstructException
     */
    private function setMintAmount(int $mintAmount): void
    {
        if ($mintAmount < 0 || $mintAmount > UInts::MAX) {
            throw TxConstructException::Prop("asset.id", "Invalid initial/fixed mint supply");
        }

        $this->mintAmount = $mintAmount;
    }

    /**
     * @param \Closure $callback
     * @return $this
     * @throws TxConstructException
     * @throws TxEncodeException
     */
    public function generateUniqueAssetId(\Closure $callback): self
    {
        if (!isset($this->id, $this->name, $this->ticker, $this->scale)) {
            throw new TxEncodeException('Asset information not set');
        }

        while (true) {
            $rand1 = mt_rand(97, 122);
            $rand2 = mt_rand(97, 122);
            $rand3 = mt_rand(1, 9);
            $assetId = sprintf('%s-%s%s%d', substr($this->ticker, 0, 4), chr($rand1), chr($rand2), $rand3);

            // Check if ID is unique (having no duplicates in DB) using a callback method
            $isUnique = call_user_func_array($callback, [$assetId]);
            if ($isUnique) {
                $this->useUniqueId($assetId);
                return $this;
            }
        }
    }

    /**
     * @param string $id
     * @return $this
     * @throws TxConstructException
     */
    public function useUniqueId(string $id): self
    {
        if (!Validator::isValidAssetId($id)) {
            throw TxConstructException::Prop("asset.id", "Invalid unique asset identifier");
        }

        $this->id = $id;
        return $this;
    }

    /**
     * @throws TxEncodeException
     */
    protected function beforeSerialize(): void
    {
        $data = new Binary();

        if (!isset($this->id, $this->name, $this->ticker, $this->scale)) {
            throw new TxEncodeException('Asset information not set');
        }

        $data->append(str_pad($this->id, 8, "\0", STR_PAD_LEFT));
        $data->append(str_pad($this->ticker, 6, "\0", STR_PAD_LEFT));
        $data->append(UInts::Encode_UInt1LE(strlen($this->name)));
        $data->append($this->name);
        $data->append(UInts::Encode_UInt1LE($this->scale));

        if (!isset($this->isFixedSupply, $this->mintAmount)) {
            throw new TxEncodeException('Asset supply information not set');
        }

        $data->append($this->isFixedSupply ? "\1" : "\0");
        $data->append(UInts::Encode_UInt8LE($this->mintAmount));

        $this->data = $data->readOnly(true);
    }
}
