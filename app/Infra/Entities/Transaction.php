<?php

declare(strict_types=1);

namespace App\Infra\Entities;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $wallet_payer_id
 * @property string $wallet_payee_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Transaction extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'transactions';

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
        'id'              => 'string',
        'wallet_payer_id' => 'string',
        'wallet_payee_id' => 'string',
        'value'           => 'float',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime'
    ];
}
