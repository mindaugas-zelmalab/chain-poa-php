<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

/**
 * Interface LedgerEntryFlags
 * @package ForwardBlock\Chain\PoA\Transactions
 */
interface LedgerEntryFlags
{
    /** @var int Initial supply as per genesis */
    public const TX_RECEIPT_G_INIT_SUPPLY = 0x01;
    /** @var int Chain-master mint op */
    public const TX_RECEIPT_MINT = 0x02;
    /** @var int Fee deduction per byte */
    public const TX_RECEIPT_DEBIT_FEE = 0x64;
    /** @var int Transfer's debit from payer */
    public const TX_RECEIPT_TRANSFER_DEBIT = 201;
    /** @var int Transfer's credit to payee */
    public const TX_RECEIPT_TRANSFER_CREDIT = 202;
    /** @var int Transfer's fee */
    public const TX_RECEIPT_TRANSFER_FEE = 203;
    /** @var int Registration charges */
    public const TX_RECEIPT_REGISTER = 301;
    /** @var int Referrer commission */
    public const TX_RECEIPT_REGISTER_REF_COM = 302;
}
