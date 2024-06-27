<?php

declare(strict_types=1);

namespace App\Infra\Entities;

use Carbon\Carbon;
use Decimal\Decimal;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $user_id
 * @property string $balance
 * @property string $last_balance
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
        'updated_at'   => 'datetime'
    ];
}
