<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Infra\Entities;

use App\Infra\Enums\UserType;
use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $identify
 * @property string $email
 * @property string $password
 * @property UserType $user_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $wallet_id
 */
class User extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

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
        'id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_type' => UserType::class,
    ];
}
