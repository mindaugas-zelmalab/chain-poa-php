<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Protocol\Exception\TxDecodeException;
use ForwardBlock\Protocol\Math\UInts;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;
use ForwardBlock\Protocol\Validator;

/**
 * Class AssetCreateTx
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class AssetCreateTx extends AbstractPreparedTx
{
    /** @var string */
    private string $id;
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
     * @throws TxDecodeException
     */
    public function decodeCallback(): void
    {
        if (!$this->data) {
            throw TxDecodeException::Incomplete($this, 'Asset create data not present');
        }

        $dataReader = (new Binary($this->data->raw()))->read();
        $dataReader->throwUnderflowEx();

        // Asset unique identifier
        $assetId = $dataReader->next(8);
        if (is_string($assetId)) {
            $assetId = ltrim($assetId, "\0");
        }

        if (!is_string($assetId) || !Validator::isValidAssetId($assetId)) {
            throw TxDecodeException::Incomplete($this, 'Invalid asset identifier');
        }

        $this->id = $assetId;

        // Asset ticker
        $assetTicker = $dataReader->next(6);
        if (is_string($assetTicker)) {
            $assetTicker = ltrim($assetTicker, "\0");
        }

        if (!is_string($assetTicker) || !Validator::isValidAssetTicker($assetId)) {
            throw TxDecodeException::Incomplete($this, 'Invalid asset ticker');
        }

        $this->ticker = $assetTicker;

        // Asset name
        $assetNameLen = UInts::Decode_UInt1LE($dataReader->next(1));
        if ($assetNameLen < 1 || $assetNameLen > 32) {
            throw TxDecodeException::Incomplete($this, 'Invalid asset name len');
        }

        $assetName = $dataReader->next($assetNameLen);
        if (!is_string($assetName) || !Validator::isValidAssetName($assetName)) {
            throw TxDecodeException::Incomplete($this, 'Invalid asset name');
        }

        $this->name = $assetName;

        // Scale
        $assetScale = UInts::Decode_UInt1LE($dataReader->next(1));
        if ($assetScale < 0 || $assetScale > 18) {
            throw TxDecodeException::Incomplete($this, 'Invalid asset scale value');
        }

        $this->scale = $assetScale;

        // Is Fixed Supply?
        $isFixedSupply = $dataReader->next(1);
        if (!in_array($isFixedSupply, ["\0", "\1"])) {
            throw TxDecodeException::Incomplete($this, 'Invalid fixed/liquid supply indicator');
        }

        $this->isFixedSupply = $isFixedSupply === "\1";

        // Mint Amount
        $this->mintAmount = UInts::Decode_UInt8LE($dataReader->next(8));

        // Extra bytes?
        if ($dataReader->remaining()) {
            throw TxDecodeException::Incomplete($this, 'Data contains unnecessary additional bytes');
        }
    }

    /**
     * @return string
     */
    public function assetId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function assetName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function assetTicker(): string
    {
        return $this->ticker;
    }

    /**
     * @return int
     */
    public function assetScale(): int
    {
        return $this->scale;
    }

    /**
     * @return bool
     */
    public function assetIsFixedSupply(): bool
    {
        return $this->isFixedSupply;
    }

    /**
     * @return int
     */
    public function assetMintAmount(): int
    {
        return $this->mintAmount;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $partial = parent::array();
        $partial["txData"] = [];

        foreach (["id", "ticker", "name", "scale", "isFixedSupply", "mintAmount"] as $prop) {
            if (isset($this->$prop)) {
                $partial["txData"][$prop] = $this->$prop;
            }
        }

        return $partial;
    }
}
