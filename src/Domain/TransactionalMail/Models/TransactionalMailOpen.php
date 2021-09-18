<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TransactionalMailOpen extends Model
{
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_transactional_mail_opens';

    protected $guarded = [];

    function __construct()
    {
        $this->setConnection(config('mailcoach.default_db_table_connection'));
    }

    public function send(): BelongsTo
    {
        return $this->belongsTo($this->getSendClass(), 'send_id');
    }
}
