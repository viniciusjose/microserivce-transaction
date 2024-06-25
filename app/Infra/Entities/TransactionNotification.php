<?php

declare(strict_types=1);

namespace App\Infra\Entities;

use App\Domain\Enums\StatusEnum;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $transaction_id
 * @property string $status
 * @property string $date
 */
class TransactionNotification extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'transaction_notifications';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = [];

    protected string $keyType = 'string';

    public bool $incrementing = false;

    public bool $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id'   => 'string',
        'date' => 'datetime',
        'status' => StatusEnum::class
    ];
}
