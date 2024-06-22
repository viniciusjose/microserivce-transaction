<?php

declare(strict_types=1);

namespace App\Infra\Entities;

use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $balance
 * @property string $last_balance
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Wallet extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = [];

    protected string $keyType = 'string';

    public bool $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id'           => 'string',
        'user_id'      => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'balance'      => 'float',
        'last_balance' => 'float',
    ];
}
