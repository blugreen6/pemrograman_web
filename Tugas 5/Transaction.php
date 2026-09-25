<?php

declare(strict_types=1);

/* Kelas Transaction merepresentasikan satu transaksi keuangan */
class Transaction
{
    public function __construct(
        private readonly int $id,
        private readonly string $type,
        private readonly float $amount
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    /* Proses transaksi terhadap saldo saat ini */
    public function process(float $currentBalance): float
    {
        return match ($this->type) {
            'deposit' => $currentBalance + $this->amount,
            'penarikan' => $this->processWithdrawal($currentBalance),
            default => throw new InvalidArgumentException('Jenis transaksi tidak dikenal: ' . $this->getType()),
        };
    }

    private function processWithdrawal(float $currentBalance): float
    {
        if ($this->amount > $currentBalance) {
            throw new RuntimeException('Saldo Anda tidak mencukupi untuk melakukan penarikan');
        }

        return $currentBalance - $this->amount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
        ];
    }
}